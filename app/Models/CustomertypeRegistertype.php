<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomertypeRegistertype extends Model
{

    public function registertype(){
        return $this->belongsTo(Registertype::class);
    }
    public function customertype(){
        return $this->belongsTo(Customertype::class);
    }
}
