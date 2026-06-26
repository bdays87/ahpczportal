<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otherapplicationinstitutionimport extends Model
{
    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function otherservice()
    {
        return $this->belongsTo(Otherservice::class);
    }

    public function otherapplication()
    {
        return $this->belongsTo(Otherapplication::class);
    }
}
