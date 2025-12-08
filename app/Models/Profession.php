<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profession extends Model
{
    
    public function tires(){
        return $this->hasMany(ProfessionTire::class);
    }
    public function conditions(){
        return $this->hasMany(Professioncondition::class);
    }
}
