<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />

    @if($quiz && !$quizCompleted)
    <x-card title="{{ $activity->title }} - Quiz" separator class="border-2 border-gray-200">
        
        @if(!$quizStarted)
        <!-- Quiz Instructions -->
        <div class="text-center py-8">
            <div class="max-w-2xl mx-auto">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ $quiz->title }}</h2>
                @if($quiz->description)
                    <p class="text-gray-600 mb-6">{{ $quiz->description }}</p>
                @endif
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-blue-900 mb-4">Quiz Instructions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-blue-800">
                        <div class="flex items-center">
                            <x-icon name="o-question-mark-circle" class="w-5 h-5 mr-2" />
                            <span>{{ count($questions) }} Questions</span>
                        </div>
                        <div class="flex items-center">
                            <x-icon name="o-star" class="w-5 h-5 mr-2" />
                            <span>Pass Rate: {{ $quiz->pass_percentage }}%</span>
                        </div>
                        @if($quiz->time_limit_minutes)
                        <div class="flex items-center">
                            <x-icon name="o-clock" class="w-5 h-5 mr-2" />
                            <span>Time Limit: {{ $quiz->time_limit_minutes }} minutes</span>
                        </div>
                        @endif
                        <div class="flex items-center">
                            <x-icon name="o-arrow-path" class="w-5 h-5 mr-2" />
                            <span>Attempts: {{ $quiz->getAttemptsCount(Auth::user()->customer) }}/{{ $quiz->max_attempts }}</span>
                        </div>
                    </div>
                </div>
                
                <x-button 
                    label="Start Quiz" 
                    class="btn-primary btn-lg" 
                    wire:click="startQuiz"
                    spinner="startQuiz" />
            </div>
        </div>
        
        @else
        <!-- Quiz Content -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Question Navigation Sidebar -->
            <div class="lg:col-span-1">
                <div class="sticky top-4">
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <h3 class="font-semibold text-gray-900 mb-3">Progress</h3>
                        <div class="text-sm text-gray-600 mb-2">
                            {{ $answeredCount }}/{{ $totalQuestions }} answered
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" 
                                 style="width: {{ ($answeredCount / $totalQuestions) * 100 }}%"></div>
                        </div>
                    </div>
                    
                    @if($quiz->time_limit_minutes)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                        <h3 class="font-semibold text-yellow-800 mb-2">Time Remaining</h3>
                        <div class="text-2xl font-bold text-yellow-600" id="timer">
                            {{ gmdate('H:i:s', $timeRemaining) }}
                        </div>
                    </div>
                    @endif
                    
                    <div class="bg-white rounded-lg border p-4">
                        <h3 class="font-semibold text-gray-900 mb-3">Questions</h3>
                        <div class="grid grid-cols-5 gap-2">
                            @foreach($questions as $index => $question)
                            <button 
                                wire:click="goToQuestion({{ $index }})"
                                class="w-8 h-8 rounded text-xs font-medium
                                    @if($index === $currentQuestionIndex) bg-blue-600 text-white
                                    @elseif($this->isQuestionAnswered($question->id)) bg-green-100 text-green-800 border border-green-300
                                    @else bg-gray-100 text-gray-600 hover:bg-gray-200
                                    @endif">
                                {{ $index + 1 }}
                            </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main Question Area -->
            <div class="lg:col-span-3">
                @if($currentQuestion)
                <div class="bg-white rounded-lg border p-6">
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-sm font-medium text-gray-500">
                                Question {{ $currentQuestionIndex + 1 }} of {{ $totalQuestions }}
                            </span>
                            <span class="badge badge-primary">{{ $currentQuestion->points }} pts</span>
                        </div>
                        
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">
                            {{ $currentQuestion->question }}
                        </h2>
                        
                        <div class="space-y-3">
                            @foreach($currentQuestion->answers as $answer)
                            <label class="flex items-start space-x-3 p-4 border rounded-lg cursor-pointer hover:bg-gray-50
                                {{ $this->isAnswerSelected($currentQuestion->id, $answer->id) ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                                <input 
                                    type="{{ $currentQuestion->type === 'MULTIPLE_CHOICE' ? 'checkbox' : 'radio' }}"
                                    wire:click="selectAnswer({{ $currentQuestion->id }}, {{ $answer->id }}, {{ $currentQuestion->type === 'MULTIPLE_CHOICE' ? 'true' : 'false' }})"
                                    {{ $this->isAnswerSelected($currentQuestion->id, $answer->id) ? 'checked' : '' }}
                                    class="{{ $currentQuestion->type === 'MULTIPLE_CHOICE' ? 'checkbox' : 'radio' }}" />
                                <span class="text-gray-900 flex-1">{{ $answer->answer_text }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Navigation Buttons -->
                    <div class="flex justify-between items-center pt-6 border-t">
                        <x-button 
                            label="Previous" 
                            class="btn-outline"
                            wire:click="previousQuestion"
                            :disabled="$currentQuestionIndex === 0" />
                            
                        <div class="flex space-x-3">
                            @if($currentQuestionIndex === $totalQuestions - 1)
                            <x-button 
                                label="Submit Quiz" 
                                class="btn-success"
                                wire:click="submitQuiz"
                                wire:confirm="Are you sure you want to submit your quiz? This action cannot be undone."
                                spinner="submitQuiz" />
                            @else
                            <x-button 
                                label="Next" 
                                class="btn-primary"
                                wire:click="nextQuestion" />
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif
    </x-card>
    
    @elseif($quizCompleted && $showResults)
    <!-- Results Screen -->
    <x-card title="Quiz Results" separator class="border-2 border-gray-200">
        <div class="text-center py-8">
            <div class="max-w-2xl mx-auto">
                @if($attempt->passed)
                <div class="mb-6">
                    <x-icon name="o-check-circle" class="w-16 h-16 mx-auto text-green-500 mb-4" />
                    <h2 class="text-3xl font-bold text-green-600 mb-2">Congratulations!</h2>
                    <p class="text-gray-600">You have successfully passed the quiz.</p>
                </div>
                @else
                <div class="mb-6">
                    <x-icon name="o-x-circle" class="w-16 h-16 mx-auto text-red-500 mb-4" />
                    <h2 class="text-3xl font-bold text-red-600 mb-2">Quiz Not Passed</h2>
                    <p class="text-gray-600">You need {{ $quiz->pass_percentage }}% to pass. Don't give up!</p>
                </div>
                @endif
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">{{ $attempt->score }}</div>
                        <div class="text-sm text-blue-600">Points Earned</div>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">{{ $attempt->correct_answers }}</div>
                        <div class="text-sm text-green-600">Correct Answers</div>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-yellow-600">{{ number_format($attempt->percentage, 1) }}%</div>
                        <div class="text-sm text-yellow-600">Percentage</div>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-purple-600">{{ $attempt->time_taken_minutes }}</div>
                        <div class="text-sm text-purple-600">Minutes Taken</div>
                    </div>
                </div>
                
                <div class="flex justify-center space-x-4">
                    <x-button 
                        label="Back to Activities" 
                        class="btn-primary"
                        :link="route('customer.activities')" />
                    @if(!$attempt->passed && $quiz->canAttempt(Auth::user()->customer))
                    <x-button 
                        label="Try Again" 
                        class="btn-outline"
                        wire:click="$refresh" />
                    @endif
                </div>
            </div>
        </div>
    </x-card>
    @endif
    
    @if($quiz && $quiz->time_limit_minutes && $quizStarted && !$quizCompleted)
    <script>
        let timeRemaining = {{ $timeRemaining }};
        
        function updateTimer() {
            if (timeRemaining <= 0) {
                @this.call('submitQuiz');
                return;
            }
            
            const hours = Math.floor(timeRemaining / 3600);
            const minutes = Math.floor((timeRemaining % 3600) / 60);
            const seconds = timeRemaining % 60;
            
            document.getElementById('timer').textContent = 
                String(hours).padStart(2, '0') + ':' +
                String(minutes).padStart(2, '0') + ':' +
                String(seconds).padStart(2, '0');
            
            timeRemaining--;
        }
        
        setInterval(updateTimer, 1000);
        updateTimer();
    </script>
    @endif
</div>