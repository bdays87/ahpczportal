<?php

namespace App\Livewire\Components;

use App\Interfaces\icityInterface;
use App\Interfaces\icustomerInterface;
use App\Interfaces\iemploymentlocationInterface;
use App\Interfaces\iemploymentstatusInterface;
use App\Interfaces\inationalityInterface;
use App\Interfaces\iprofessionInterface;
use App\Interfaces\iprovinceInterface;
use App\Interfaces\iregistertypeInterface;
use App\Models\Customer;
use App\Models\Customerhistoricaldata;
use App\Models\Customerhistoricaldataprofession;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

class Checkcustomer extends Component
{
    use Toast, WithFileUploads;

    // Step management
    public $currentStep = 1; // 1: valid cert question, 2: nationalID input, 3: customer found confirmation, 4: historical data capture, 5: update personal details

    // Step 1: Valid certificate question
    public $hasValidCertificate = null;

    // Step 2: National ID input
    public $nationalID;

    // Step 3: Customer found confirmation
    public $foundCustomer = null;

    // Step 4: Historical data capture
    public $captureHistoricalData = false;

    public $historicalName;

    public $historicalSurname;

    public $historicalGender;

    public $historicalNationalID;

    public $historicalDOB;

    public $historicalIdentityType;

    public $historicalIdentityNumber;

    public $historicalNationalityId;

    public $historicalAddress;

    public $historicalPlaceOfBirth;

    public $historicalPhone;

    public $historicalProfessions = []; // Array of profession data with registration numbers and certificates

    // Step 5: Update personal details (existing fields)
    public $profile = null;

    public $name;

    public $email;

    public $phone;

    public $surname;

    public $nationalid;

    public $previousname;

    public $dob;

    public $gender;

    public $maritalstatus;

    public $identitytype;

    public $identitynumber;

    public $nationality_id;

    public $employmentstatus_id;

    public $employmentlocation_id;

    public $province_id;

    public $city_id;

    public $customertype_id;

    public $address;

    public $placeofbirth;

    public $id;

    public $signup_type;

    public $registration_number;

    protected $customerrepo;

    protected $nationalityrepo;

    protected $provincerepo;

    protected $cityrepo;

    protected $employmentstatusrepo;

    protected $employmentlocationrepo;

    protected $professionrepo;

    protected $registertyperepo;

    public $modal = false;

    public $hasPendingApproval = false;

    public $pendingSubmissions = [];

    public function mount()
    {
        // Check if user has pending historical data submissions
        $pendingSubmissions = Customerhistoricaldata::where('user_id', Auth::user()->id)
            ->where('status', 'PENDING')
            ->with('professions.profession')
            ->get();

        if ($pendingSubmissions->count() > 0) {
            $this->hasPendingApproval = true;
            $this->pendingSubmissions = $pendingSubmissions;

            return; // Don't show modal, show status message instead
        }

        // Only show modal if user doesn't have customer linked
        if (Auth::user()->customer == null) {
            $this->modal = true;
            $this->currentStep = 1;
        } elseif (Auth::user()->customer->customer && Auth::user()->customer->customer->profile_complete == 0) {
            $customer = Auth::user()->customer->customer;
            $this->modal = true;
            $this->currentStep = 5; // Go directly to update personal details
            $this->name = Auth::user()->name;
            $this->surname = Auth::user()->surname;
            $this->identitynumber = $customer->identificationnumber;
            $this->identitytype = $customer->identificationtype;
            $this->dob = $customer->dob;
            $this->gender = $customer->gender;
            $this->maritalstatus = $customer->maritalstatus;
            $this->previousname = $customer->previous_name;
            $this->nationality_id = $customer->nationality_id;
            $this->province_id = $customer->province_id;
            $this->city_id = $customer->city_id;
            $this->address = $customer->address;
            $this->placeofbirth = $customer->place_of_birth;
            $this->phone = Auth::user()->phone;
            $this->email = Auth::user()->email;
        }
    }

