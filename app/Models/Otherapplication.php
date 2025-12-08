<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;
use App\Models\Otherservice;
use App\Models\Otherapplicationdocument;
use App\Models\User;


class Otherapplication extends Model
{
    public function customer(){
        return $this->belongsTo(Customer::class);
    }
    public function otherservice(){
        return $this->belongsTo(Otherservice::class);
    }
    public function documents(){
        return $this->hasMany(Otherapplicationdocument::class);
    }
    public function approvedby(){
        return $this->belongsTo(User::class,'approvedby');
    }
    public function customerprofession(){
        return $this->belongsTo(Customerprofession::class);
    }

    public function invoice(){
        return $this->hasOne(Invoice::class,'source_id','id')->where('source','otherapplication');
    }
}
