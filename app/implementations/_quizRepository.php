<?php

namespace App\implementations;

use App\Interfaces\iquizInterface;
use App\Models\ActivityEnrollment;
use App\Models\ActivityQuiz;
use App\Models\CustomerQuizAttempt;
use App\Models\QuizAnswer;
use App\Models\QuizQuestion;
use Illuminate\Support\Facades\DB;

class _quizRepository implements iquizInterface
{
    protected $quiz;

    protected $question;

    protected $answer;

    protected $attempt;

    protected $enrollment;

    public function __construct(
        ActivityQuiz $quiz,
        QuizQuestion $question,
        QuizAnswer $answer,
        CustomerQuizAttempt $attempt,
        ActivityEnrollment $enrollment
    ) {
        $this->quiz = $quiz;
        $this->question = $question;
        $this->answer = $answer;
        $this->attempt = $attempt;
        $this->enrollment = $enrollment;
    }

    public function createQuiz(array $data)
    {
        try {
            $quiz = $this->quiz->create($data);

            return ['status' => 'success', 'message' => 'Quiz created successfully', 'data' => $quiz];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function updateQuiz(int $id, array $data)
    {
        try {
            $quiz = $this->quiz->find($id);
            if (! $quiz) {
                return ['status' => 'error', 'message' => 'Quiz not found'];
            }

            $quiz->update($data);

            return ['status' => 'success', 'message' => 'Quiz updated successfully', 'data' => $quiz];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function deleteQuiz(int $id)
    {
        try {
            $quiz = $this->quiz->find($id);
            if (! $quiz) {
                return ['status' => 'error', 'message' => 'Quiz not found'];
            }

            // Check if there are attempts
            if ($quiz->attempts()->exists()) {
                return ['status' => 'error', 'message' => 'Cannot delete quiz with existing attempts'];
            }

            $quiz->delete();

            return ['status' => 'success', 'message' => 'Quiz deleted successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function getQuiz(int $id)
    {
        return $this->quiz->with(['activity', 'questions.answers', 'attempts'])->find($id);
    }

    public function addQuestion(int $quizId, array $questionData)
    {
        try {
            $quiz = $this->quiz->find($quizId);
            if (! $quiz) {
                return ['status' => 'error', 'message' => 'Quiz not found'];
            }

            DB::beginTransaction();

            // Extract answers from questionData before creating question
            $answers = $questionData['answers'] ?? [];
            unset($questionData['answers']);

            $questionData['quiz_id'] = $quizId;
            $question = $this->question->create($questionData);

            // Add answers if provided
            if (! empty($answers) && is_array($answers)) {
                foreach ($answers as $answerData) {
                    $answerData['question_id'] = $question->id;
                    $this->answer->create($answerData);
                }
            }

            DB::commit();

            return ['status' => 'success', 'message' => 'Question added successfully', 'data' => $question];
        } catch (\Exception $e) {
            DB::rollBack();

            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function updateQuestion(int $questionId, array $questionData)
    {
        try {
            $question = $this->question->find($questionId);
            if (! $question) {
                return ['status' => 'error', 'message' => 'Question not found'];
            }

            DB::beginTransaction();

            // Extract answers from questionData before updating question
            $answers = $questionData['answers'] ?? [];
            unset($questionData['answers']);

            // Update question fields
            $question->update($questionData);

            // Handle answers update
            if (! empty($answers) && is_array($answers)) {
                // Get existing answer IDs
                $existingAnswerIds = $question->answers()->pluck('id')->toArray();
                $submittedAnswerIds = [];

                foreach ($answers as $answerData) {
                    if (isset($answerData['id']) && in_array($answerData['id'], $existingAnswerIds)) {
                        // Update existing answer
                        $answerId = $answerData['id'];
                        unset($answerData['id']);
                        $this->answer->where('id', $answerId)->update($answerData);
                        $submittedAnswerIds[] = $answerId;
                    } else {
                        // Create new answer
                        unset($answerData['id']);
                        $answerData['question_id'] = $questionId;
                        $newAnswer = $this->answer->create($answerData);
                        $submittedAnswerIds[] = $newAnswer->id;
                    }
                }

                // Delete answers that were removed
                $answersToDelete = array_diff($existingAnswerIds, $submittedAnswerIds);
                if (! empty($answersToDelete)) {
                    $this->answer->whereIn('id', $answersToDelete)->delete();
                }
            }

            DB::commit();

            return ['status' => 'success', 'message' => 'Question updated successfully', 'data' => $question->fresh()];
        } catch (\Exception $e) {
            DB::rollBack();

            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function deleteQuestion(int $questionId)
    {
        try {
            $question = $this->question->find($questionId);
            if (! $question) {
                return ['status' => 'error', 'message' => 'Question not found'];
            }

            $question->delete();

            return ['status' => 'success', 'message' => 'Question deleted successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function addAnswers(int $questionId, array $answers)
    {
        try {
            $question = $this->question->find($questionId);
            if (! $question) {
                return ['status' => 'error', 'message' => 'Question not found'];
            }

            foreach ($answers as $answerData) {
                $answerData['question_id'] = $questionId;
                $this->answer->create($answerData);
            }

            return ['status' => 'success', 'message' => 'Answers added successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function updateAnswer(int $answerId, array $answerData)
    {
        try {
            $answer = $this->answer->find($answerId);
            if (! $answer) {
                return ['status' => 'error', 'message' => 'Answer not found'];
            }

            $answer->update($answerData);

            return ['status' => 'success', 'message' => 'Answer updated successfully', 'data' => $answer];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function deleteAnswer(int $answerId)
    {
        try {
            $answer = $this->answer->find($answerId);
            if (! $answer) {
                return ['status' => 'error', 'message' => 'Answer not found'];
            }

            $answer->delete();

            return ['status' => 'success', 'message' => 'Answer deleted successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function startQuizAttempt(int $enrollmentId, int $customerId)
    {
        try {
            $enrollment = $this->enrollment->with('activity.quiz')->find($enrollmentId);
            if (! $enrollment) {
                return ['status' => 'error', 'message' => 'Enrollment not found'];
            }

            $quiz = $enrollment->activity->quiz;
            if (! $quiz) {
                return ['status' => 'error', 'message' => 'Quiz not found for this activity'];
            }

            // Check if customer can attempt
            if (! $quiz->canAttempt($enrollment->customer)) {
                return ['status' => 'error', 'message' => 'Maximum attempts reached'];
            }

            // Check for existing in-progress attempt
            $existingAttempt = $this->attempt->where([
                'enrollment_id' => $enrollmentId,
                'customer_id' => $customerId,
                'status' => 'IN_PROGRESS',
            ])->first();

            if ($existingAttempt) {
                return ['status' => 'success', 'message' => 'Resuming existing attempt', 'data' => $existingAttempt];
            }

            // Create new attempt
            $attemptNumber = $this->attempt->where([
                'quiz_id' => $quiz->id,
                'customer_id' => $customerId,
            ])->count() + 1;

            $attempt = $this->attempt->create([
                'enrollment_id' => $enrollmentId,
                'quiz_id' => $quiz->id,
                'customer_id' => $customerId,
                'attempt_number' => $attemptNumber,
                'started_at' => now(),
                'status' => 'IN_PROGRESS',
            ]);

            // Mark enrollment as started
            $enrollment->markAsStarted();

            return ['status' => 'success', 'message' => 'Quiz attempt started', 'data' => $attempt];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function submitQuizAttempt(int $attemptId, array $answers)
    {
        try {
            $attempt = $this->attempt->with(['quiz', 'enrollment'])->find($attemptId);
            if (! $attempt) {
                return ['status' => 'error', 'message' => 'Attempt not found'];
            }

            if ($attempt->status !== 'IN_PROGRESS') {
                return ['status' => 'error', 'message' => 'Attempt is not in progress'];
            }

            // Calculate results
            $attempt->calculateResults($answers);
            $attempt->calculateTimeTaken();

            return ['status' => 'success', 'message' => 'Quiz submitted successfully', 'data' => $attempt->fresh()];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function getQuizAttempts(int $customerId, ?int $quizId = null)
    {
        $query = $this->attempt->with(['quiz', 'enrollment.activity'])
            ->where('customer_id', $customerId);

        if ($quizId) {
            $query->where('quiz_id', $quizId);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function getAttemptResults(int $attemptId)
    {
        return $this->attempt->with([
            'quiz.questions.answers',
            'enrollment.activity',
        ])->find($attemptId);
    }

    public function canAttemptQuiz(int $customerId, int $quizId)
    {
        $quiz = $this->quiz->find($quizId);
        $customer = \App\Models\Customer::find($customerId);

        return $quiz && $customer && $quiz->canAttempt($customer);
    }

    public function getQuizStatistics(int $quizId)
    {
        $attempts = $this->attempt->where('quiz_id', $quizId)
            ->where('status', 'COMPLETED');

        $totalAttempts = $attempts->count();
        $passedAttempts = $attempts->where('passed', true)->count();
        $averageScore = $attempts->avg('percentage');
        $averageTime = $attempts->avg('time_taken_minutes');

        return [
            'total_attempts' => $totalAttempts,
            'passed_attempts' => $passedAttempts,
            'failed_attempts' => $totalAttempts - $passedAttempts,
            'pass_rate' => $totalAttempts > 0 ? round(($passedAttempts / $totalAttempts) * 100, 2) : 0,
            'average_score' => round($averageScore, 2),
            'average_time_minutes' => round($averageTime, 2),
        ];
    }
}
