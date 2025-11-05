<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    public function suspense(){
        return $this->belongsTo(Suspense::class);
    }
    public function customer(){
        return $this->belongsTo(Customer::class);
    }
    public function currency(){
        return $this->belongsTo(Currency::class);
    }
    public function exchangerate(){
        return $this->belongsTo(Exchangerate::class);
    }
    public function invoice(){
        return $this->belongsTo(Invoice::class);
    }
}
