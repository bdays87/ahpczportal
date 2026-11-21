<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappSession extends Model
{
    protected $guarded = [];

    protected $casts = [
        'data' => 'array',
        'code_expires_at' => 'datetime',
        'last_message_at' => 'datetime',
    ];
}
