<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Journal extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'author',
        'published_date',
        'link',
        'created_by',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($journal) {
            if (empty($journal->slug)) {
                $journal->slug = \Illuminate\Support\Str::slug($journal->title);
            }
        });

        static::updating(function ($journal) {
            if ($journal->isDirty('title') && empty($journal->slug)) {
                $journal->slug = \Illuminate\Support\Str::slug($journal->title);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'published_date' => 'date',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
