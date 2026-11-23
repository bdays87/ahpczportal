<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappLink extends Model
{
    protected $guarded = [];

    protected $casts = ['linked_at' => 'datetime'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
