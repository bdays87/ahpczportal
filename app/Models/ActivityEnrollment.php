<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ActivityEnrollment extends Model
{
    protected $fillable = [
        'activity_id',
        'customer_id',
        'status',
        'enrolled_at',
        'started_at',
        'completed_at',
        'points_earned'
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'points_earned' => 'integer',
    ];

    /**
     * The activity this enrollment belongs to
     */
    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    /**
     * The customer who enrolled
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Quiz attempts for this enrollment
     */
    public function quizAttempts(): HasMany
    {
        return $this->hasMany(CustomerQuizAttempt::class, 'enrollment_id');
    }

    /**
     * Check if enrollment is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'COMPLETED';
    }

    /**
     * Mark as started
     */
    public function markAsStarted(): void
    {
        if ($this->status === 'ENROLLED') {
            $this->update([
                'status' => 'IN_PROGRESS',
                'started_at' => now()
            ]);
        }
    }

    /**
     * Mark as completed
     */
    public function markAsCompleted(int $pointsEarned = 0): void
    {
        $this->update([
            'status' => 'COMPLETED',
            'completed_at' => now(),
            'points_earned' => $pointsEarned
        ]);
    }

    /**
     * Get the best quiz attempt
     */
    public function getBestQuizAttempt(): ?CustomerQuizAttempt
    {
        return $this->quizAttempts()
            ->where('status', 'COMPLETED')
            ->orderBy('score', 'desc')
            ->orderBy('percentage', 'desc')
            ->first();
    }
}