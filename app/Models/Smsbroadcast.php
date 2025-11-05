<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Smsbroadcast extends Model
{
    protected $fillable = [
        'campaign_name',
        'message',
        'filters',
        'total_recipients',
        'sent_count',
        'failed_count',
        'credits_used',
        'status',
        'createdby',
    ];

    protected $casts = [
        'filters' => 'array',
        'total_recipients' => 'integer',
        'sent_count' => 'integer',
        'failed_count' => 'integer',
        'credits_used' => 'integer',
    ];

    public function recipients()
    {
        return $this->hasMany(Smsbroadcastrecipient::class, 'smsbroadcast_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'createdby');
    }

    public function getProgressPercentageAttribute(): float
    {
        if ($this->total_recipients === 0) {
            return 0;
        }

        return (($this->sent_count + $this->failed_count) / $this->total_recipients) * 100;
    }

    public function getPendingCountAttribute(): int
    {
        return $this->total_recipients - ($this->sent_count + $this->failed_count);
    }
}
