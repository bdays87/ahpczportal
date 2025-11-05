<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registrationfee extends Model
{
    
    public function currency(){
        return $this->belongsTo(Currency::class);
    }
    public function customertype(){
        return $this->belongsTo(Customertype::class);
    }
    public function employmentlocation(){
        return $this->belongsTo(Employmentlocation::class);
    }
    
}