    public function boot(
        icustomerInterface $customerrepo,
        iemploymentlocationInterface $employmentlocationrepo,
        inationalityInterface $nationalityrepo,
        iprovinceInterface $provincerepo,
        icityInterface $cityrepo,
        iemploymentstatusInterface $employmentstatusrepo,
        iprofessionInterface $professionrepo,
        iregistertypeInterface $registertyperepo
    ) {
        $this->customerrepo = $customerrepo;
        $this->employmentlocationrepo = $employmentlocationrepo;
        $this->nationalityrepo = $nationalityrepo;
        $this->provincerepo = $provincerepo;
        $this->cityrepo = $cityrepo;
        $this->employmentstatusrepo = $employmentstatusrepo;
        $this->professionrepo = $professionrepo;
        $this->registertyperepo = $registertyperepo;
    }

    // Populate historical data fields from authenticated user
    public function populateHistoricalDataFromUser()
    {
        $user = Auth::user();
        $this->historicalName = $user->name ?? '';
        $this->historicalSurname = $user->surname ?? '';
        $this->historicalPhone = $user->phone ?? '';
    }

    // Step 1: Handle valid certificate response
    public function setHasValidCertificate($value)
    {
        $this->hasValidCertificate = $value;
        if ($value == 1) {
            $this->currentStep = 2; // Move to National ID input
        } else {
            // Populate historical data from authenticated user
            $this->populateHistoricalDataFromUser();
            // Initialize with one empty profession for historical data
            $this->historicalProfessions = [
                [
                    'profession_id' => null,
                    'tire_id' => null,
                    'registration_number' => '',
                    'registration_year' => '',
                    'practising_certificate_number' => '',
                    'registertype_id' => null,
                    'last_renewal_year' => '',
                    'last_renewal_year_cdp_points' => '',
                    'last_renewal_expire_date' => '',
                    'certificates' => $this->initializeDefaultCertificates(),
                    'descriptions' => $this->initializeDefaultDescriptions(),
                ],
            ];
            $this->currentStep = 4; // Move to historical data capture
        }
    }

    // Step 2: Search customer by National ID
    public function searchCustomer()
    {
        $this->validate([
            'nationalID' => 'required',
        ]);

        $customer = Customer::where('identificationnumber', $this->nationalID)->first();

        if ($customer) {
            // Check if customer already has a customeruser record
            if ($customer->customeruser && $customer->customeruser->user_id != Auth::user()->id) {
                $this->error('This customer is already linked to another user account.');

                return;
            }

            $this->foundCustomer = $customer;
            $this->currentStep = 3; // Move to confirmation step
        } else {
            // Customer not found, go to historical data capture
            // Populate historical data from authenticated user
            $this->populateHistoricalDataFromUser();
            $this->currentStep = 4;
            $this->captureHistoricalData = true;
            // Initialize with one empty profession
            $this->historicalProfessions = [
                [
                    'profession_id' => null,
                    'tire_id' => null,
                    'registration_number' => '',
                    'registration_year' => '',
                    'practising_certificate_number' => '',
                    'registertype_id' => null,
                    'last_renewal_year' => '',
                    'last_renewal_year_cdp_points' => '',
                    'last_renewal_expire_date' => '',
                    'certificates' => $this->initializeDefaultCertificates(),
                    'descriptions' => $this->initializeDefaultDescriptions(),
                ],
            ];
        }
    }

    // Step 3: Confirm customer data
    public function confirmCustomer()
    {
        if (! $this->foundCustomer) {
            $this->error('Customer not found');

            return;
        }

        // Create customeruser record if it doesn't exist
        if (! $this->foundCustomer->customeruser) {
            $this->foundCustomer->customeruser()->create([
                'customer_id' => $this->foundCustomer->id,
                'user_id' => Auth::user()->id,
            ]);
        }

        $this->success('Customer linked successfully. Please update your personal details.');
        $this->currentStep = 5; // Move to update personal details
        $this->loadCustomerData();
    }

    // Initialize default mandatory certificates for a profession
    private function initializeDefaultCertificates(): array
    {
        return [
            null, // Registration Certificate
            null, // Practising Certificate
        ];
    }

    // Initialize default certificate descriptions
    private function initializeDefaultDescriptions(): array
    {
        return [
            'Registration Certificate',
            'Practising Certificate',
        ];
    }

