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
        })->paginate(10);
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

            if ($data['signup_type'] == 1) {
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
            if (! isset($data['regnumber'])) {
                $data['regnumber'] = $this->generalutils->generateregistrationnumber()['data'];
            }
            if (! isset($data['profile_complete'])) {
                $data['profile_complete'] = true;
            }
            if (! isset($data['first_login_completed'])) {
                $data['first_login_completed'] = true;
            }
            unset($data['signup_type']);
            unset($data['registration_number']);
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
            $customer = $this->customer->where('id', $id)->first();
            if (! $customer) {
                return ['status' => 'error', 'message' => 'Customer not found'];
            }
            if ($customer->profile != null) {
                if (Storage::disk('s3')->exists($customer->profile)) {
                    Storage::disk('s3')->delete($customer->profile);
                }
            }
            if (! $customer->customeruser) {
                $customer->delete();

                return ['status' => 'success', 'message' => 'Customer deleted successfully'];

            }

            return ['status' => 'error', 'message' => 'Customer has users, cannot be deleted'];

        } catch (\Exception $e) {
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
