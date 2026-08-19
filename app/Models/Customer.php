<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use DateTime;

class Customer extends Model
{
    use Notifiable;
    public function nationality(){
        return $this->belongsTo(Nationality::class);
    }
    public function province(){
        return $this->belongsTo(Province::class);
    }
    public function city(){
        return $this->belongsTo(City::class);
    }
    public function employmentstatus(){
        return $this->belongsTo(Employmentstatus::class);
    }
    public function employmentlocation(){
        return $this->belongsTo(Employmentlocation::class);
    }
    public function employmentdetails(){
        return $this->hasMany(Customeremployment::class);
    }
    public function contactdetails(){
        return $this->hasMany(Customercontact::class);
    }
    public function suspenses(){
        return $this->hasMany(Suspense::class);
    }

    public function customerprofessions(){
        return $this->hasMany(Customerprofession::class);
    }
    public function customeruser(){
        return $this->hasOne(Customeruser::class);
    }

    public function getage(){
        $dob = DateTime::createFromFormat('Y-m-d', $this->dob);
        if($dob === false){
            return 0;
        }
        return \Carbon\Carbon::parse($dob)->diffInYears(\Carbon\Carbon::now());
    }

    /**
     * Overall compliance label across all of this customer's professions:
     * "Compliant" if any profession has a still-valid approved application,
     * "Non-compliant" if it has professions but none are currently valid,
     * "N/A" if the customer has no professions at all.
     *
     * Uses already eager-loaded `customerprofessions.applications` when available
     * to avoid N+1 queries on listing pages.
     */
    public function getComplianceLabelAttribute(): string
    {
        if (! $this->relationLoaded('customerprofessions')) {
            $this->load('customerprofessions.applications');
        }

        if ($this->customerprofessions->isEmpty()) {
            return 'N/A';
        }

        foreach ($this->customerprofessions as $customerprofession) {
            $applications = $customerprofession->relationLoaded('applications')
                ? $customerprofession->applications
                : $customerprofession->applications()->get();

            $lastApproved = $applications->where('status', 'APPROVED')->sortByDesc('updated_at')->first();

            if ($lastApproved && $lastApproved->isValid()) {
                return 'Compliant';
            }
        }

        return 'Non-compliant';
    }
}
