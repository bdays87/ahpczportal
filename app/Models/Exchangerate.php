<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exchangerate extends Model
{
    use SoftDeletes;
    
    public function basecurrency(){
        return $this->belongsTo(Currency::class, 'base_currency_id');
    }
    public function secondarycurrency(){
        return $this->belongsTo(Currency::class, 'secondary_currency_id');
    }
    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
