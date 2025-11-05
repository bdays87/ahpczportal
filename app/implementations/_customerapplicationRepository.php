<?php

namespace App\implementations;

use App\Interfaces\icustomerapplicationInterface;
use App\Interfaces\igeneralutilsInterface;
use App\Models\Customerapplication;
use App\Models\Mycdp;
use App\Notifications\ApplicationApprovalNotification;
use Illuminate\Support\Facades\Auth;

class _customerapplicationRepository implements icustomerapplicationInterface
{
    /**
     * Create a new class instance.
     */
    protected $customerapplication;

    protected $mycdp;

    protected $generalutils;

    public function __construct(Customerapplication $customerapplication, Mycdp $mycdp, igeneralutilsInterface $generalutils)
    {
        $this->customerapplication = $customerapplication;
        $this->mycdp = $mycdp;
        $this->generalutils = $generalutils;
    }

    public function retrieve($year, $status, $search = null, $applicationtype_id = null)
    {
        return $this->customerapplication
            ->with('customerprofession.customer', 'customerprofession.profession', 'applicationtype', 'customerprofession.registertype', 'customerprofession.customertype')
            ->withWhereHas('customerprofession.customer', function ($query) use ($search) {
                if ($search) {
                    return $query->where('name', 'like', '%'.$search.'%')->orWhere('surname', 'like', '%'.$search.'%');
                }
            })
            ->when($applicationtype_id, function ($query) use ($applicationtype_id) {
                return $query->where('applicationtype_id', $applicationtype_id);
            })

            ->where('year', $year)
            ->where('status', $status)
            ->where('applicationtype_id', '!=', 1)
            ->get();
    }

    public function getbyuuid($uuid)
    {
        $customerapplication = $this->customerapplication->with('customerprofession.customer.customeruser.user', 'customerprofession.profession', 'applicationtype', 'documents.document')->where('uuid', $uuid)->first();
        if (! $customerapplication) {
            return ['status' => 'error', 'message' => 'Customer application not found'];
        }
        $yearrange = [$customerapplication->year - 1, $customerapplication->year];
        $mycdps = $this->mycdp->with('attachments')->where('customerprofession_id', $customerapplication->customerprofession_id)->whereIn('year', $yearrange)->get();

        return ['customerapplication' => $customerapplication, 'mycdps' => $mycdps];
    }

    public function makedecision($data)
    {
        $customerapplication = $this->customerapplication->with('customerprofession.customer', 'customerprofession.profession')->find($data['id']);
        if (! $customerapplication) {
            return ['status' => 'error', 'message' => 'Customer application not found'];
        }
        if ($data['status'] == 'APPROVED') {
            $certificatenumber = $this->generalutils->generatecertificatenumber($customerapplication->customerprofession->profession->prefix, $customerapplication->id);
            $customerapplication->update(['status' => 'APPROVED', 'approvedby' => Auth::user()->id, 'certificate_number' => $certificatenumber, 'registration_date' => date('Y-m-d'), 'certificate_expiry_date' => date('Y').'-12-31']);
        }
        $user = $customerapplication->customerprofession->customer?->customeruser?->user;
        if ($user) {
            $user->notify(new ApplicationApprovalNotification($customerapplication->customerprofession->customer, $customerapplication->customerprofession->profession, $data['status'], $data['comment']));
        }

        return ['status' => 'success', 'message' => 'Customer application decision made successfully'];
    }

    public function reportData(array $filters = [])
    {
        $query = $this->customerapplication
            ->with([
                'customerprofession.customer.province',
                'customerprofession.customer.city',
                'customerprofession.profession',
                'customerprofession.registertype',
                'customerprofession.customertype',
                'applicationtype',
            ])
            ->whereHas('customerprofession.customer');

        // Filter by date range
        if (! empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (! empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Filter by province
        if (! empty($filters['province_id'])) {
            $query->whereHas('customerprofession.customer', function ($q) use ($filters) {
                $q->where('province_id', $filters['province_id']);
            });
        }

        // Filter by city
        if (! empty($filters['city_id'])) {
            $query->whereHas('customerprofession.customer', function ($q) use ($filters) {
                $q->where('city_id', $filters['city_id']);
            });
        }

        // Filter by gender
        if (! empty($filters['gender'])) {
            $query->whereHas('customerprofession.customer', function ($q) use ($filters) {
                $q->where('gender', $filters['gender']);
            });
        }

        // Filter by register type
        if (! empty($filters['registertype_id'])) {
            $query->whereHas('customerprofession', function ($q) use ($filters) {
                $q->where('registertype_id', $filters['registertype_id']);
            });
        }

        // Filter by year
        if (! empty($filters['year'])) {
            $query->where('year', $filters['year']);
        }

        // Filter by profession
        if (! empty($filters['profession_id'])) {
            $query->whereHas('customerprofession', function ($q) use ($filters) {
                $q->where('profession_id', $filters['profession_id']);
            });
        }

        // Filter by status
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter by application type
        if (! empty($filters['applicationtype_id'])) {
            $query->where('applicationtype_id', $filters['applicationtype_id']);
        }

        // Search by name
        if (! empty($filters['search'])) {
            $query->whereHas('customerprofession.customer', function ($q) use ($filters) {
                $q->where('name', 'like', '%'.$filters['search'].'%')
                    ->orWhere('surname', 'like', '%'.$filters['search'].'%')
                    ->orWhere('email', 'like', '%'.$filters['search'].'%');
            });
        }

        return $query->orderBy('created_at', 'desc')->get();
    }
}
