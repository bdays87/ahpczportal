<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Election extends Model
{
    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'status',
        'year',
        'publish_status',
        'createdby',
        'updatedby',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'year' => 'integer',
    ];

    public function positions()
    {
        return $this->hasMany(Electionposition::class);
    }

    public function votes()
    {
        return $this->hasMany(Electionvote::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'createdby');
    }

    public function isActive(): bool
    {
        return $this->status === 'RUNNING' &&
               now()->between($this->start_date, $this->end_date);
    }

    public function hasEnded(): bool
    {
        return now()->gt($this->end_date) || $this->status === 'CLOSED';
    }

    public function isPublished(): bool
    {
        return $this->publish_status === 'PUBLISHED';
    }

    public function canBeActivated(): bool
    {
        return $this->publish_status === 'PUBLISHED' &&
               $this->status === 'DRAFT' &&
               $this->positions()->count() > 0;
    }

    public function isReadyToStart(): bool
    {
        return $this->publish_status === 'PUBLISHED' &&
               $this->status === 'RUNNING' &&
               now()->lt($this->start_date);
    }
}
