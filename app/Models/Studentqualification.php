<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Studentqualification extends Model
{
    public function documents(){
        return $this->hasMany(Studentqualificationdocument::class);
    }
    public function qualificationlevel(){
        return $this->belongsTo(Qualificationlevel::class);
    }
}
