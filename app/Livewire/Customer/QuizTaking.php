<?php

namespace App\Livewire\Customer;

use App\Interfaces\iquizInterface;
use App\Interfaces\iactivityInterface;
use Livewire\Component;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Auth;

class QuizTaking extends Component
{
    use Toast;

    public $breadcrumbs = [];
    protected $quizRepo;
    protected $activityRepo;

    // Quiz data
    public $enrollment_id;
    public $activity;
    public $quiz;
    public $questions;
    public $attempt;

    // Quiz state
    public $currentQuestionIndex = 0;
    public $answers = [];
    public $timeRemaining;
    public $quizStarted = false;
    public $quizCompleted = false;
    public $showResults = false;

    public function mount($enrollment_id)
    {
        $this->enrollment_id = $enrollment_id;
        $this->breadcrumbs = [
            ['label' => 'Dashboard', 'icon' => 'o-home', 'link' => route('dashboard')],
            ['label' => 'My Activities', 'link' => route('customer.activities')],
            ['label' => 'Quiz']
        ];
    }

    public function boot(iquizInterface $quizRepo, iactivityInterface $activityRepo)
    {
        $this->quizRepo = $quizRepo;
        $this->activityRepo = $activityRepo;
        $this->loadQuizData();
    }

    public function loadQuizData()
    {
        // Get enrollment and activity data
        $enrollment = \App\Models\ActivityEnrollment::with(['activity.quiz', 'customer'])->find($this->enrollment_id);
        
        if (!$enrollment || $enrollment->customer_id !== Auth::user()->customer->id) {
            abort(403, 'Unauthorized access to this quiz.');
        }

        $this->activity = $enrollment->activity;
        $this->quiz = $this->activity->quiz;

        if (!$this->quiz) {
            abort(404, 'Quiz not found for this activity.');
        }

        // Check if customer can attempt quiz
        if (!$this->quiz->canAttempt(Auth::user()->customer)) {
            $this->error('You have reached the maximum number of attempts for this quiz.');
            return;
        }

        // Check for existing in-progress attempt
        $this->attempt = \App\Models\CustomerQuizAttempt::where([
            'enrollment_id' => $this->enrollment_id,
            'customer_id' => Auth::user()->customer->id,
            'status' => 'IN_PROGRESS'
        ])->first();

        if ($this->attempt) {
            $this->quizStarted = true;
            $this->answers = $this->attempt->answers ?? [];
            $this->calculateTimeRemaining();
        }

        $this->questions = $this->quiz->getQuestionsForQuiz();
    }

    public function startQuiz()
    {
        $response = $this->quizRepo->startQuizAttempt($this->enrollment_id, Auth::user()->customer->id);
        
        if ($response['status'] === 'success') {
            $this->attempt = $response['data'];
            $this->quizStarted = true;
            $this->timeRemaining = $this->quiz->time_limit_minutes ? $this->quiz->time_limit_minutes * 60 : null;
            $this->success('Quiz started! Good luck!');
        } else {
            $this->error($response['message']);
        }
    }

    public function selectAnswer($questionId, $answerId, $isMultiple = false)
    {
        if (!$this->quizStarted || $this->quizCompleted) {
            return;
        }

        if ($isMultiple) {
            // Multiple choice - toggle answer
            if (!isset($this->answers[$questionId])) {
                $this->answers[$questionId] = [];
            }
            
            $key = array_search($answerId, $this->answers[$questionId]);
            if ($key !== false) {
                unset($this->answers[$questionId][$key]);
                $this->answers[$questionId] = array_values($this->answers[$questionId]);
            } else {
                $this->answers[$questionId][] = $answerId;
            }
        } else {
            // Single choice - replace answer
            $this->answers[$questionId] = [$answerId];
        }
    }

    public function nextQuestion()
    {
        if ($this->currentQuestionIndex < count($this->questions) - 1) {
            $this->currentQuestionIndex++;
        }
    }

    public function previousQuestion()
    {
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
        }
    }

    public function goToQuestion($index)
    {
        $this->currentQuestionIndex = $index;
    }

    public function submitQuiz()
    {
        if (!$this->quizStarted || $this->quizCompleted) {
            return;
        }

        $response = $this->quizRepo->submitQuizAttempt($this->attempt->id, $this->answers);
        
        if ($response['status'] === 'success') {
            $this->attempt = $response['data'];
            $this->quizCompleted = true;
            $this->showResults = true;
            $this->success('Quiz submitted successfully!');
        } else {
            $this->error($response['message']);
        }
    }

    public function calculateTimeRemaining()
    {
        if ($this->quiz->time_limit_minutes && $this->attempt && $this->attempt->started_at) {
            $elapsed = now()->diffInSeconds($this->attempt->started_at);
            $totalTime = $this->quiz->time_limit_minutes * 60;
            $this->timeRemaining = max(0, $totalTime - $elapsed);
            
            if ($this->timeRemaining <= 0 && !$this->quizCompleted) {
                $this->submitQuiz();
            }
        }
    }

    public function getCurrentQuestion()
    {
        return $this->questions[$this->currentQuestionIndex] ?? null;
    }

    public function getAnsweredQuestionsCount()
    {
        return count($this->answers);
    }

    public function isQuestionAnswered($questionId)
    {
        return isset($this->answers[$questionId]) && !empty($this->answers[$questionId]);
    }

    public function isAnswerSelected($questionId, $answerId)
    {
        return isset($this->answers[$questionId]) && in_array($answerId, $this->answers[$questionId]);
    }

    public function render()
    {
        return view('livewire.customer.quiz-taking', [
            'currentQuestion' => $this->getCurrentQuestion(),
            'totalQuestions' => count($this->questions),
            'answeredCount' => $this->getAnsweredQuestionsCount(),
        ]);
    }
}