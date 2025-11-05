<?php

namespace App\implementations;

use App\Interfaces\icustomerregistrationInterface;
use App\Models\Customerregistration;

class _customerregistrationRepository implements icustomerregistrationInterface
{
    /**
     * Create a new class instance.
     */
    protected $customerregistration;

    public function __construct(Customerregistration $customerregistration)
    {
        $this->customerregistration = $customerregistration;
    }

    public function reportData(array $filters = [])
    {
        $query = $this->customerregistration
            ->with([
                'customerprofession.customer.province',
                'customerprofession.customer.city',
                'customerprofession.profession',
                'customerprofession.registertype',
                'customerprofession.customertype',
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

