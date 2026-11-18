<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Practitionermachine extends Model
{
    protected $fillable = [
        'uuid', 'customer_id', 'customerprofession_id', 'otherapplication_id',
        'name', 'make', 'model', 'serial_number', 'asset_tag', 'location',
        'date_from', 'date_to', 'status', 'notes', 'created_by',
    ];

    protected $casts = [
        'date_from' => 'date',
        'date_to' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function customerprofession()
    {
        return $this->belongsTo(Customerprofession::class);
    }

    public function otherapplication()
    {
        return $this->belongsTo(Otherapplication::class);
    }
}
