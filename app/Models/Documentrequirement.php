<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documentrequirement extends Model
{
    public function document()
    {
        return $this->belongsTo(Document::class);
    }
    public function tire()
    {
        return $this->belongsTo(Tire::class);
    }
    public function customertype()
    {
        return $this->belongsTo(Customertype::class);
    }
    public function applicationtype()
    {
        return $this->belongsTo(Applicationtype::class);
    }
}
