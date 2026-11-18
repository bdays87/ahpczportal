<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Accreditationlevel extends Model
{
    protected $fillable = ['accreditation_id', 'name', 'sort_order', 'status'];

    public function accreditation()
    {
        return $this->belongsTo(Accreditation::class);
    }
}
