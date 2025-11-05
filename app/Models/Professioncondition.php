<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Professioncondition extends Model
{
   public function customertype(){
    return $this->belongsTo(Customertype::class);
   }
}
