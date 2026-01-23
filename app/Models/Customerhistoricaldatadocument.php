<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customerhistoricaldatadocument extends Model
{
    protected $table = 'customerhistoricaldatadocuments';

    protected $fillable = [
        'customerhistoricaldata_id',
        'file',
        'description',
    ];

    public function customerhistoricaldata()
    {
        return $this->belongsTo(Customerhistoricaldata::class);
    }
}
