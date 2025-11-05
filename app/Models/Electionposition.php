<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Electionposition extends Model
{
    protected $fillable = [
        'election_id',
        'name',
        'description',
        'status',
        'createdby',
        'updatedby',
    ];

    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    public function candidates()
    {
        return $this->hasMany(Electioncandidate::class, 'electionposition_id');
    }

    public function votes()
    {
        return $this->hasMany(Electionvote::class, 'electionposition_id');
    }

    public function getResultsAttribute()
    {
        return $this->candidates->map(function ($candidate) {
            return [
                'candidate' => $candidate,
                'votes' => $this->votes()->where('electioncandidate_id', $candidate->id)->count(),
            ];
        })->sortByDesc('votes');
    }
}
