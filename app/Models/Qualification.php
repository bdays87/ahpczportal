<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Qualification extends Model
{
    
    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }
}
