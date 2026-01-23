<?php

namespace App\Livewire\Customer;

use App\Interfaces\icityInterface;
use App\Interfaces\icustomerInterface;
use App\Interfaces\iemploymentlocationInterface;
use App\Interfaces\iemploymentstatusInterface;
use App\Interfaces\inationalityInterface;
use App\Interfaces\iprovinceInterface;
use App\Interfaces\iuserInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

class ProfileUpdate extends Component
{
    use Toast, WithFileUploads;

    public $name;

    public $surname;

    public $email;

    public $phone;

    public $address;

    public $placeofbirth;

    public $identitynumber;

    public $identitytype;

    public $dob;

    public $gender;

    public $maritalstatus;

    public $previousname;

    public $nationality_id;

    public $province_id;

    public $city_id;

    public $employmentstatus_id;

    public $employmentlocation_id;

    public $profile;

    public $current_password;

    public $new_password;

    public $new_password_confirmation;

    protected $customerrepo;

    protected $nationalityrepo;

    protected $provincerepo;

    protected $cityrepo;

    protected $employmentstatusrepo;

    protected $employmentlocationrepo;

    protected $userrepo;

    public function boot(
        icustomerInterface $customerrepo,
        inationalityInterface $nationalityrepo,
        iprovinceInterface $provincerepo,
        icityInterface $cityrepo,
        iemploymentstatusInterface $employmentstatusrepo,
        iemploymentlocationInterface $employmentlocationrepo,
        iuserInterface $userrepo
    ) {
        $this->customerrepo = $customerrepo;
        $this->nationalityrepo = $nationalityrepo;
        $this->provincerepo = $provincerepo;
        $this->cityrepo = $cityrepo;
        $this->employmentstatusrepo = $employmentstatusrepo;
        $this->employmentlocationrepo = $employmentlocationrepo;
        $this->userrepo = $userrepo;
    }

    public function mount()
    {
        $user = Auth::user();
        if (! $user || ! $user->customer || ! $user->customer->customer) {
            return $this->redirect(route('dashboard'));
        }

        $customer = $user->customer->customer;

        // Load customer data
        $this->name = $customer->name;
        $this->surname = $customer->surname;
        $this->email = $customer->email;
        $this->phone = $customer->phone;
        $this->address = $customer->address;
        $this->placeofbirth = $customer->place_of_birth;
        $this->identitynumber = $customer->identificationnumber;
        $this->identitytype = $customer->identificationtype;
        $this->dob = $customer->dob;
        $this->gender = $customer->gender;
        $this->maritalstatus = $customer->maritalstatus;
        $this->previousname = $customer->previous_name;
        $this->nationality_id = $customer->nationality_id;
        $this->province_id = $customer->province_id;
        $this->city_id = $customer->city_id;
        $this->employmentstatus_id = $customer->employmentstatus_id;
        $this->employmentlocation_id = $customer->employmentlocation_id;
    }

    public function getnationalities()
    {
        return $this->nationalityrepo->getAll('');
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

    public function getemploymentlocations()
    {
        return $this->employmentlocationrepo->getAll();
    }

    public function save()
    {
        $this->validate([
            'name' => 'required',
            'surname' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'address' => 'required',
            'placeofbirth' => 'required',
            'identitynumber' => 'required',
            'identitytype' => 'required',
            'dob' => 'required|date',
            'gender' => 'required',
            'maritalstatus' => 'required',
            'nationality_id' => 'required',
            'province_id' => 'required',
            'city_id' => 'required',
            'employmentstatus_id' => 'required',
            'employmentlocation_id' => 'required',
            'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();
        $customer = $user->customer->customer;

        $data = [
            'name' => $this->name,
            'surname' => $this->surname,
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
            'nationality_id' => $this->nationality_id,
            'province_id' => $this->province_id,
            'city_id' => $this->city_id,
            'employmentstatus_id' => $this->employmentstatus_id,
            'employmentlocation_id' => $this->employmentlocation_id,
            'profile_complete' => true,
            'first_login_completed' => true,
        ];

        if ($this->profile) {
            $data['profile'] = $this->profile->store('customers', 'public');
        } else {
            $data['profile'] = null;
        }
        $response = $this->customerrepo->update($customer->id, $data);

        if ($response['status'] == 'success') {
            $this->success('Profile updated successfully!');
            $this->redirect(route('dashboard'));
        } else {
            $this->error($response['message']);
        }
    }

    public function changePassword(): void
    {
        $this->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ], [
            'current_password.required' => 'Current password is required.',
            'new_password.required' => 'New password is required.',
            'new_password.min' => 'New password must be at least 8 characters.',
            'new_password.confirmed' => 'New password confirmation does not match.',
        ]);

        $user = Auth::user();

        if (! Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'The current password is incorrect.');

            return;
        }

        $response = $this->userrepo->update($user->id, [
            'password' => $this->new_password,
        ]);

        if ($response['status'] == 'success') {
            $this->success('Password changed successfully!');
            $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        } else {
            $this->error($response['message']);
        }
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.customer.profile-update', [
            'nationalities' => $this->getnationalities(),
            'provinces' => $this->getprovinces(),
            'cities' => $this->getcities(),
            'employmentstatuses' => $this->getemploymentstatuses(),
            'employmentlocations' => $this->getemploymentlocations(),
        ]);
    }
}
