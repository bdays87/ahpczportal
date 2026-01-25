<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customerhistoricaldataprofession extends Model
{
    protected $table = 'customerhistoricaldataprofessions';

    protected $fillable = [
        'customerhistoricaldata_id',
        'profession_id',
        'registrationnumber',
        'registrationyear',
        'practisingcertificatenumber',
        'registertype_id',
        'tire_id',
        'last_renewal_year',
        'last_renewal_year_cdp_points',
        'last_renewal_expire_date',
    ];

    protected $casts = [
        'last_renewal_expire_date' => 'date',
    ];

    public function customerhistoricaldata()
    {
        return $this->belongsTo(Customerhistoricaldata::class);
    }

    public function profession()
    {
        return $this->belongsTo(Profession::class);
    }

    public function registertype()
    {
        return $this->belongsTo(Registertype::class);
    }

    public function tire()
    {
        return $this->belongsTo(Tire::class);
    }

    public function documents()
    {
        return $this->hasMany(Customerhistoricaldatadocument::class);
    }
}
