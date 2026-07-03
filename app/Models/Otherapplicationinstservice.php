<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otherapplicationinstservice extends Model
{
    protected $fillable = ['otherapplication_id', 'parent_id', 'name', 'description', 'status'];

    public function otherapplication(){
        return $this->belongsTo(Otherapplication::class);
    }

    public function subtests(){
        return $this->hasMany(Otherapplicationinstservice::class, 'parent_id');
    }

    public function parent(){
        return $this->belongsTo(Otherapplicationinstservice::class, 'parent_id');
    }
}
