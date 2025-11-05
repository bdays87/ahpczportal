<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Emailbroadcastrecipient extends Model
{
    protected $fillable = [
        'emailbroadcast_id',
        'customer_id',
        'email',
        'status',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function broadcast()
    {
        return $this->belongsTo(Emailbroadcast::class, 'emailbroadcast_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
