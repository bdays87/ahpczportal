<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paynowtransaction extends Model
{
    
    public function customer(){
        return $this->belongsTo(Customer::class);
    }
    public function currency(){
        return $this->belongsTo(Currency::class);
    }
}
