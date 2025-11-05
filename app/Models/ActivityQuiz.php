<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ActivityQuiz extends Model
{
    protected $fillable = [
        'activity_id',
        'title',
        'description',
        'pass_percentage',
        'time_limit_minutes',
        'max_attempts',
        'randomize_questions',
        'status'
    ];

    protected $casts = [
        'pass_percentage' => 'integer',
        'time_limit_minutes' => 'integer',
        'max_attempts' => 'integer',
        'randomize_questions' => 'boolean',
    ];

    /**
     * The activity this quiz belongs to
     */
    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    /**
     * Questions in this quiz
     */
    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class, 'quiz_id');
    }

    /**
     * Customer attempts for this quiz
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(CustomerQuizAttempt::class, 'quiz_id');
    }

    /**
     * Get active questions
     */
    public function activeQuestions(): HasMany
    {
        return $this->questions()->where('is_active', true)->orderBy('order');
    }

    /**
     * Get questions for taking quiz (randomized if enabled)
     */
    public function getQuestionsForQuiz()
    {
        $questions = $this->activeQuestions()->with('answers')->get();
        
        if ($this->randomize_questions) {
            return $questions->shuffle();
        }
        
        return $questions;
    }

    /**
     * Calculate total possible points
     */
    public function getTotalPoints(): int
    {
        return $this->activeQuestions()->sum('points');
    }

    /**
     * Check if customer can attempt quiz
     */
    public function canAttempt(Customer $customer): bool
    {
        $attempts = $this->attempts()
            ->where('customer_id', $customer->id)
            ->where('status', 'COMPLETED')
            ->count();
            
        return $attempts < $this->max_attempts;
    }

    /**
     * Get customer's attempts count
     */
    public function getAttemptsCount(Customer $customer): int
    {
        return $this->attempts()
            ->where('customer_id', $customer->id)
            ->where('status', 'COMPLETED')
            ->count();
    }
}