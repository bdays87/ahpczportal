<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Renewalfee extends Model
{
    public function tire(){
        return $this->belongsTo(Tire::class);
    }

    public function currency(){
        return $this->belongsTo(Currency::class);
    }

    public function registertype(){
        return $this->belongsTo(Registertype::class);
    }

    public function applicationtype(){
        return $this->belongsTo(Applicationtype::class);
    }
}