    // Step 4: Add historical profession
    public function addHistoricalProfession()
    {
        $this->historicalProfessions[] = [
            'profession_id' => null,
            'tire_id' => null,
            'registration_number' => '',
            'registration_year' => '',
            'practising_certificate_number' => '',
            'registertype_id' => null,
            'last_renewal_year' => '',
            'last_renewal_year_cdp_points' => '',
            'last_renewal_expire_date' => '',
            'certificates' => $this->initializeDefaultCertificates(),
            'descriptions' => $this->initializeDefaultDescriptions(),
        ];
    }

    // Auto-calculate last renewal expire date when last renewal year changes
    // Also load tiers when profession changes
    public function updatedHistoricalProfessions($value, $key)
    {
        // Check if last_renewal_year was updated
        // Key format: "historicalProfessions.0.last_renewal_year"
        if (str_ends_with($key, '.last_renewal_year') && ! empty($value)) {
            $parts = explode('.', $key);
            if (count($parts) >= 3) {
                $index = (int) $parts[1];
                // Calculate expire date as end of the renewal year (December 31st)
                if (isset($this->historicalProfessions[$index])) {
                    $this->historicalProfessions[$index]['last_renewal_expire_date'] = $value.'-12-31';
                }
            }
        }

        // Check if profession_id was updated
        // Key format: "historicalProfessions.0.profession_id"
        if (str_ends_with($key, '.profession_id')) {
            $parts = explode('.', $key);
            if (count($parts) >= 3) {
                $index = (int) $parts[1];
                // Reset tire_id when profession changes
                if (isset($this->historicalProfessions[$index])) {
                    $this->historicalProfessions[$index]['tire_id'] = null;
                }
            }
        }
    }

    // Get tiers for a profession
    public function getTiresForProfession($professionId)
    {
        if (! $professionId) {
            return [];
        }

        $tires = $this->professionrepo->gettires($professionId);

        return $tires->map(function ($professionTire) {
            return [
                'id' => $professionTire->tire_id,
                'name' => $professionTire->tire->name ?? 'Tire '.$professionTire->tire_id,
            ];
        })->toArray();
    }

    // Step 4: Remove historical profession
    public function removeHistoricalProfession($index)
    {
        unset($this->historicalProfessions[$index]);
        $this->historicalProfessions = array_values($this->historicalProfessions);
    }

    // Step 4: Add certificate to historical profession (for additional certificates beyond mandatory ones)
    public function addHistoricalCertificate($professionIndex)
    {
        if (! isset($this->historicalProfessions[$professionIndex]['certificates'])) {
            $this->historicalProfessions[$professionIndex]['certificates'] = $this->initializeDefaultCertificates();
            $this->historicalProfessions[$professionIndex]['descriptions'] = $this->initializeDefaultDescriptions();
        }
        $this->historicalProfessions[$professionIndex]['certificates'][] = null;
        $this->historicalProfessions[$professionIndex]['descriptions'][] = '';
    }

    // Step 4: Remove certificate from historical profession (only for additional certificates, not mandatory ones)
    public function removeHistoricalCertificate($professionIndex, $certIndex)
    {
        // Prevent removal of mandatory certificates (index 0 and 1)
        if ($certIndex < 2) {
            return;
        }

        unset($this->historicalProfessions[$professionIndex]['certificates'][$certIndex]);
        unset($this->historicalProfessions[$professionIndex]['descriptions'][$certIndex]);
        $this->historicalProfessions[$professionIndex]['certificates'] = array_values($this->historicalProfessions[$professionIndex]['certificates']);
        $this->historicalProfessions[$professionIndex]['descriptions'] = array_values($this->historicalProfessions[$professionIndex]['descriptions']);
    }

