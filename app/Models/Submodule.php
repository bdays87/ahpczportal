<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

class Submodule extends Model
{

    public function systemmodule()
    {
        return $this->belongsTo(Systemmodule::class);
    }

    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }
}
