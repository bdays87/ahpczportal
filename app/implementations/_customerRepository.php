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
                Storage::disk('s3')->delete($customer->profile);
                Storage::disk('s3')->exists($customer->profile) && Storage::disk('s3')->delete($customer->profile);
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
