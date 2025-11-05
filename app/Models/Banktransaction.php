<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banktransaction extends Model
{
    
    public function bank(){
        return $this->belongsTo(Bank::class);
    }
    public function currency(){
        return $this->belongsTo(Currency::class);
    }
    public function customer(){
        return $this->belongsTo(Customer::class);
    }
}
