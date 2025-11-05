<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    
    public function tire(){
        return $this->belongsTo(Tire::class);
    }
}
