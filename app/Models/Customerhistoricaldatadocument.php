<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customerhistoricaldatadocument extends Model
{
    protected $table = 'customerhistoricaldatadocuments';

    protected $fillable = [
        'customerhistoricaldataprofession_id',
        'file',
        'description',
    ];

    public function customerhistoricaldataprofession()
    {
        return $this->belongsTo(Customerhistoricaldataprofession::class);
    }
}
