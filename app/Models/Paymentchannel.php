<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paymentchannel extends Model
{
    public function parameters()
    {
        return $this->hasMany(Paymentchannelparameter::class);
    }
}
