<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otherapplicationinstcustomer extends Model
{
    protected $fillable = ['otherapplication_id', 'customer_id', 'employmenttype', 'date_employed', 'status'];

    public function otherapplication(){
        return $this->belongsTo(Otherapplication::class);
    }

    public function customer(){
        return $this->belongsTo(Customer::class);
    }
}
