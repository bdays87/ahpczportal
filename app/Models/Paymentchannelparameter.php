<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paymentchannelparameter extends Model
{
    
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
