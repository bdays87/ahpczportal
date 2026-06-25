<?php

namespace App\implementations;

use App\Interfaces\icustomerInterface;
use App\Interfaces\igeneralutilsInterface;
use App\Interfaces\iuserInterface;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class _customerRepository implements icustomerInterface
{
    /**
     * Create a new class instance.
     */
    protected $customer;

    protected $userrepo;

    protected $generalutils;

    public function __construct(Customer $customer, igeneralutilsInterface $generalutils, iuserInterface $userrepo)
    {
        $this->customer = $customer;
        $this->generalutils = $generalutils;
        $this->userrepo = $userrepo;
    }

    public function getAll($search)
    {
        return $this->customer->with('nationality', 'province', 'city', 'employmentstatus', 'employmentlocation')->when($search, function ($query) use ($search) {
            return $query->where('name', 'like', '%'.$search.'%')
                ->orWhere('surname', 'like', '%'.$search.'%')
                ->orWhere('identificationnumber', 'like', '%'.$search.'%');
        })->orderBy('created_at', 'desc')->paginate(10);
    }

    public function getallsearch($search)
    {
        return $this->customer->with('nationality', 'province', 'city', 'employmentstatus', 'employmentlocation')->when($search, function ($query) use ($search) {
            return $query->where('name', 'like', '%'.$search.'%')
                ->orWhere('surname', 'like', '%'.$search.'%')
                ->orWhere('identificationnumber', 'like', '%'.$search.'%');
        })->get();
    }

    public function get($id)
    {
        return $this->customer->find($id);
    }

    public function create($data)
    {
        try {
            $checkcustomer = $this->customer->where('identificationnumber', $data['identificationnumber'])->first();
            if ($checkcustomer) {
                return ['status' => 'error', 'message' => 'Customer identity number already exists'];
            }
            $checkemail = $this->customer->where('email', $data['email'])->first();
            if ($checkemail) {
                return ['status' => 'error', 'message' => 'Customer email already exists'];
            }
            $data['uuid'] = Str::uuid()->toString();
            $data['regnumber'] = $this->generalutils->generateregistrationnumber()['data'];
            $customer = $this->customer->create($data);

            $response = $this->userrepo->create(['name' => $data['name'], 'surname' => $data['surname'], 'phone' => $data['phone'], 'email' => $data['email'], 'accounttype_id' => 2]);
            if ($response['status'] == 'success') {
                $customer->customeruser()->create(['customer_id' => $customer->id, 'user_id' => $response['data']->id]);
            }

            return ['status' => 'success', 'message' => 'Customer created successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function register($data)
    {
        try {

            if (isset($data['signup_type']) && $data['signup_type'] == 1) {
                $checkcustomer = $this->customer->where('regnumber', config('generalutils.registration_prefix').str_replace(config('generalutils.registration_prefix'), '', $data['registration_number']))
                    ->orWhere('regnumber', $data['registration_number'])
                    ->first();
                if (! $checkcustomer) {
                    return ['status' => 'error', 'message' => 'Customer registration number not found'];
                }
                if ($checkcustomer->name != $data['name'] || $checkcustomer->surname != $data['surname']) {
                    return ['status' => 'error', 'message' => 'Customer information does not match'];
                }
                $checkcustomer->customeruser()->create(['customer_id' => $checkcustomer->id, 'user_id' => Auth::user()->id]);
                $checkcustomer->profile_complete = true;
                $checkcustomer->first_login_completed = true;
                $checkcustomer->save();

                return ['status' => 'success', 'message' => 'Customer created successfully'];
            }
            if (isset($data['identificationnumber'])) {
                $checkcustomer = $this->customer->where('identificationnumber', $data['identificationnumber'])->first();
                if ($checkcustomer) {
                    return ['status' => 'error', 'message' => 'Customer identity number already exists'];
                }
            }

            $data['uuid'] = Str::uuid()->toString();
            $data['email'] = Auth::user()->email;
            $data['phone'] = Auth::user()->phone;
            // Always generate a new registration number for new customers (when customer doesn't exist)
            if (! isset($data['regnumber'])) {
                $regNumberResponse = $this->generalutils->generateregistrationnumber();
                if ($regNumberResponse['status'] == 'success') {
                    $data['regnumber'] = $regNumberResponse['data'];
                } else {
                    return ['status' => 'error', 'message' => 'Failed to generate registration number: '.$regNumberResponse['message']];
                }
            }
            if (! isset($data['profile_complete'])) {
                $data['profile_complete'] = true;
            }
            if (! isset($data['first_login_completed'])) {
                $data['first_login_completed'] = true;
            }

            // Remove fields that don't exist in customers table
            $fieldsToRemove = ['signup_type', 'registration_number'];
            foreach ($fieldsToRemove as $field) {
                if (isset($data[$field])) {
                    unset($data[$field]);
                }
            }

            $customer = $this->customer->create($data);

            $customer->customeruser()->create(['customer_id' => $customer->id, 'user_id' => Auth::user()->id]);

            return ['status' => 'success', 'message' => 'Customer created successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function update($id, $data)
    {
        try {
            $customer = $this->customer->find($id);
            if (! $customer) {
                return ['status' => 'error', 'message' => 'Customer not found'];
            }
            $checkcustomer = $this->customer->where('identificationnumber', $data['identificationnumber'])->where('id', '!=', $id)->first();
            if ($checkcustomer) {
                return ['status' => 'error', 'message' => 'Customer identity number already exists'];
            }
            $checkemail = $this->customer->where('email', $data['email'])->where('id', '!=', $id)->first();
            if ($checkemail) {
                return ['status' => 'error', 'message' => 'Customer email already exists'];
            }
            if ($data['profile'] == null) {
                unset($data['profile']);
            }
            if ($customer->regnumber == null) {
                $data['regnumber'] = $this->generalutils->generateregistrationnumber()['data'];
            }
            if (! isset($data['profile_complete'])) {
                $data['profile_complete'] = true;
            }
            if (! isset($data['first_login_completed'])) {
                $data['first_login_completed'] = true;
            }
            $customer->update($data);

            return ['status' => 'success', 'message' => 'Customer updated successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function delete($id)
    {
        try {
            $customer = $this->customer->with('customeruser.user')->where('id', $id)->first();
            if (! $customer) {
                return ['status' => 'error', 'message' => 'Customer not found'];
            }

            // Delete profile photo from storage
            if ($customer->profile) {
                // Storage::disk('s3')->delete($customer->profile);
                // Storage::disk('s3')->exists($customer->profile) && Storage::disk('s3')->delete($customer->profile);

                Storage::disk('public')->delete($customer->profile);
                Storage::disk('public')->exists($customer->profile) && Storage::disk('public')->delete($customer->profile);
            }

               // Disable FK checks, delete all related records, re-enable
               \DB::statement('SET FOREIGN_KEY_CHECKS=0');

                \DB::table('customerprofessions')->where('customer_id', $id)->get()->each(function ($cp) {
                \DB::table('customerapplications')->where('customerprofession_id', $cp->id)->delete();
                \DB::table('customerregistrations')->where('customerprofession_id', $cp->id)->delete();
                \DB::table('customerprofessiondocuments')->where('customerprofession_id', $cp->id)->delete();
                \DB::table('customerprofessionqualifications')->where('customerprofession_id', $cp->id)->delete();
                \DB::table('customerprofessionqualificationassessments')->where('customerprofession_id', $cp->id)->delete();
                \DB::table('mycdps')->where('customerprofession_id', $cp->id)->delete();
            });

            \DB::table('customerprofessions')->where('customer_id', $id)->delete();
            \DB::table('customerregistrations')->where('customer_id', $id)->delete();
            \DB::table('customeremployments')->where('customer_id', $id)->delete();
            \DB::table('customercontacts')->where('customer_id', $id)->delete();
            \DB::table('invoices')->where('customer_id', $id)->delete();
            \DB::table('otherapplications')->where('customer_id', $id)->delete();
            \DB::table('suspenses')->where('customer_id', $id)->delete();

            // Delete linked user account
            if ($customer->customeruser) {
                $user = $customer->customeruser->user;
                \DB::table('customerusers')->where('customer_id', $id)->delete();
                if ($user) {
                    \DB::table('users')->where('id', $user->id)->delete();
                }
            }

            \DB::table('customers')->where('id', $id)->delete();

            \DB::statement('SET FOREIGN_KEY_CHECKS=1');

            return ['status' => 'success', 'message' => 'Customer and all related records deleted successfully'];

        } catch (\Exception $e) {
            \DB::statement('SET FOREIGN_KEY_CHECKS=1'); // always re-enable
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function updateprofile($id, $data)
    {
        try {
            $customer = $this->customer->find($id);
            if (! $customer) {
                return ['status' => 'error', 'message' => 'Customer not found'];
            }
            if ($data['profile'] == null) {
                unset($data['profile']);
            }

            $customer->profile = $data['profile'];
            $customer->save();

            return ['status' => 'success', 'message' => 'Customer profile updated successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Import customers from the uploaded .xlsx file.
     *
     * Expected columns (by header position in the sheet):
     *   A => Name (Surname [middle/previous names...] FirstName)
     *   B => Customer (registration number, e.g. 511285E / 19209U)
     *   C => Title
     *   H => E-mail
     *   L => Physical Address 1
     *   M => Contact Person (phone number)
     *
     * Rules:
     *   - Registration number: strip any trailing letters (511285E -> 511285, 19209U -> 19209).
     *   - Skip rows whose registration number starts with "L" (e.g. L0111).
     *   - Skip rows missing a name, surname or registration number.
     *   - Skip rows whose registration number already exists in the customers table
     *     (or earlier in the same file).
     */
    public function importcustomersexcel($path)
    {
        try {
            $fullpath = Storage::path($path);
            $rows = $this->readXlsx($fullpath);
            if ($rows === false) {
                return ['status' => 'error', 'message' => 'Failed to read the Excel file'];
            }

            // Pull every registration number that already exists so we can dedupe quickly.
            $existing = $this->customer->whereNotNull('regnumber')->pluck('regnumber')->all();
            $existing = array_flip($existing);

            $now = now();
            $batch = [];
            $seen = [];
            $imported = 0;
            $skipped = 0;
            $batchSize = 500;
            $isHeader = true;

            foreach ($rows as $row) {
                // The first row is the header row.
                if ($isHeader) {
                    $isHeader = false;
                    continue;
                }

                $regnumber = $this->normalizeRegnumber($row['B'] ?? '');
                $rawname = trim($row['A'] ?? '');

                // Skip registration numbers that start with "L" (e.g. L0111).
                if ($regnumber === '' || strtoupper(substr($regnumber, 0, 1)) === 'L') {
                    $skipped++;
                    continue;
                }

                [$surname, $name, $previousname] = $this->splitcustomername($rawname);

                // Must have a name and a surname.
                if ($name === '' || $surname === '') {
                    $skipped++;
                    continue;
                }

                // Skip if this registration number already exists (in db or earlier in file).
                if (isset($existing[$regnumber]) || isset($seen[$regnumber])) {
                    $skipped++;
                    continue;
                }
                $seen[$regnumber] = true;

                $batch[] = [
                    'profile' => 'placeholder.jpg',
                    'uuid' => Str::uuid()->toString(),
                    'title' => trim($row['C'] ?? '') ?: null,
                    'name' => $name,
                    'surname' => $surname,
                    'previous_name' => $previousname ?: null,
                    'regnumber' => $regnumber,
                    'email' => trim($row['H'] ?? '') ?: null,
                    'phone' => trim($row['M'] ?? '') ?: null,
                    'address' => trim($row['L'] ?? '') ?: null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $imported++;

                if (count($batch) >= $batchSize) {
                    $this->customer->insert($batch);
                    $batch = [];
                }
            }

            if (! empty($batch)) {
                $this->customer->insert($batch);
            }

            return [
                'status' => 'success',
                'message' => "Customers imported successfully. Imported: {$imported}, Skipped: {$skipped}.",
            ];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Strip trailing letters from a registration number (511285E -> 511285).
     */
    private function normalizeRegnumber($value)
    {
        $value = trim((string) $value);

        return preg_replace('/[A-Za-z]+$/', '', $value);
    }

    /**
     * Split a full name where the surname comes first and the first name last.
     *   "Tauzeni Innocent"               -> surname=Tauzeni, name=Innocent
     *   "Tauzeni Rudolph Innocent"       -> surname=Tauzeni, name=Innocent, previous=Rudolph
     *   "Tauzeni Rudolph Anesu Innocent" -> surname=Tauzeni, name=Innocent, previous=Rudolph Anesu
     *
     * @return array [surname, name, previousname]
     */
    private function splitcustomername($fullname)
    {
        $parts = preg_split('/\s+/', trim($fullname), -1, PREG_SPLIT_NO_EMPTY);

        if (empty($parts)) {
            return ['', '', ''];
        }
        if (count($parts) === 1) {
            // Only one token - treat it as surname, no first name.
            return [$parts[0], '', ''];
        }

        $surname = array_shift($parts);
        $name = array_pop($parts);
        $previousname = implode(' ', $parts);

        return [$surname, $name, $previousname];
    }

    /**
     * Minimal .xlsx reader (no PhpSpreadsheet dependency).
     * Returns an array of rows, each row being an associative array keyed by
     * column letter (A, B, C ...). Returns false on failure.
     */
    private function readXlsx($fullpath)
    {
        if (! class_exists('ZipArchive')) {
            throw new \Exception('PHP ZipArchive extension is required to read Excel files.');
        }

        $zip = new \ZipArchive;
        if ($zip->open($fullpath) !== true) {
            return false;
        }

        // Shared strings table.
        $shared = [];
        $sharedXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedXml !== false) {
            $sx = new \SimpleXMLElement($sharedXml);
            foreach ($sx->si as $si) {
                $text = '';
                if (isset($si->t)) {
                    $text = (string) $si->t;
                } else {
                    foreach ($si->r as $r) {
                        $text .= (string) $r->t;
                    }
                }
                $shared[] = $text;
            }
        }

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        if ($sheetXml === false) {
            return false;
        }

        $sheet = new \SimpleXMLElement($sheetXml);
        $rows = [];
        foreach ($sheet->sheetData->row as $row) {
            $cells = [];
            foreach ($row->c as $c) {
                $ref = (string) $c['r'];
                $col = preg_replace('/[0-9]+/', '', $ref);
                $type = (string) $c['t'];
                if ($type === 's') {
                    $value = $shared[(int) $c->v] ?? '';
                } elseif ($type === 'inlineStr') {
                    $value = (string) $c->is->t;
                } else {
                    $value = (string) $c->v;
                }
                $cells[$col] = $value;
            }
            $rows[] = $cells;
        }

        return $rows;
    }

    public function getcustomerprofile($uuid)
    {
        try {
            $customer = $this->customer->with('nationality', 'province', 'city', 'employmentstatus', 'employmentlocation', 'employmentdetails', 'contactdetails', 'customerprofessions.applications.applicationtype', 'suspenses')->where('uuid', $uuid)->first();
            if (! $customer) {
                return null;
            }

            return $customer;
        } catch (\Exception $e) {
            return null;
        }
    }
}
