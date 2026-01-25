<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customerhistoricaldata extends Model
{
    protected $table = 'customerhistoricaldata';

    protected $fillable = [
        'user_id',
        'created_by',
        'name',
        'surname',
        'gender',
        'identificationnumber',
        'dob',
        'identificationtype',
        'nationality_id',
        'address',
        'placeofbirth',
        'phone',
        'status',
        'approvedby',
        'rejection_reason',
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function nationality()
    {
        return $this->belongsTo(Nationality::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approvedby');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function professions()
    {
        return $this->hasMany(Customerhistoricaldataprofession::class);
    }
}
