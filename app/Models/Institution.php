<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    
    protected $fillable = ['name', 'accredited'];
    public function qualifications()
    {
        return $this->hasMany(Qualification::class);
    }
}
