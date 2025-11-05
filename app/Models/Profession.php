<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profession extends Model
{
    
    public function tire(){
        return $this->belongsTo(Tire::class);
    }
    public function conditions(){
        return $this->hasMany(Professioncondition::class);
    }
}
