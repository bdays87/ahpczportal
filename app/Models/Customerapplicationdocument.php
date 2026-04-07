<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customerapplicationdocument extends Model
{
    protected $fillable = [
        'customerapplication_id',
        'document_id',
        'file',
        'status',
        'verifiedby',
        'remarks',
    ];

    public function document(){
        return $this->belongsTo(Document::class);
    }
    public function customerapplication(){
        return $this->belongsTo(Customerapplication::class);
    }
}
