<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfessionTire extends Model
{
    //
    public function profession(){
        return $this->belongsTo(Profession::class);
    }
    public function tire(){
        return $this->belongsTo(Tire::class);
    }
}
