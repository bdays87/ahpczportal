<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otherservice extends Model
{
    public function currency(){
        return $this->belongsTo(Currency::class);
    }
    public function documents(){
        return $this->hasMany(Otherservicedocument::class);
    }

    public function otherapplicationdocuments(){
        return $this->hasMany(Otherapplicationdocument::class);
    }
}