    // Step 4: Submit historical data
    public function submitHistoricalData()
    {
        $this->validate([
            'historicalName' => 'required',
            'historicalSurname' => 'required',
            'historicalGender' => 'required',
            'historicalDOB' => 'required|date',
            'historicalIdentityType' => 'required',
            'historicalIdentityNumber' => 'required',
            'historicalNationalityId' => 'required',
            'historicalAddress' => 'required',
            'historicalPlaceOfBirth' => 'required',
            'historicalPhone' => 'required',
            'historicalProfessions' => 'required|array|min:1',
        ], [
            'historicalProfessions.required' => 'Please add at least one profession.',
            'historicalProfessions.min' => 'Please add at least one profession.',
        ]);

        // Validate each profession
        foreach ($this->historicalProfessions as $index => $profession) {
            if (empty($profession['profession_id'])) {
                $this->addError('historicalProfessions.'.$index.'.profession_id', 'Please select a profession.');

                return;
            }
            if (empty($profession['tire_id'])) {
                $this->addError('historicalProfessions.'.$index.'.tire_id', 'Please select a tier for this profession.');

                return;
            }
            if (empty($profession['registration_number'])) {
                $this->addError('historicalProfessions.'.$index.'.registration_number', 'Please enter registration number.');

                return;
            }
            // Validate mandatory certificates
            if (empty($profession['certificates']) || count($profession['certificates']) < 2) {
                $this->addError('historicalProfessions.'.$index.'.certificates', 'Please attach both Registration Certificate and Practising Certificate.');

                return;
            }
            // Check if Registration Certificate (index 0) is attached
            if (empty($profession['certificates'][0])) {
                $this->addError('historicalProfessions.'.$index.'.certificates.0', 'Registration Certificate is required.');

                return;
            }
            // Check if Practising Certificate (index 1) is attached
            if (empty($profession['certificates'][1])) {
                $this->addError('historicalProfessions.'.$index.'.certificates.1', 'Practising Certificate is required.');

                return;
            }
        }

        try {
            // Create one historical data record with customer information
            $historicalData = Customerhistoricaldata::create([
                'user_id' => Auth::user()->id,
                'name' => $this->historicalName,
                'surname' => $this->historicalSurname,
                'gender' => $this->historicalGender,
                'identificationnumber' => $this->historicalIdentityNumber,
                'dob' => $this->historicalDOB,
                'identificationtype' => $this->historicalIdentityType,
                'nationality_id' => $this->historicalNationalityId,
                'address' => $this->historicalAddress,
                'placeofbirth' => $this->historicalPlaceOfBirth,
                'phone' => $this->historicalPhone,
                'status' => 'PENDING',
            ]);

            // Create profession records for each profession
            foreach ($this->historicalProfessions as $professionData) {
                // Store certificates
                $storedCertificates = [];
                foreach ($professionData['certificates'] as $index => $certificate) {
                    if ($certificate) {
                        $path = $certificate->store('historical-certificates', 's3');
                        $storedCertificates[] = [
                            'file' => $path,
                            'description' => $professionData['descriptions'][$index] ?? 'Previous Certificate',
                        ];
                    }
                }

                // Auto-calculate last_renewal_expire_date if last_renewal_year is provided but expire date is not
                $lastRenewalExpireDate = $professionData['last_renewal_expire_date'] ?? null;
                if (empty($lastRenewalExpireDate) && ! empty($professionData['last_renewal_year'])) {
                    $lastRenewalExpireDate = $professionData['last_renewal_year'].'-12-31';
                }

                // Create profession record
                $historicalProfession = Customerhistoricaldataprofession::create([
                    'customerhistoricaldata_id' => $historicalData->id,
                    'profession_id' => $professionData['profession_id'],
                    'tire_id' => $professionData['tire_id'],
                    'registrationnumber' => $professionData['registration_number'],
                    'registrationyear' => $professionData['registration_year'],
                    'practisingcertificatenumber' => $professionData['practising_certificate_number'],
                    'registertype_id' => $professionData['registertype_id'],
                    'last_renewal_year' => $professionData['last_renewal_year'] ?? null,
                    'last_renewal_year_cdp_points' => $professionData['last_renewal_year_cdp_points'] ?? null,
                    'last_renewal_expire_date' => $lastRenewalExpireDate,
                ]);

                // Attach documents to the profession record
                foreach ($storedCertificates as $cert) {
                    $historicalProfession->documents()->create($cert);
                }
            }

            $this->success('Historical data submitted successfully. Please wait for admin approval.');
            $this->modal = false;

            return $this->redirect(route('dashboard'));
        } catch (\Exception $e) {
            $this->error('Failed to submit historical data: '.$e->getMessage());
        }
    }

