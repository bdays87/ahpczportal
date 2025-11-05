<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Suspense extends Model
{
    use SoftDeletes;
    public function receipts(){
        return $this->hasMany(Receipt::class);
    }
    public function customer(){
        return $this->belongsTo(Customer::class);
    }
    public function currency(){
        return $this->belongsTo(Currency::class);
    }
   
}
