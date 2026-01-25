<?php

namespace App\implementations;

use App\Interfaces\icustomerhistoricaldataInterface;
use App\Interfaces\icustomerInterface;
use App\Interfaces\igeneralutilsInterface;
use App\Interfaces\iuserInterface;
use App\Models\Customer;
use App\Models\Customerapplication;
use App\Models\Customercdpimports;
use App\Models\Customerhistoricaldata;
use App\Models\Customerhistoricaldatadocument;
use App\Models\Customerhistoricaldataprofession;
use App\Models\Customerprofession;
use App\Models\Customerprofessiondocument;
use App\Models\Customerregistration;
use App\Models\Customeruser;
use App\Models\Mycdp;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class _customerhistoricaldataRepository implements icustomerhistoricaldataInterface
{
    protected $customerhistoricaldata;

    protected $customerrepo;

    protected $generalutils;

    protected $userrepo;

    public function __construct(
        Customerhistoricaldata $customerhistoricaldata,
        icustomerInterface $customerrepo,
        igeneralutilsInterface $generalutils,
        iuserInterface $userrepo
    ) {
        $this->customerhistoricaldata = $customerhistoricaldata;
        $this->customerrepo = $customerrepo;
        $this->generalutils = $generalutils;
        $this->userrepo = $userrepo;
    }

    public function getAll($status = null)
    {
        return $this->customerhistoricaldata
            ->with('user', 'nationality', 'professions.profession', 'professions.registertype', 'professions.tire', 'professions.documents')
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function get($id)
    {
        return $this->customerhistoricaldata
            ->with('user', 'nationality', 'professions.profession', 'professions.registertype', 'professions.tire', 'professions.documents')
            ->find($id);
    }

    public function approve($id)
    {
        try {
            $historicalData = $this->customerhistoricaldata->with('user', 'professions.profession', 'professions.registertype', 'professions.tire', 'professions.documents')->find($id);

            if (! $historicalData) {
                return ['status' => 'error', 'message' => 'Historical data not found'];
            }

            if ($historicalData->status != 'PENDING') {
                return ['status' => 'error', 'message' => 'This submission has already been processed'];
            }

            // Get the last registration number from historical professions
            // Sort by last_renewal_year (desc) or registrationyear (desc) to get the most recent
            $lastRegistrationNumber = null;
            if ($historicalData->professions->count() > 0) {
                $sortedProfessions = $historicalData->professions->sortByDesc(function ($prof) {
                    return $prof->last_renewal_year ?? $prof->registrationyear ?? 0;
                });
                $lastProfession = $sortedProfessions->first();
                $lastRegistrationNumber = $lastProfession->registrationnumber;
            }

            // Check if customer already exists
            $customer = Customer::where('identificationnumber', $historicalData->identificationnumber)->first();

            if (! $customer) {
                // Use the last registration number as customer regnumber, or generate new one if not available
                $customerRegNumber = $lastRegistrationNumber ?? $this->generalutils->generateregistrationnumber()['data'];

                // Create customer
                $customer = Customer::create([
                    'uuid' => Str::uuid()->toString(),
                    'name' => $historicalData->name,
                    'surname' => $historicalData->surname,
                    'gender' => $historicalData->gender,
                    'identificationnumber' => $historicalData->identificationnumber,
                    'identificationtype' => $historicalData->identificationtype,
                    'dob' => $historicalData->dob,
                    'nationality_id' => $historicalData->nationality_id,
                    'address' => $historicalData->address,
                    'place_of_birth' => $historicalData->placeofbirth,
                    'phone' => $historicalData->phone,
                    'email' => $historicalData->user->email,
                    'regnumber' => $customerRegNumber,
                    'profile_complete' => true,
                    'first_login_completed' => true,
                ]);
            }

            // Create customeruser link if it doesn't exist
            if (! $customer->customeruser) {
                Customeruser::create([
                    'customer_id' => $customer->id,
                    'user_id' => $historicalData->user_id,
                ]);
            }

            // Process each profession
            foreach ($historicalData->professions as $historicalProfession) {
                // Create customerprofession
                // Using defaults: customertype_id = 1 (Practitioner), employmentstatus_id = 1, employmentlocation_id = 1
                // These should ideally be configurable or asked during approval
                $customerprofession = Customerprofession::create([
                    'customer_id' => $customer->id,
                    'profession_id' => $historicalProfession->profession_id,
                    'tire_id' => $historicalProfession->tire_id,
                    'customertype_id' => 1, // Practitioner - should be configurable
                    'employmentstatus_id' => 1, // Default - should be configurable
                    'employmentlocation_id' => 1, // Default - should be configurable
                    'registertype_id' => $historicalProfession->registertype_id,
                    'registrationnumber' => $historicalProfession->registrationnumber, // Add registration number from historical profession
                    'uuid' => Str::uuid()->toString(),
                    'employmentsector' => 'PRIVATE', // Default
                    'status' => 'APPROVED',
                    'year' => $historicalProfession->registrationyear ?? date('Y'),
                    'created_by' => Auth::user()->id,
                ]);

                // Copy documents to customerprofession documents
                foreach ($historicalProfession->documents as $doc) {
                    Customerprofessiondocument::create([
                        'customerprofession_id' => $customerprofession->id,
                        'document_id' => 1, // Default document type - should be configurable
                        'file' => $doc->file,
                        'status' => 'VERIFIED',
                        'verifiedby' => Auth::user()->id,
                    ]);
                }

                // Create customerregistration with registration number from historical profession
                Customerregistration::create([
                    'customer_id' => $customer->id,
                    'customerprofession_id' => $customerprofession->id,
                    'status' => 'APPROVED',
                    'certificatenumber' => $historicalProfession->registrationnumber, // Use registration number from historical profession
                    'certificateexpirydate' => $historicalProfession->last_renewal_expire_date,
                    'year' => $historicalProfession->registrationyear ?? $historicalProfession->last_renewal_year ?? date('Y'),
                    'registrationdate' => $historicalProfession->registrationyear ? $historicalProfession->registrationyear.'-01-01' : ($historicalProfession->last_renewal_year ? $historicalProfession->last_renewal_year.'-01-01' : now()),
                ]);

                // Create customerapplication if last renewal year is provided
                if ($historicalProfession->last_renewal_year) {
                    Customerapplication::create([
                        'customer_id' => $customer->id,
                        'customerprofession_id' => $customerprofession->id,
                        'registertype_id' => $historicalProfession->registertype_id,
                        'status' => 'APPROVED',
                        'certificate_number' => $historicalProfession->practisingcertificatenumber,
                        'certificate_expiry_date' => $historicalProfession->last_renewal_expire_date,
                        'year' => $historicalProfession->last_renewal_year,
                        'registration_date' => $historicalProfession->last_renewal_year ? $historicalProfession->last_renewal_year.'-01-01' : now(),
                        'approvedby' => Auth::user()->id,
                    ]);
                }

                // Create Mycdp entry for last renewal year CDP points if provided
                if ($historicalProfession->last_renewal_year && $historicalProfession->last_renewal_year_cdp_points) {
                    Mycdp::create([
                        'customerprofession_id' => $customerprofession->id,
                        'title' => 'Historical CDP Points - Last Renewal',
                        'year' => $historicalProfession->last_renewal_year,
                        'description' => 'CDP points from historical data submission for renewal year '.$historicalProfession->last_renewal_year,
                        'type' => 'PHYSICAL',
                        'points' => (int) $historicalProfession->last_renewal_year_cdp_points,
                        'duration' => null,
                        'durationunit' => 'HOURS', // Required field, using default
                        'user_id' => $historicalData->user_id,
                        'status' => 'PROCESSED', // Already approved, so mark as processed
                        'comment' => 'Imported from historical data approval',
                        'assessed_by' => Auth::user()->id,
                        'assessed_at' => now(),
                    ]);
                }

                // Create CDP points entry for last renewal year if provided (for customercdpimports table)
                if ($historicalProfession->last_renewal_year && $customer->regnumber) {
                    Customercdpimports::create([
                        'regnumber' => $customer->regnumber,
                        'points' => $historicalProfession->last_renewal_year_cdp_points ?? '0',
                        'year' => $historicalProfession->last_renewal_year,
                        'processed' => 'N',
                    ]);
                }

                // Create CDP points entry for registration year
                if ($historicalProfession->registrationyear && $customer->regnumber) {
                    // Only create if it's different from last_renewal_year
                    if (! $historicalProfession->last_renewal_year || $historicalProfession->registrationyear != $historicalProfession->last_renewal_year) {
                        Customercdpimports::create([
                            'regnumber' => $customer->regnumber,
                            'points' => '0', // Default points, can be updated later by admin
                            'year' => $historicalProfession->registrationyear,
                            'processed' => 'N',
                        ]);
                    }
                }
            }

            // Update historical data status
            $historicalData->update([
                'status' => 'APPROVED',
                'approvedby' => Auth::user()->id,
            ]);

            return ['status' => 'success', 'message' => 'Historical data approved and customer records created successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Failed to approve: '.$e->getMessage()];
        }
    }

    public function reject($id, $reason)
    {
        try {
            $historicalData = $this->customerhistoricaldata->find($id);

            if (! $historicalData) {
                return ['status' => 'error', 'message' => 'Historical data not found'];
            }

            if ($historicalData->status != 'PENDING') {
                return ['status' => 'error', 'message' => 'This submission has already been processed'];
            }

            $historicalData->update([
                'status' => 'REJECTED',
                'rejection_reason' => $reason,
            ]);

            return ['status' => 'success', 'message' => 'Historical data rejected successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Failed to reject: '.$e->getMessage()];
        }
    }

    public function create(array $data)
    {
        try {
            // Find or create user
            $user = User::where('email', $data['email'])->first();

            if (! $user) {
                // Create user with default password
                $password = Str::random(12);
                $userResponse = $this->userrepo->create([
                    'name' => $data['name'],
                    'surname' => $data['surname'],
                    'email' => $data['email'],
                    'phone' => $data['phone'] ?? '',
                    'password' => $password,
                    'accounttype_id' => $data['accounttype_id'] ?? 2, // Default to customer/practitioner
                ]);

                if ($userResponse['status'] == 'error') {
                    return ['status' => 'error', 'message' => 'Failed to create user: '.$userResponse['message']];
                }

                $user = $userResponse['data'];
            }

            // Create historical data record
            $historicalData = Customerhistoricaldata::create([
                'user_id' => $user->id,
                'created_by' => Auth::user()->id, // Track which admin created this record
                'name' => $data['name'],
                'surname' => $data['surname'],
                'gender' => $data['gender'],
                'identificationnumber' => $data['identificationnumber'],
                'dob' => $data['dob'],
                'identificationtype' => $data['identificationtype'],
                'nationality_id' => $data['nationality_id'],
                'address' => $data['address'],
                'placeofbirth' => $data['placeofbirth'],
                'phone' => $data['phone'],
                'status' => 'PENDING',
            ]);

            // Create profession records
            if (isset($data['professions']) && is_array($data['professions'])) {
                foreach ($data['professions'] as $professionData) {
                    $lastRenewalExpireDate = $professionData['last_renewal_expire_date'] ?? null;
                    if (empty($lastRenewalExpireDate) && ! empty($professionData['last_renewal_year'])) {
                        $lastRenewalExpireDate = $professionData['last_renewal_year'].'-12-31';
                    }

                    $historicalProfession = Customerhistoricaldataprofession::create([
                        'customerhistoricaldata_id' => $historicalData->id,
                        'profession_id' => $professionData['profession_id'],
                        'tire_id' => $professionData['tire_id'] ?? null,
                        'registrationnumber' => $professionData['registrationnumber'] ?? null,
                        'registrationyear' => $professionData['registrationyear'] ?? null,
                        'practisingcertificatenumber' => $professionData['practisingcertificatenumber'] ?? null,
                        'registertype_id' => $professionData['registertype_id'] ?? null,
                        'last_renewal_year' => $professionData['last_renewal_year'] ?? null,
                        'last_renewal_year_cdp_points' => $professionData['last_renewal_year_cdp_points'] ?? null,
                        'last_renewal_expire_date' => $lastRenewalExpireDate,
                    ]);

                    // Handle documents if provided
                    if (isset($professionData['documents']) && is_array($professionData['documents'])) {
                        foreach ($professionData['documents'] as $doc) {
                            if (isset($doc['file']) && ! empty($doc['file'])) {
                                Customerhistoricaldatadocument::create([
                                    'customerhistoricaldataprofession_id' => $historicalProfession->id,
                                    'file' => $doc['file'],
                                    'description' => $doc['description'] ?? 'Certificate',
                                ]);
                            }
                        }
                    }
                }
            }

            return ['status' => 'success', 'message' => 'Historical data created successfully and sent for approval', 'data' => $historicalData];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Failed to create historical data: '.$e->getMessage()];
        }
    }

    public function importFromFile(string $filePath)
    {
        try {
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
            $rows = [];

            if (in_array(strtolower($extension), ['xlsx', 'xls'])) {
                // Handle Excel files - check if PhpSpreadsheet is available
                if (class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
                    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(Storage::path($filePath));
                    $worksheet = $spreadsheet->getActiveSheet();
                    $highestRow = $worksheet->getHighestRow();

                    // Find header row (skip instruction rows)
                    $headerRow = null;
                    $headerRowNum = 0;
                    for ($row = 1; $row <= min(10, $highestRow); $row++) {
                        $firstCell = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                        if ($firstCell && stripos($firstCell, 'name') !== false) {
                            $headerRowNum = $row;
                            // Get header row
                            $headers = [];
                            for ($col = 1; $col <= 20; $col++) {
                                $cellValue = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                                if ($cellValue) {
                                    $headers[$col] = strtolower(trim($cellValue));
                                }
                            }
                            $headerRow = $headers;
                            break;
                        }
                    }

                    if (! $headerRow) {
                        return ['status' => 'error', 'message' => 'Could not find header row in Excel file'];
                    }

                    // Process data rows (starting after header row)
                    for ($row = $headerRowNum + 1; $row <= $highestRow; $row++) {
                        $rowData = [];
                        $hasData = false;

                        foreach ($headerRow as $col => $header) {
                            $value = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            if ($value !== null && $value !== '') {
                                $hasData = true;
                                $rowData[$header] = $value;
                            }
                        }

                        if ($hasData) {
                            $rows[] = $rowData;
                        }
                    }
                } else {
                    return ['status' => 'error', 'message' => 'Excel file support requires PhpSpreadsheet package. Please install it or use CSV format.'];
                }
            } else {
                // Handle CSV files
                $file = fopen(Storage::path($filePath), 'r');
                if ($file === false) {
                    return ['status' => 'error', 'message' => 'Failed to open file'];
                }

                // Read header row (skip instruction rows)
                $headerRow = null;
                $lineNumber = 0;
                while (($row = fgetcsv($file)) !== false) {
                    $lineNumber++;
                    // Skip instruction rows and empty rows
                    if (empty($row) || empty(array_filter($row)) ||
                        stripos(implode('', $row), 'INSTRUCTIONS') !== false ||
                        stripos(implode('', $row), 'name') === false) {
                        continue;
                    }
                    // Found header row
                    $headerRow = array_map('trim', array_map('strtolower', $row));
                    break;
                }

                if (! $headerRow) {
                    fclose($file);

                    return ['status' => 'error', 'message' => 'Could not find header row in CSV file'];
                }

                // Read data rows
                while (($row = fgetcsv($file)) !== false) {
                    if (count($row) === count($headerRow)) {
                        $rowData = array_combine($headerRow, array_map('trim', $row));
                        // Skip empty rows
                        if (array_filter($rowData)) {
                            $rows[] = $rowData;
                        }
                    }
                }

                fclose($file);
            }

            if (empty($rows)) {
                return ['status' => 'error', 'message' => 'No data found in file'];
            }

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($rows as $index => $row) {
                try {
                    // Map Excel/CSV columns to data structure
                    // Look up nationality by name
                    $nationalityId = null;
                    $nationalityName = $row['nationality'] ?? $row['nationality_id'] ?? null;
                    if (! empty($nationalityName)) {
                        $nationality = \App\Models\Nationality::where('name', 'like', trim($nationalityName))->first();
                        if (! $nationality) {
                            // Try case-insensitive search
                            $nationality = \App\Models\Nationality::whereRaw('LOWER(name) = ?', [strtolower(trim($nationalityName))])->first();
                        }
                        if ($nationality) {
                            $nationalityId = $nationality->id;
                        } else {
                            $errorCount++;
                            $errors[] = 'Row '.($index + 2).': Nationality "'.$nationalityName.'" not found';

                            continue;
                        }
                    }

                    $data = [
                        'name' => $row['name'] ?? $row['first_name'] ?? '',
                        'surname' => $row['surname'] ?? $row['last_name'] ?? '',
                        'email' => $row['email'] ?? '',
                        'phone' => $row['phone'] ?? $row['phone_number'] ?? '',
                        'gender' => strtoupper($row['gender'] ?? ''),
                        'identificationnumber' => $row['identificationnumber'] ?? $row['id_number'] ?? $row['national_id'] ?? '',
                        'dob' => $row['dob'] ?? $row['date_of_birth'] ?? $row['birth_date'] ?? null,
                        'identificationtype' => strtoupper($row['identificationtype'] ?? $row['id_type'] ?? 'NATIONAL_ID'),
                        'nationality_id' => $nationalityId,
                        'address' => $row['address'] ?? '',
                        'placeofbirth' => $row['placeofbirth'] ?? $row['place_of_birth'] ?? '',
                        'accounttype_id' => $row['accounttype_id'] ?? 2,
                    ];

                    // Validate required fields
                    if (empty($data['name']) || empty($data['surname']) || empty($data['email']) || empty($data['identificationnumber'])) {
                        $errorCount++;
                        $errors[] = 'Row '.($index + 2).': Missing required fields (name, surname, email, or identification number)';

                        continue;
                    }

                    // Handle profession data - look up by names
                    $professions = [];
                    $professionName = $row['profession'] ?? $row['profession_id'] ?? null;
                    if (! empty($professionName)) {
                        // Find profession by name
                        $profession = \App\Models\Profession::where('name', 'like', trim($professionName))->first();
                        if (! $profession) {
                            // Try case-insensitive search
                            $profession = \App\Models\Profession::whereRaw('LOWER(name) = ?', [strtolower(trim($professionName))])->first();
                        }

                        if (! $profession) {
                            $errorCount++;
                            $errors[] = 'Row '.($index + 2).': Profession "'.$professionName.'" not found';

                            continue;
                        }

                        $professionId = $profession->id;

                        // Find tire by name for this profession
                        $tireId = null;
                        $tireName = $row['tire'] ?? $row['tire_id'] ?? null;
                        if (! empty($tireName)) {
                            $professionTire = \App\Models\ProfessionTire::where('profession_id', $professionId)
                                ->with('tire')
                                ->get()
                                ->first(function ($pt) use ($tireName) {
                                    return strtolower(trim($pt->tire->name ?? '')) === strtolower(trim($tireName));
                                });

                            if ($professionTire && $professionTire->tire) {
                                $tireId = $professionTire->tire->id;
                            } else {
                                $errorCount++;
                                $errors[] = 'Row '.($index + 2).': Tire "'.$tireName.'" not found for profession "'.$professionName.'"';

                                continue;
                            }
                        }

                        // Find registertype by name
                        $registertypeId = null;
                        $registertypeName = $row['registertype'] ?? $row['registertype_id'] ?? null;
                        if (! empty($registertypeName)) {
                            $registertype = \App\Models\Registertype::where('name', 'like', trim($registertypeName))->first();
                            if (! $registertype) {
                                // Try case-insensitive search
                                $registertype = \App\Models\Registertype::whereRaw('LOWER(name) = ?', [strtolower(trim($registertypeName))])->first();
                            }

                            if ($registertype) {
                                $registertypeId = $registertype->id;
                            } else {
                                $errorCount++;
                                $errors[] = 'Row '.($index + 2).': Register Type "'.$registertypeName.'" not found';

                                continue;
                            }
                        }

                        $professions[] = [
                            'profession_id' => $professionId,
                            'tire_id' => $tireId,
                            'registrationnumber' => $row['registrationnumber'] ?? $row['registration_number'] ?? null,
                            'registrationyear' => $row['registrationyear'] ?? $row['registration_year'] ?? null,
                            'practisingcertificatenumber' => $row['practisingcertificatenumber'] ?? $row['practising_certificate_number'] ?? null,
                            'registertype_id' => $registertypeId,
                            'last_renewal_year' => $row['last_renewal_year'] ?? null,
                            'last_renewal_year_cdp_points' => $row['last_renewal_year_cdp_points'] ?? null,
                            'last_renewal_expire_date' => $row['last_renewal_expire_date'] ?? null,
                        ];
                    }

                    $data['professions'] = $professions;

                    $result = $this->create($data);
                    if ($result['status'] == 'success') {
                        $successCount++;
                    } else {
                        $errorCount++;
                        $errors[] = 'Row '.($index + 2).': '.$result['message'];
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = 'Row '.($index + 2).': '.$e->getMessage();
                }
            }

            $message = "Import completed. Success: {$successCount}, Errors: {$errorCount}";
            if (! empty($errors)) {
                $message .= "\nErrors:\n".implode("\n", array_slice($errors, 0, 10));
                if (count($errors) > 10) {
                    $message .= "\n... and ".(count($errors) - 10).' more errors';
                }
            }

            return [
                'status' => $errorCount === 0 ? 'success' : 'partial',
                'message' => $message,
                'success_count' => $successCount,
                'error_count' => $errorCount,
            ];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Failed to import file: '.$e->getMessage()];
        }
    }
}
