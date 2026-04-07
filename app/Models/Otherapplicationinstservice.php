<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otherapplicationinstservice extends Model
{
    protected $fillable = ['otherapplication_id', 'name', 'description', 'status'];

    public function otherapplication(){
        return $this->belongsTo(Otherapplication::class);
    }
}
