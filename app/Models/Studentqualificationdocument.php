<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Studentqualificationdocument extends Model
{
    public function document(){
        return $this->belongsTo(Document::class);
    }
    public function studentqualification(){
        return $this->belongsTo(Studentqualification::class);
    }
}
