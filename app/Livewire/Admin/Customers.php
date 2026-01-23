<?php

namespace App\Livewire\Admin;

use App\Interfaces\icityInterface;
use App\Interfaces\icustomerInterface;
use App\Interfaces\iemploymentlocationInterface;
use App\Interfaces\iemploymentstatusInterface;
use App\Interfaces\inationalityInterface;
use App\Interfaces\iprovinceInterface;
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

    public $breadcrumbs = [];

    public bool $modal = false;

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

    public function getcustomers()
    {
        return $this->customerrepo->getAll($this->search);
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
            $path = $this->profile->store('customers', 's3');
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
        dd($this->profile);
        if ($this->profile) {
            $this->validate([
                'profile' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
            $path = $this->profile->store('customers', 's3');
            dd($path);
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
            ['key' => 'profile', 'label' => 'Profile'],
            ['key' => 'regnumber', 'label' => 'Regnumber'],
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'surname', 'label' => 'Surname'],
            ['key' => 'identificationnumber', 'label' => 'National ID'],
            ['key' => 'dob', 'label' => 'Date of Birth'],
            ['key' => 'gender', 'label' => 'Gender'],
            ['key' => 'nationality.name', 'label' => 'Nationality'],
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
        ]);
    }
}
