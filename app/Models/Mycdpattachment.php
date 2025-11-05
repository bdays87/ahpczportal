<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mycdpattachment extends Model
{
    protected $fillable = [
        'mycdp_id',
        'type',
        'file'
    ];

    public function mycdp()
    {
        return $this->belongsTo(Mycdp::class);
    }
}
