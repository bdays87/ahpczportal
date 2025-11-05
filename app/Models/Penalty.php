<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penalty extends Model
{
    public function tire()
    {
        return $this->belongsTo(Tire::class);
    }
}
