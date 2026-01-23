<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customerhistoricaldata extends Model
{
    protected $table = 'customerhistoricaldata';

    protected $fillable = [
        'user_id',
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
        'profession_id',
        'registrationnumber',
        'registrationyear',
        'practisingcertificatenumber',
        'applicationyear',
        'registertype_id',
        'expiredate',
        'status',
        'approvedby',
        'rejection_reason',
    ];

    protected $casts = [
        'dob' => 'date',
        'expiredate' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function nationality()
    {
        return $this->belongsTo(Nationality::class);
    }

    public function profession()
    {
        return $this->belongsTo(Profession::class);
    }

    public function registertype()
    {
        return $this->belongsTo(Registertype::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approvedby');
    }

    public function documents()
    {
        return $this->hasMany(Customerhistoricaldatadocument::class);
    }
}
