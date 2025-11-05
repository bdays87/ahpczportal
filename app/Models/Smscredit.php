<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Smscredit extends Model
{
    protected $fillable = [
        'credits',
        'used_credits',
        'remaining_credits',
        'description',
        'addedby',
    ];

    protected $casts = [
        'credits' => 'integer',
        'used_credits' => 'integer',
        'remaining_credits' => 'integer',
    ];

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'addedby');
    }
}
