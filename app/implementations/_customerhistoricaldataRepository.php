<?php

namespace App\implementations;

use App\Interfaces\icustomerhistoricaldataInterface;
use App\Interfaces\icustomerInterface;
use App\Interfaces\igeneralutilsInterface;
use App\Models\Customer;
use App\Models\Customerapplication;
use App\Models\Customerhistoricaldata;
use App\Models\Customerprofession;
use App\Models\Customerprofessiondocument;
use App\Models\Customerregistration;
use App\Models\Customeruser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class _customerhistoricaldataRepository implements icustomerhistoricaldataInterface
{
    protected $customerhistoricaldata;

    protected $customerrepo;

    protected $generalutils;

    public function __construct(
        Customerhistoricaldata $customerhistoricaldata,
        icustomerInterface $customerrepo,
        igeneralutilsInterface $generalutils
    ) {
        $this->customerhistoricaldata = $customerhistoricaldata;
        $this->customerrepo = $customerrepo;
        $this->generalutils = $generalutils;
    }

    public function getAll($status = null)
    {
        return $this->customerhistoricaldata
            ->with('user', 'nationality', 'profession', 'registertype', 'documents')
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function get($id)
    {
        return $this->customerhistoricaldata
            ->with('user', 'nationality', 'profession', 'registertype', 'documents')
            ->find($id);
    }

    public function approve($id)
    {
        try {
            $historicalData = $this->customerhistoricaldata->with('user', 'documents')->find($id);

            if (! $historicalData) {
                return ['status' => 'error', 'message' => 'Historical data not found'];
            }

            if ($historicalData->status != 'PENDING') {
                return ['status' => 'error', 'message' => 'This submission has already been processed'];
            }

            // Check if customer already exists
            $customer = Customer::where('identificationnumber', $historicalData->identificationnumber)->first();

            if (! $customer) {
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
                    'regnumber' => $this->generalutils->generateregistrationnumber()['data'],
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

            // Create customerprofession
            // Using defaults: customertype_id = 1 (Practitioner), employmentstatus_id = 1, employmentlocation_id = 1
            // These should ideally be configurable or asked during approval
            $customerprofession = Customerprofession::create([
                'customer_id' => $customer->id,
                'profession_id' => $historicalData->profession_id,
                'customertype_id' => 1, // Practitioner - should be configurable
                'employmentstatus_id' => 1, // Default - should be configurable
                'employmentlocation_id' => 1, // Default - should be configurable
                'registertype_id' => $historicalData->registertype_id,
                'uuid' => Str::uuid()->toString(),
                'employmentsector' => 'PRIVATE', // Default
                'status' => 'APPROVED',
                'year' => $historicalData->registrationyear ?? date('Y'),
                'created_by' => Auth::user()->id,
            ]);

            // Copy documents to customerprofession documents
            foreach ($historicalData->documents as $doc) {
                Customerprofessiondocument::create([
                    'customerprofession_id' => $customerprofession->id,
                    'document_id' => 1, // Default document type - should be configurable
                    'file' => $doc->file,
                    'status' => 'VERIFIED',
                    'verifiedby' => Auth::user()->id,
                ]);
            }

            // Create customerregistration
            Customerregistration::create([
                'customer_id' => $customer->id,
                'customerprofession_id' => $customerprofession->id,
                'status' => 'APPROVED',
                'certificatenumber' => $historicalData->registrationnumber,
                'certificateexpirydate' => $historicalData->expiredate,
                'year' => $historicalData->registrationyear,
                'registrationdate' => $historicalData->registrationyear ? $historicalData->registrationyear.'-01-01' : now(),
            ]);

            // Create customerapplication
            Customerapplication::create([
                'customer_id' => $customer->id,
                'customerprofession_id' => $customerprofession->id,
                'registertype_id' => $historicalData->registertype_id,
                'status' => 'APPROVED',
                'certificate_number' => $historicalData->practisingcertificatenumber,
                'certificate_expiry_date' => $historicalData->expiredate,
                'year' => $historicalData->applicationyear,
                'registration_date' => $historicalData->applicationyear ? $historicalData->applicationyear.'-01-01' : now(),
                'approvedby' => Auth::user()->id,
            ]);

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
}
