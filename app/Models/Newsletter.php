<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Newsletter extends Model
{
    protected $fillable = [
        'title',
        'link',
        'published_date',
        'is_broadcasted',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'published_date' => 'date',
            'is_broadcasted' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
