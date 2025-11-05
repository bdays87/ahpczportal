<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mycdp extends Model
{
    protected $fillable = [
        'customerprofession_id',
        'title',
        'year',
        'description',
        'type',
        'points',
        'duration',
        'durationunit',
        'user_id',
        'status',
        'comment',
        'assessment_notes',
        'assessed_by',
        'assessed_at'
    ];

    protected $casts = [
        'assessed_at' => 'datetime',
    ];

    public function customerprofession(){
        return $this->belongsTo(Customerprofession::class);
    }
    
    public function attachments(){
        return $this->hasMany(Mycdpattachment::class);
    }
}
