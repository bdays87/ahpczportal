<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customerprofessionqualification extends Model
{
    
    public function qualificationcategory(){
        return $this->belongsTo(Qualificationcategory::class);
    }
    public function qualificationlevel(){
        return $this->belongsTo(Qualificationlevel::class);
    }
}
