<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Professiondocument extends Model
{
    public function profession(){
        return $this->belongsTo(Profession::class);
    }
    public function document(){
        return $this->belongsTo(Document::class);
    }
    public function customertype(){
        return $this->belongsTo(CustomerType::class);
    }
}
