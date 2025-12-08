<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customerprofession extends Model
{
    public function customer(){
        return $this->belongsTo(Customer::class);
    }
    public function profession(){
        return $this->belongsTo(Profession::class);
    }
    public function customertype(){
        return $this->belongsTo(Customertype::class);
    }
    public function employmentstatus(){
        return $this->belongsTo(Employmentstatus::class);
    }
    public function employmentlocation(){
        return $this->belongsTo(Employmentlocation::class);
    }
    public function registertype(){
        return $this->belongsTo(Registertype::class);
    }
    public function registration(){
        return $this->hasOne(Customerregistration::class);
    }
    public function applications(){
        return $this->hasMany(Customerapplication::class);
    }
    public function documents(){
        return $this->hasMany(Customerprofessiondocument::class);
    }
    public function qualifications(){
        return $this->hasMany(Customerprofessionqualification::class);
    }
    public function qualificationassessments(){
        return $this->hasMany(Customerprofessionqualificationassessment::class);
    }
    public function comments(){
        return $this->hasMany(Customerprofessioncomment::class);
    }
    public function institutions(){
        return $this->hasMany(Customerprofessioninstitution::class);
    }
    public function studentqualifications(){
        return $this->hasOne(Studentqualification::class);
    }
    public function placements(){
        return $this->hasMany(Studentplacement::class);
    }

    /**
     * Get the last approved application and check if it's compliant
     *
     * @return array Returns an array with the application and compliance status
     */
    public function getLastApprovedApplicationCompliance(): array
    {
        $lastApprovedApplication = $this->applications()
            ->where('status', 'APPROVED')
            ->orderBy('updated_at', 'desc')
            ->first();

        if (!$lastApprovedApplication) {
            return [
                'application' => null,
                'is_compliant' => false,
                'status' => 'No approved application found'
            ];
        }

        $isCompliant = $lastApprovedApplication->isValid();
        
        return [
            'application' => $lastApprovedApplication,
            'is_compliant' => $isCompliant,
            'status' => $isCompliant ? 'Compliant' : 'Non-compliant',
            'certificate_expiry_date' => $lastApprovedApplication->certificate_expiry_date,
            'certificate_status' => $lastApprovedApplication->certificate_status
        ];
    }

    /**
     * Get the compliance status of the last approved application
     *
     * @return string 'Compliant', 'Non-compliant', or 'No approved application found'
     */
    public function getComplianceStatusAttribute(): string
    {
        $result = $this->getLastApprovedApplicationCompliance();
        return $result['status'];
    }

    /**
     * Check if the last approved application is compliant
     *
     * @return bool True if compliant, false otherwise
     */
    public function isCompliant(): bool
    {
        $result = $this->getLastApprovedApplicationCompliance();
        return $result['is_compliant'];
    }

    public function totalcdpoints($year = null)
    {
        if($year){
            return $this->hasMany(Mycdp::class)->where('year',$year)->where('status','PROCESSED')->sum('points');
        }else{
            return $this->hasMany(Mycdp::class)->where('year',date('Y'))->where('status','PROCESSED')->sum('points');
        }
    }
    public function tire(){
        return $this->belongsTo(Tire::class);
    }
}
