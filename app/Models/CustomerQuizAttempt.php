<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerQuizAttempt extends Model
{
    protected $fillable = [
        'enrollment_id',
        'quiz_id',
        'customer_id',
        'attempt_number',
        'answers',
        'score',
        'total_questions',
        'correct_answers',
        'percentage',
        'passed',
        'time_taken_minutes',
        'started_at',
        'completed_at',
        'status'
    ];

    protected $casts = [
        'answers' => 'array',
        'score' => 'integer',
        'total_questions' => 'integer',
        'correct_answers' => 'integer',
        'percentage' => 'decimal:2',
        'passed' => 'boolean',
        'time_taken_minutes' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * The enrollment this attempt belongs to
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(ActivityEnrollment::class, 'enrollment_id');
    }

    /**
     * The quiz being attempted
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(ActivityQuiz::class, 'quiz_id');
    }

    /**
     * The customer making the attempt
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Calculate and update the attempt results
     */
    public function calculateResults(array $answers): void
    {
        $quiz = $this->quiz()->with('questions.answers')->first();
        $score = 0;
        $correctAnswers = 0;
        $totalQuestions = $quiz->questions->count();

        foreach ($answers as $questionId => $answerIds) {
            $question = $quiz->questions->find($questionId);
            if ($question && $question->isCorrect($answerIds)) {
                $score += $question->points;
                $correctAnswers++;
            }
        }

        $percentage = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;
        $passed = $percentage >= $quiz->pass_percentage;

        $this->update([
            'answers' => $answers,
            'score' => $score,
            'total_questions' => $totalQuestions,
            'correct_answers' => $correctAnswers,
            'percentage' => $percentage,
            'passed' => $passed,
            'completed_at' => now(),
            'status' => 'COMPLETED'
        ]);

        // If passed, update enrollment with points earned
        if ($passed) {
            $this->enrollment->markAsCompleted($score);
        }
    }

    /**
     * Calculate time taken
     */
    public function calculateTimeTaken(): void
    {
        if ($this->started_at && $this->completed_at) {
            $timeTaken = $this->started_at->diffInMinutes($this->completed_at);
            $this->update(['time_taken_minutes' => $timeTaken]);
        }
    }

    /**
     * Check if attempt is in progress
     */
    public function isInProgress(): bool
    {
        return $this->status === 'IN_PROGRESS';
    }

    /**
     * Check if attempt is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'COMPLETED';
    }
}