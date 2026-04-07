<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otherservice extends Model
{
    protected $guarded = [];

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
