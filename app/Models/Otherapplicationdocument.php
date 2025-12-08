<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otherapplicationdocument extends Model
{
    public function otherapplication(){
        return $this->belongsTo(Otherapplication::class);
    }
    public function otherservicedocument(){
        return $this->belongsTo(Otherservicedocument::class);
    }
    public function document(){
        return $this->belongsTo(Document::class);
    }
}
