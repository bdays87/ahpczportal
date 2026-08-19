<?php

namespace App\Livewire\Admin;

use App\Interfaces\icityInterface;
use App\Interfaces\icustomerInterface;
use App\Interfaces\iemploymentlocationInterface;
use App\Interfaces\iemploymentstatusInterface;
use App\Interfaces\inationalityInterface;
use App\Interfaces\iprovinceInterface;
use App\Models\Profession;
use App\Models\Registertype;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class Customers extends Component
{
    use Toast,WithFileUploads,WithPagination;

    public $search;

    public $profile = null;

    public $name;

    public $surname;

    public $nationalid;

    public $previousname;

    public $dob;

    public $gender;

    public $maritalstatus;

    public $identitytype;

    public $identitynumber;

    public $nationality_id;

    public $province_id;

    public $city_id;

    public $customertype_id;

    public $employmentstatus_id;

    public $employmentlocation_id;

    public $email;

    public $phone;

    public $address;

    public $placeofbirth;

    public $id;

    // Listing filters (kept separate from the create/edit form fields above)
    public $filter_gender;

    public $filter_province_id;

    public $filter_city_id;

    public $filter_profession_id;

    public $filter_registertype_id;

    public $filter_compliant;

    public $breadcrumbs = [];

    public bool $modal = false;

    public bool $importmodal = false;

    public $excelfile;

    protected $customerrepo;

    protected $nationalityrepo;

    protected $provincerepo;

    protected $cityrepo;

    protected $employmentstatusrepo;

    protected $employmentlocationrepo;

    public function boot(icustomerInterface $customerrepo, iemploymentlocationInterface $employmentlocationrepo, inationalityInterface $nationalityrepo, iprovinceInterface $provincerepo, icityInterface $cityrepo, iemploymentstatusInterface $employmentstatusrepo)
    {
        $this->customerrepo = $customerrepo;
        $this->employmentlocationrepo = $employmentlocationrepo;
        $this->nationalityrepo = $nationalityrepo;
        $this->provincerepo = $provincerepo;
        $this->cityrepo = $cityrepo;
        $this->employmentstatusrepo = $employmentstatusrepo;
    }

    public function mount()
    {
        $this->breadcrumbs = [
            [
                'label' => 'Dashboard',
                'icon' => 'o-home',
                'link' => route('dashboard'),
            ],
            [
                'label' => 'Customers',
            ],
        ];
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

    public function getfiltercities()
    {
        if ($this->filter_province_id) {
            return $this->getcities()->where('province_id', $this->filter_province_id)->values();
        }

        return $this->getcities();
    }

    public function getprofessions()
    {
        return Profession::orderBy('name')->get();
    }

    public function getregistertypes()
    {
        return Registertype::orderBy('name')->get();
    }

    public function getcomplianceoptions(): array
    {
        return [
            ['id' => 'COMPLIANT', 'name' => 'Compliant'],
            ['id' => 'NON_COMPLIANT', 'name' => 'Non-compliant'],
        ];
    }

    protected function filters(): array
    {
        return [
            'gender' => $this->filter_gender,
            'province_id' => $this->filter_province_id,
            'city_id' => $this->filter_city_id,
            'profession_id' => $this->filter_profession_id,
            'registertype_id' => $this->filter_registertype_id,
            'compliant' => $this->filter_compliant,
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterGender()
    {
        $this->resetPage();
    }

    public function updatedFilterProvinceId()
    {
        $this->filter_city_id = null;
        $this->resetPage();
    }

    public function updatedFilterCityId()
    {
        $this->resetPage();
    }

    public function updatedFilterProfessionId()
    {
        $this->resetPage();
    }

    public function updatedFilterRegistertypeId()
    {
        $this->resetPage();
    }

    public function updatedFilterCompliant()
    {
        $this->resetPage();
    }

    public function clearfilters()
    {
        $this->reset(['search', 'filter_gender', 'filter_province_id', 'filter_city_id', 'filter_profession_id', 'filter_registertype_id', 'filter_compliant']);
        $this->resetPage();
    }

    public function getcustomers()
    {
        return $this->customerrepo->getAll($this->search, $this->filters());
    }

    public function exportcsv()
    {
        $customers = $this->customerrepo->getallsearch($this->search, $this->filters());

        if ($customers->isEmpty()) {
            $this->warning('No customers to export');

            return null;
        }

        $filename = 'customers_'.date('Y-m-d_His').'.csv';
        $filepath = storage_path('app/public/'.$filename);

        $file = fopen($filepath, 'w');

        fputcsv($file, [
            'Regnumber', 'Name', 'Surname', 'National ID', 'Date of Birth', 'Gender',
            'Nationality', 'Province', 'City', 'Email', 'Phone', 'Profession(s)', 'Register Type(s)', 'Compliance Status',
        ]);

        foreach ($customers as $customer) {
            fputcsv($file, [
                $customer->regnumber ?? 'N/A',
                $customer->name,
                $customer->surname,
                $customer->identificationnumber ?? 'N/A',
                $customer->dob ?? 'N/A',
                $customer->gender ?? 'N/A',
                $customer->nationality->name ?? 'N/A',
                $customer->province->name ?? 'N/A',
                $customer->city->name ?? 'N/A',
                $customer->email ?? 'N/A',
                $customer->phone ?? 'N/A',
                $customer->customerprofessions->pluck('profession.name')->filter()->unique()->implode(', ') ?: 'N/A',
                $customer->customerprofessions->pluck('registertype.name')->filter()->unique()->implode(', ') ?: 'N/A',
                $customer->compliance_label,
            ]);
        }

        fclose($file);

        $this->success('Customers exported successfully');

        return response()->download($filepath)->deleteFileAfterSend(true);
    }

    public function save()
    {
        try {
            $this->validate([
                'name' => 'required',
                'surname' => 'required',
                'nationality_id' => 'required',
                'employmentstatus_id' => 'required',
                'employmentlocation_id' => 'required',
                'email' => ['required', 'email', 'max:255', $this->id ? "unique:customers,email,{$this->id},id" : 'unique:customers,email'],
                'phone' => ['required', 'string', 'regex:/^(?:\+263|0)7[1378]\d{7}$/'],
                'address' => 'required',
                'placeofbirth' => 'required',
                'identitynumber' => 'required',
                'identitytype' => 'required',
                'dob' => 'required',
                'gender' => 'required',
                'maritalstatus' => 'required',

            ], [
                'email.required' => 'Email address is required.',
                'email.email' => 'Please enter a valid email address.',
                'email.unique' => 'This email address is already registered.',
                'phone.required' => 'Phone number is required.',
                'phone.regex' => 'Please enter a valid Zimbabwe mobile number (e.g., 0771234567 or +263771234567).',
            ]);

            if ($this->nationality_id == 1) {
                $this->validate([
                    'province_id' => 'required',
                    'city_id' => 'required',
                ]);
                if ($this->identitytype == 'NATIONAL_ID') {

                    $result = preg_match('/[0-9]{8,9}[a-z,A-Z][0-9]{2}/i', $this->identitynumber);
                    if ($result == 0) {
                        $this->addError('identitynumber', 'Required formate 00000000L00');

                        return;
                    }
                }

            }
            if ($this->id) {
                $this->update();
            } else {
                $this->create();
            }
            $this->reset(['name', 'surname', 'nationality_id', 'province_id', 'city_id', 'employmentstatus_id', 'employmentlocation_id', 'email', 'phone', 'address', 'placeofbirth', 'identitynumber', 'identitytype', 'dob', 'gender', 'maritalstatus', 'previousname', 'nationalid']);
            $this->modal = false;
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }

    }

    public function create()
    {
        $path = null;
        if ($this->profile) {
            $this->validate([
                'profile' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
            $path = $this->profile->store('customers', config('filesystems.default'));
        }
        $response = $this->customerrepo->create([
            'profile' => $path,
            'name' => $this->name,
            'surname' => $this->surname,
            'nationality_id' => $this->nationality_id,
            'province_id' => $this->province_id,
            'city_id' => $this->city_id,
            'employmentstatus_id' => $this->employmentstatus_id,
            'employmentlocation_id' => $this->employmentlocation_id,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'place_of_birth' => $this->placeofbirth,
            'identificationnumber' => $this->identitynumber,
            'identificationtype' => $this->identitytype,
            'dob' => $this->dob,
            'gender' => $this->gender,
            'maritalstatus' => $this->maritalstatus,
            'previous_name' => $this->previousname,
        ]);
        if ($response['status'] == 'success') {
            $this->success($response['message']);
        } else {
            $this->error($response['message']);
        }
    }

    public function update()
    {
        $path = null;
        if ($this->profile) {
            $this->validate([
                'profile' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
            $path = $this->profile->store('customers', config('filesystems.default'));
        }
        $response = $this->customerrepo->update($this->id, [
            'profile' => $path,
            'name' => $this->name,
            'surname' => $this->surname,
            'nationality_id' => $this->nationality_id,
            'province_id' => $this->province_id,
            'city_id' => $this->city_id,
            'employmentstatus_id' => $this->employmentstatus_id,
            'employmentlocation_id' => $this->employmentlocation_id,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'place_of_birth' => $this->placeofbirth,
            'identificationnumber' => $this->identitynumber,
            'identificationtype' => $this->identitytype,
            'dob' => $this->dob,
            'gender' => $this->gender,
            'maritalstatus' => $this->maritalstatus,
            'previous_name' => $this->previousname,
        ]);
        if ($response['status'] == 'success') {
            $this->success($response['message']);
        } else {
            $this->error($response['message']);
        }

    }

    public function saveexcelimport()
    {
        $this->validate([
            'excelfile' => 'required|file|mimes:xlsx,csv,txt|max:20480',
        ], [
            'excelfile.mimes' => 'Please upload an Excel (.xlsx) or CSV (.csv) file.',
        ]);

        // Store on the local disk so the importer can read it with ZipArchive
        // even when the default filesystem is S3/DigitalOcean Spaces.
        $path = $this->excelfile->store('customerimports', 'local');
        $response = $this->customerrepo->importcustomersexcel($path);

        if ($response['status'] == 'success') {
            $this->success($response['message']);
        } else {
            $this->error($response['message']);
        }
        $this->reset('excelfile');
        $this->importmodal = false;
    }

    public function delete($id)
    {
        $response = $this->customerrepo->delete($id);
        if ($response['status'] == 'success') {
            $this->success($response['message']);
        } else {
            $this->error($response['message']);
        }
    }

    public function edit($id)
    {
        $customer = $this->customerrepo->get($id);
        $this->id = $id;
        $this->name = $customer->name;
        $this->surname = $customer->surname;
        $this->nationalid = $customer->nationalid;
        $this->previousname = $customer->previousname;
        $this->dob = $customer->dob;
        $this->gender = $customer->gender;
        $this->maritalstatus = $customer->maritalstatus;
        $this->identitytype = $customer->identificationtype;
        $this->identitynumber = $customer->identificationnumber;
        $this->nationality_id = $customer->nationality_id;
        $this->province_id = $customer->province_id;
        $this->city_id = $customer->city_id;
        $this->employmentstatus_id = $customer->employmentstatus_id;
        $this->employmentlocation_id = $customer->employmentlocation_id;
        $this->email = $customer->email;
        $this->phone = $customer->phone;
        $this->address = $customer->address;
        $this->placeofbirth = $customer->place_of_birth;

        $this->modal = true;
    }

    public function headers(): array
    {
        return [
            // Avatar + name + surname collapsed into one column — the table
            // was 11 columns wide before this, pushing the action buttons
            // off-screen no matter what. Nationality moved out of the default
            // view entirely (still visible/editable via the Edit modal).
            ['key' => 'profile', 'label' => 'Practitioner', 'class' => 'whitespace-normal break-words'],
            ['key' => 'regnumber', 'label' => 'Reg No'],
            ['key' => 'identificationnumber', 'label' => 'National ID'],
            ['key' => 'dob', 'label' => 'DOB'],
            ['key' => 'gender', 'label' => 'Gender'],
            ['key' => 'profession', 'label' => 'Profession', 'class' => 'whitespace-normal break-words max-w-[16rem]'],
            ['key' => 'registertype', 'label' => 'Register Type', 'class' => 'whitespace-normal break-words max-w-[11rem]'],
            ['key' => 'compliance', 'label' => 'Compliance'],
        ];
    }

    public function render()
    {
        return view('livewire.admin.customers', [
            'headers' => $this->headers(),
            'customers' => $this->getcustomers(),
            'nationalities' => $this->getnationalities(),
            'provinces' => $this->getprovinces(),
            'cities' => $this->getcities(),
            'employmentstatuses' => $this->getemploymentstatuses(),
            'employmentlocations' => $this->getemploymentlocations(),
            'filtercities' => $this->getfiltercities(),
            'professions' => $this->getprofessions(),
            'registertypes' => $this->getregistertypes(),
            'complianceoptions' => $this->getcomplianceoptions(),
        ]);
    }
}
