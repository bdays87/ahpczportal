<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    
    public function currency(){
        return $this->belongsTo(Currency::class);
    }
    public function customer(){
        return $this->belongsTo(Customer::class);
    }
    public function settlementsplit(){
        return $this->belongsTo(Settlementsplit::class,'settlementsplit_id','id');
    }
    public function proofofpayment(){
        return $this->hasMany(Proofofpayment::class);
    }
    public function receipts(){
        return $this->hasMany(Receipt::class);
    }
}
