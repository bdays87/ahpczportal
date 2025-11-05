<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customertype extends Model
{
    
    public function registertypes()
    {
        return $this->hasMany(CustomertypeRegistertype::class);
    }
}
