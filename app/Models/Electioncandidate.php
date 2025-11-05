<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Electioncandidate extends Model
{
    protected $fillable = [
        'electionposition_id',
        'customer_id',
        'profile_picture',
        'description',
        'status',
        'createdby',
        'updatedby',
    ];

    public function position()
    {
        return $this->belongsTo(Electionposition::class, 'electionposition_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function votes()
    {
        return $this->hasMany(Electionvote::class, 'electioncandidate_id');
    }

    public function getVoteCountAttribute(): int
    {
        return $this->votes()->count();
    }
}
