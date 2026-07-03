<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otherapplicationinstaccreditation extends Model
{
    protected $fillable = ['otherapplication_id', 'name', 'level', 'status'];

    public function otherapplication()
    {
        return $this->belongsTo(Otherapplication::class);
    }
}
