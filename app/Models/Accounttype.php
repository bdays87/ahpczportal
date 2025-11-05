<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class Accounttype extends Model
{
    public function roles()
    {
        return $this->hasMany(Role::class);
    }
    public function systemmodules()
    {
        return $this->hasMany(Systemmodule::class);
    }
}
