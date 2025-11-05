<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customerprofessioninstitution extends Model
{
    public function customerprofession(){
        return $this->belongsTo(Customerprofession::class);
    }
}
