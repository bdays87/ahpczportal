<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settlementsplit extends Model
{
    
    public function employmentlocation(){
        return $this->belongsTo(Employmentlocation::class, 'employmentlocation_id');
    }
    public function currency(){
        return $this->belongsTo(Currency::class, 'currency_id');
    }
  
    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
