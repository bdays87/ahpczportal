<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customerregistration extends Model
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'certificateexpirydate' => 'date',
        'registrationdate' => 'date',
    ];

    public function customerprofession()
    {
        return $this->belongsTo(Customerprofession::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
