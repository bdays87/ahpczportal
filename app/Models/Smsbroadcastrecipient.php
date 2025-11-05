<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Smsbroadcastrecipient extends Model
{
    protected $fillable = [
        'smsbroadcast_id',
        'customer_id',
        'phone',
        'status',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function broadcast()
    {
        return $this->belongsTo(Smsbroadcast::class, 'smsbroadcast_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
