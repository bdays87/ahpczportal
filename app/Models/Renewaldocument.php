<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Renewaldocument extends Model
{
    public function tire(){
        return $this->belongsTo(Tire::class);
    }

    public function applicationtype(){
        return $this->belongsTo(Applicationtype::class);
    }
    public function registertype(){
        return $this->belongsTo(Registertype::class);
    }

    public function document(){
        return $this->belongsTo(Document::class);
    }

}
