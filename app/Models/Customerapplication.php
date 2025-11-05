<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Customerapplication extends Model
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'certificate_expiry_date' => 'date',
        'registration_date' => 'date',
    ];

    public function customerprofession()
    {
        return $this->belongsTo(Customerprofession::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function applicationtype()
    {
        return $this->belongsTo(Applicationtype::class);
    }

    public function registertype()
    {
        return $this->belongsTo(Registertype::class);
    }

    public function documents()
    {
        return $this->hasMany(Customerapplicationdocument::class);
    }

    /**
     * Check if the certificate is expired based on the certificate_expiry_date
     *
     * @return bool True if the certificate is expired, false if it's valid
     */
    public function isExpired(): bool
    {
        if (! $this->certificate_expiry_date) {
            return true; // Consider as not expired if no expiry date is set
        }

        return Carbon::parse($this->certificate_expiry_date)->isPast();
    }

    /**
     * Check if the certificate is valid based on the certificate_expiry_date
     *
     * @return bool True if the certificate is valid, false if it's expired
     */
    public function isValid(): bool
    {
        return ! $this->isExpired();
    }

    /**
     * Get the certificate status as a string
     *
     * @return string 'Valid' or 'Expired'
     */
    public function getCertificateStatusAttribute(): string
    {
        return $this->isExpired() ? 'Expired' : 'Valid';
    }

    public function mycdps()
    {
        return $this->hasMany(Mycdp::class);
    }
}
