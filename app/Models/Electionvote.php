<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Electionvote extends Model
{
    protected $fillable = [
        'election_id',
        'customer_id',
        'electionposition_id',
        'electioncandidate_id',
    ];

    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function position()
    {
        return $this->belongsTo(Electionposition::class, 'electionposition_id');
    }

    public function candidate()
    {
        return $this->belongsTo(Electioncandidate::class, 'electioncandidate_id');
    }
}
