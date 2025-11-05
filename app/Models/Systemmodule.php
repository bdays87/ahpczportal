<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Systemmodule extends Model
{
    
    public function submodules()
    {
        return $this->hasMany(Submodule::class);
    }
    public function accounttype()
    {
        return $this->belongsTo(Accounttype::class);
    }
}
