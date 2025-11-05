<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizQuestion extends Model
{
    protected $fillable = [
        'quiz_id',
        'question',
        'type',
        'points',
        'order',
        'is_active'
    ];

    protected $casts = [
        'points' => 'integer',
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * The quiz this question belongs to
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(ActivityQuiz::class, 'quiz_id');
    }

    /**
     * Answer options for this question
     */
    public function answers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class, 'question_id')->orderBy('order');
    }

    /**
     * Get correct answers
     */
    public function correctAnswers(): HasMany
    {
        return $this->answers()->where('is_correct', true);
    }

    /**
     * Check if given answer IDs are correct
     */
    public function isCorrect(array $answerIds): bool
    {
        $correctAnswerIds = $this->correctAnswers()->pluck('id')->toArray();
        
        // For single choice questions, check if exactly one correct answer is selected
        if ($this->type === 'SINGLE_CHOICE' || $this->type === 'TRUE_FALSE') {
            return count($answerIds) === 1 && in_array($answerIds[0], $correctAnswerIds);
        }
        
        // For multiple choice, check if all correct answers are selected and no incorrect ones
        if ($this->type === 'MULTIPLE_CHOICE') {
            sort($answerIds);
            sort($correctAnswerIds);
            return $answerIds === $correctAnswerIds;
        }
        
        return false;
    }

    /**
     * Get points for given answer IDs
     */
    public function getPoints(array $answerIds): int
    {
        return $this->isCorrect($answerIds) ? $this->points : 0;
    }
}