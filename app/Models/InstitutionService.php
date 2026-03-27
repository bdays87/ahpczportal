<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstitutionService extends Model
{
    protected $fillable = ['institution_id', 'name', 'description', 'status'];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }
}