    // Step 5: Update personal details (existing register method)
    public function register()
    {
        $this->validate([
            'name' => 'required',
            'surname' => 'required',
            'nationality_id' => 'required',
            'address' => 'required',
            'placeofbirth' => 'required',
            'identitynumber' => 'required',
            'identitytype' => 'required',
            'dob' => 'required|date',
            'gender' => 'required',
            'maritalstatus' => 'required',
        ]);

        if ($this->nationality_id == 230) { // Zimbabwe
            $this->validate([
                'province_id' => 'required',
                'city_id' => 'required',
            ]);
            if ($this->identitytype == 'NATIONAL_ID') {
                $result = preg_match('/[0-9]{8,9}[a-z,A-Z][0-9]{2}/i', $this->identitynumber);
                if ($result == 0) {
                    $this->addError('identitynumber', 'Required format 00000000L00');

                    return;
                }
            }
        }

        $customer = Auth::user()->customer->customer ?? null;

        if (! $customer) {
            if ($this->profile) {
                $this->validate([
                    'profile' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                ]);
                $this->profile = $this->profile->store('customers', 's3');
            }
            $response = $this->customerrepo->register([
                'name' => $this->name,
                'surname' => $this->surname,
                'nationality_id' => $this->nationality_id,
                'province_id' => $this->province_id,
                'city_id' => $this->city_id,
                'address' => $this->address,
                'email' => $this->email,
                'phone' => $this->phone,
                'place_of_birth' => $this->placeofbirth,
                'identificationnumber' => $this->identitynumber,
                'identificationtype' => $this->identitytype,
                'dob' => $this->dob,
                'gender' => $this->gender,
                'maritalstatus' => $this->maritalstatus,
                'previous_name' => $this->previousname,
                'profile' => $this->profile,
            ]);
        } else {
            $response = $this->customerrepo->update($customer->id, [
                'name' => $this->name,
                'surname' => $this->surname,
                'email' => $this->email,
                'phone' => $this->phone,
                'nationality_id' => $this->nationality_id,
                'province_id' => $this->province_id,
                'city_id' => $this->city_id,
                'address' => $this->address,
                'place_of_birth' => $this->placeofbirth,
                'identificationnumber' => $this->identitynumber,
                'identificationtype' => $this->identitytype,
                'dob' => $this->dob,
                'gender' => $this->gender,
                'maritalstatus' => $this->maritalstatus,
                'previous_name' => $this->previousname,
                'profile' => $this->profile,
            ]);
        }

        if ($response['status'] == 'success') {
            $this->modal = false;
            $this->success($response['message']);

            return $this->redirect(route('dashboard'));
        } else {
            $this->error($response['message']);
        }
    }

    public function loadCustomerData()
    {
        if ($this->foundCustomer) {
            $customer = $this->foundCustomer;
            $this->name = Auth::user()->name;
            $this->surname = Auth::user()->surname;
            $this->identitynumber = $customer->identificationnumber;
            $this->identitytype = $customer->identificationtype;
            $this->dob = $customer->dob;
            $this->gender = $customer->gender;
            $this->maritalstatus = $customer->maritalstatus;
            $this->previousname = $customer->previous_name;
            $this->nationality_id = $customer->nationality_id;
            $this->province_id = $customer->province_id;
            $this->city_id = $customer->city_id;
            $this->address = $customer->address;
            $this->placeofbirth = $customer->place_of_birth;
            $this->phone = Auth::user()->phone;
            $this->email = Auth::user()->email;
        }
    }

    public function getnationalities()
    {
        return $this->nationalityrepo->getAll(null);
    }

    public function getemploymentlocations()
    {
        return $this->employmentlocationrepo->getAll();
    }

    public function getprovinces()
    {
        return $this->provincerepo->getAll();
    }

    public function getcities()
    {
        return $this->cityrepo->getAll();
    }

    public function getemploymentstatuses()
    {
        return $this->employmentstatusrepo->getAll();
    }

    public function getprofessions()
    {
        return $this->professionrepo->getAll(null, null);
    }

    public function getregistertypes()
    {
        return $this->registertyperepo->getAll();
    }

    public function render()
    {
        return view('livewire.components.checkcustomer', [
            'nationalities' => $this->getnationalities(),
            'provinces' => $this->getprovinces(),
            'cities' => $this->getcities(),
            'employmentstatuses' => $this->getemploymentstatuses(),
            'employmentlocations' => $this->getemploymentlocations(),
            'professions' => $this->getprofessions(),
            'registertypes' => $this->getregistertypes(),
        ]);
    }
}
