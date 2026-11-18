<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Accreditation extends Model
{
    protected $fillable = ['name', 'description', 'status'];

    public function levels()
    {
        return $this->hasMany(Accreditationlevel::class)->orderBy('sort_order')->orderBy('id');
    }
}
