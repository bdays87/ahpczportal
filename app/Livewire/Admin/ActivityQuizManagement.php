<?php

namespace App\Livewire\Admin;

use App\Interfaces\iquizInterface;
use App\Interfaces\iactivityInterface;
use Livewire\Component;
use Mary\Traits\Toast;

class ActivityQuizManagement extends Component
{
    use Toast;

    public $breadcrumbs = [];
    protected $quizRepo;
    protected $activityRepo;

    // Current activity and quiz
    public $activity_id;
    public $activity;
    public $quiz;

    // Modal states
    public $quizModal = false;
    public $questionModal = false;
    public $viewModal = false;

    // Quiz form properties
    public $quiz_id;
    public $title;
    public $description;
    public $pass_percentage = 70;
    public $time_limit_minutes;
    public $max_attempts = 3;
    public $randomize_questions = false;
    public $status = 'ACTIVE';

    // Question form properties
    public $question_id;
    public $question;
    public $question_type = 'SINGLE_CHOICE';
    public $points = 1;
    public $order = 0;
    public $is_active = true;
    public $answers = [];

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'pass_percentage' => 'required|integer|min:1|max:100',
        'time_limit_minutes' => 'nullable|integer|min:1',
        'max_attempts' => 'required|integer|min:1|max:10',
        'randomize_questions' => 'boolean',
        'status' => 'required|in:ACTIVE,INACTIVE',
    ];

    protected $questionRules = [
        'question' => 'required|string',
        'question_type' => 'required|in:MULTIPLE_CHOICE,SINGLE_CHOICE,TRUE_FALSE',
        'points' => 'required|integer|min:1|max:100',
        'order' => 'required|integer|min:0',
        'is_active' => 'boolean',
        'answers' => 'required|array|min:2',
        'answers.*.answer_text' => 'required|string',
        'answers.*.is_correct' => 'boolean',
        'answers.*.order' => 'nullable|integer|min:0',
    ];

    public function mount($activity_id)
    {
        $this->activity_id = $activity_id;
        $this->breadcrumbs = [
            ['label' => 'Dashboard', 'icon' => 'o-home', 'link' => route('dashboard')],
            ['label' => 'Activities', 'link' => route('admin.activities')],
            ['label' => 'Quiz Management']
        ];
    }

    public function boot(iquizInterface $quizRepo, iactivityInterface $activityRepo)
    {
        $this->quizRepo = $quizRepo;
        $this->activityRepo = $activityRepo;
        $this->loadActivity();
    }

    public function loadActivity()
    {
        $this->activity = $this->activityRepo->get($this->activity_id);
        if ($this->activity && $this->activity->quiz) {
            $this->quiz = $this->quizRepo->getQuiz($this->activity->quiz->id);
        } else {
            $this->quiz = null;
        }
    }

    public function openQuizModal()
    {
        if ($this->quiz) {
            // Edit existing quiz
            $this->quiz_id = $this->quiz->id;
            $this->title = $this->quiz->title;
            $this->description = $this->quiz->description;
            $this->pass_percentage = $this->quiz->pass_percentage;
            $this->time_limit_minutes = $this->quiz->time_limit_minutes;
            $this->max_attempts = $this->quiz->max_attempts;
            $this->randomize_questions = $this->quiz->randomize_questions;
            $this->status = $this->quiz->status;
        } else {
            // Create new quiz
            $this->resetQuizForm();
            $this->title = $this->activity->title . ' Quiz';
        }
        $this->quizModal = true;
    }

    public function saveQuiz()
    {
        $this->validate();

        $data = [
            'activity_id' => $this->activity_id,
            'title' => $this->title,
            'description' => $this->description,
            'pass_percentage' => $this->pass_percentage,
            'time_limit_minutes' => $this->time_limit_minutes,
            'max_attempts' => $this->max_attempts,
            'randomize_questions' => $this->randomize_questions,
            'status' => $this->status,
        ];

        if ($this->quiz_id) {
            $response = $this->quizRepo->updateQuiz($this->quiz_id, $data);
        } else {
            $response = $this->quizRepo->createQuiz($data);
        }

        if ($response['status'] === 'success') {
            $this->success($response['message']);
            $this->loadActivity();
            $this->closeModals();
        } else {
            $this->error($response['message']);
        }
    }

    public function openQuestionModal($questionId = null)
    {
        if (!$this->quiz) {
            $this->error('Please create a quiz first before adding questions.');
            return;
        }

        if ($questionId) {
            // Edit existing question
            $question = collect($this->quiz->questions)->firstWhere('id', $questionId);
            if ($question) {
                $this->question_id = $question->id;
                $this->question = $question->question;
                $this->question_type = $question->type;
                $this->points = $question->points;
                $this->order = $question->order;
                $this->is_active = $question->is_active;
                $this->answers = $question->answers->map(function ($answer) {
                    return [
                        'id' => $answer->id,
                        'answer_text' => $answer->answer_text,
                        'is_correct' => $answer->is_correct,
                        'order' => $answer->order,
                    ];
                })->toArray();
            }
        } else {
            // Create new question
            $this->resetQuestionForm();
            $this->order = $this->quiz->questions->count();
        }
        $this->questionModal = true;
    }

    public function saveQuestion()
    {
        if (!$this->quiz) {
            $this->error('Quiz not found. Please refresh the page.');
            return;
        }

        try {
            $this->validate($this->questionRules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->error('Validation failed: ' . implode(', ', $e->validator->errors()->all()));
            return;
        }

        // Validate at least one correct answer
        $correctAnswers = collect($this->answers)->where('is_correct', true)->count();
        if ($correctAnswers === 0) {
            $this->error('At least one answer must be marked as correct.');
            return;
        }

        // For single choice and true/false, ensure only one correct answer
        if (in_array($this->question_type, ['SINGLE_CHOICE', 'TRUE_FALSE']) && $correctAnswers > 1) {
            $this->error('Only one answer can be correct for ' . $this->question_type . ' questions.');
            return;
        }

        // Ensure all answers have the required order field and proper boolean values
        foreach ($this->answers as $index => $answer) {
            if (!isset($answer['order'])) {
                $this->answers[$index]['order'] = $index;
            }
            // Ensure is_correct is properly cast to boolean
            $this->answers[$index]['is_correct'] = (bool) ($answer['is_correct'] ?? false);
        }

        $questionData = [
            'question' => $this->question,
            'type' => $this->question_type,
            'points' => $this->points,
            'order' => $this->order,
            'is_active' => $this->is_active,
            'answers' => $this->answers,
        ];

        if ($this->question_id) {
            $response = $this->quizRepo->updateQuestion($this->question_id, $questionData);
        } else {
            $response = $this->quizRepo->addQuestion($this->quiz->id, $questionData);
        }

        if ($response['status'] === 'success') {
            $this->success($response['message']);
            $this->loadActivity();
            $this->closeModals();
        } else {
            $this->error($response['message']);
        }
    }

    public function deleteQuestion($questionId)
    {
        $response = $this->quizRepo->deleteQuestion($questionId);
        
        if ($response['status'] === 'success') {
            $this->success($response['message']);
            $this->loadActivity();
        } else {
            $this->error($response['message']);
        }
    }

    public function addAnswer()
    {
        $this->answers[] = [
            'answer_text' => '',
            'is_correct' => false,
            'order' => count($this->answers),
        ];
    }

    public function removeAnswer($index)
    {
        if (count($this->answers) > 2) {
            unset($this->answers[$index]);
            $this->answers = array_values($this->answers);
        } else {
            $this->error('A question must have at least 2 answers.');
        }
    }

    public function updatedQuestionType()
    {
        if ($this->question_type === 'TRUE_FALSE') {
            $this->answers = [
                ['answer_text' => 'True', 'is_correct' => false, 'order' => 0],
                ['answer_text' => 'False', 'is_correct' => false, 'order' => 1],
            ];
        } elseif (count($this->answers) === 0) {
            $this->addAnswer();
            $this->addAnswer();
        }
    }

    public function closeModals()
    {
        $this->quizModal = false;
        $this->questionModal = false;
        $this->viewModal = false;
        $this->resetQuizForm();
        $this->resetQuestionForm();
    }

    private function resetQuizForm()
    {
        $this->reset([
            'quiz_id', 'title', 'description', 'pass_percentage', 
            'time_limit_minutes', 'max_attempts', 'randomize_questions', 'status'
        ]);
        $this->pass_percentage = 70;
        $this->max_attempts = 3;
        $this->status = 'ACTIVE';
    }

    private function resetQuestionForm()
    {
        $this->reset([
            'question_id', 'question', 'question_type', 'points', 
            'order', 'is_active', 'answers'
        ]);
        $this->question_type = 'SINGLE_CHOICE';
        $this->points = 1;
        $this->is_active = true;
        $this->answers = [
            ['answer_text' => '', 'is_correct' => false, 'order' => 0],
            ['answer_text' => '', 'is_correct' => false, 'order' => 1],
        ];
    }

    public function render()
    {
        return view('livewire.admin.activity-quiz-management');
    }
}