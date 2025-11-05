<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />
    
    @if($activity)
    <x-card title="Quiz Management for: {{ $activity->title }}" separator class="border-2 border-gray-200">
        <x-slot:menu>
            <x-button 
                icon="o-arrow-left" 
                label="Back to Activities" 
                class="btn-ghost" 
                :link="route('admin.activities')" />
            @if(!$quiz)
                <x-button 
                    icon="o-plus" 
                    label="Create Quiz" 
                    class="btn-primary" 
                    wire:click="openQuizModal" />
            @endif
        </x-slot:menu>

        @if($quiz)
        <div class="mb-6 p-4 bg-blue-50 rounded-lg">
            <h3 class="text-lg font-semibold">{{ $quiz->title }}</h3>
            <p>Pass Rate: {{ $quiz->pass_percentage }}% | Max Attempts: {{ $quiz->max_attempts }}</p>
        </div>

        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Questions</h3>
            <x-button 
                icon="o-plus" 
                label="Add Question" 
                class="btn-primary btn-sm" 
                wire:click="openQuestionModal" />
        </div>

        @if($quiz->questions->count() > 0)
        <div class="space-y-4">
            @foreach($quiz->questions as $question)
            <div class="border rounded-lg p-4">
                <div class="flex justify-between">
                    <p>{{ $question->question }}</p>
                    <div class="flex space-x-2">
                        <x-button 
                            icon="o-pencil" 
                            class="btn-xs btn-warning" 
                            wire:click="openQuestionModal({{ $question->id }})" />
                        <x-button 
                            icon="o-trash" 
                            class="btn-xs btn-error" 
                            wire:click="deleteQuestion({{ $question->id }})" />
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12">
            <p class="text-gray-500 mb-4">No questions yet.</p>
            <x-button 
                label="Add First Question" 
                class="btn-primary" 
                wire:click="openQuestionModal" />
        </div>
        @endif

        @else
        <div class="text-center py-12">
            <p class="text-gray-500 mb-4">No quiz created yet.</p>
            <x-button 
                label="Create Quiz" 
                class="btn-primary" 
                wire:click="openQuizModal" />
        </div>
        @endif
    </x-card>
    @endif

    <!-- Quiz Modal -->
    <x-modal wire:model="quizModal" title="{{ $quiz_id ? 'Edit Quiz' : 'Create Quiz' }}" box-class=" max-w-3xl">
        <div class="space-y-4">
            <x-input 
                wire:model="title" 
                label="Quiz Title" 
                placeholder="Enter quiz title"
                class="input-bordered" />
                
            <x-textarea 
                wire:model="description" 
                label="Description (Optional)" 
                placeholder="Enter quiz description"
                rows="3"
                class="textarea-bordered" />
                
            <div class="grid grid-cols-2 gap-4">
                <x-input 
                    wire:model="pass_percentage" 
                    label="Pass Percentage (%)" 
                    type="number"
                    min="1"
                    max="100"
                    class="input-bordered" />
                    
                <x-input 
                    wire:model="max_attempts" 
                    label="Max Attempts" 
                    type="number"
                    min="1"
                    max="10"
                    class="input-bordered" />
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <x-input 
                    wire:model="time_limit_minutes" 
                    label="Time Limit (minutes)" 
                    type="number"
                    min="1"
                    placeholder="Leave empty for no limit"
                    class="input-bordered" />
                    
                <x-select 
                    wire:model="status" 
                    label="Status"
                    :options="[
                        ['id' => 'ACTIVE', 'name' => 'Active'],
                        ['id' => 'INACTIVE', 'name' => 'Inactive']
                    ]"
                    option-value="id"
                    option-label="name"
                    class="select-bordered" />
            </div>
            
            <label class="flex items-center space-x-2 cursor-pointer">
                <input 
                    type="checkbox" 
                    wire:model="randomize_questions"
                    class="checkbox" />
                <span class="text-sm">Randomize question order</span>
            </label>
        </div>

        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModals" class="btn-ghost" />
            <x-button 
                label="{{ $quiz_id ? 'Update Quiz' : 'Create Quiz' }}" 
                wire:click="saveQuiz" 
                class="btn-primary"
                spinner="saveQuiz" />
        </x-slot:actions>
    </x-modal>

    <!-- Question Modal -->
    <x-modal wire:model="questionModal" title="{{ $question_id ? 'Edit Question' : 'Add Question' }}" box-class="max-w-4xl">
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <x-textarea 
                        wire:model="question" 
                        label="Question" 
                        placeholder="Enter your question here..."
                        rows="3"
                        class="textarea-bordered" />
                </div>
                <div class="space-y-4">
                    <x-select 
                        wire:model="question_type" 
                        label="Question Type"
                        :options="[
                            ['id' => 'SINGLE_CHOICE', 'name' => 'Single Choice'],
                            ['id' => 'MULTIPLE_CHOICE', 'name' => 'Multiple Choice'],
                            ['id' => 'TRUE_FALSE', 'name' => 'True/False']
                        ]"
                        option-value="id"
                        option-label="name"
                        class="select-bordered" />
                        
                    <x-input 
                        wire:model="points" 
                        label="Points" 
                        type="number"
                        min="1"
                        max="100"
                        class="input-bordered" />
                        
                    <x-input 
                        wire:model="order" 
                        label="Order" 
                        type="number"
                        min="0"
                        class="input-bordered" />
                </div>
            </div>

            <!-- Answers Section -->
            <div>
                <div class="flex justify-between items-center mb-4">
                    <label class="block text-sm font-medium text-gray-700">Answer Options</label>
                    @if($question_type !== 'TRUE_FALSE')
                    <x-button 
                        icon="o-plus" 
                        label="Add Answer" 
                        class="btn-sm btn-outline" 
                        wire:click="addAnswer" />
                    @endif
                </div>
                
                <div class="space-y-3">
                    @foreach($answers as $index => $answer)
                    <div class="flex items-center space-x-3 p-3 border rounded-lg">
                        <label class="flex items-center cursor-pointer">
                            <input 
                                type="{{ $question_type === 'MULTIPLE_CHOICE' ? 'checkbox' : 'radio' }}" 
                                wire:model="answers.{{ $index }}.is_correct"
                                @if($question_type !== 'MULTIPLE_CHOICE') name="correct_answer" @endif
                                class="{{ $question_type === 'MULTIPLE_CHOICE' ? 'checkbox' : 'radio' }}" />
                            <span class="ml-2 text-sm text-gray-600">Correct</span>
                        </label>
                        
                        <x-input 
                            wire:model="answers.{{ $index }}.answer_text" 
                            placeholder="Enter answer option..."
                            class="input-bordered flex-1" />
                            
                        @if($question_type !== 'TRUE_FALSE' && count($answers) > 2)
                        <x-button 
                            icon="o-trash" 
                            class="btn-sm btn-error btn-outline" 
                            wire:click="removeAnswer({{ $index }})" />
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            
            <label class="flex items-center space-x-2 cursor-pointer">
                <input 
                    type="checkbox" 
                    wire:model="is_active"
                    class="checkbox" />
                <span class="text-sm">Question is active</span>
            </label>
        </div>

        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModals" class="btn-ghost" />
            <x-button 
                label="{{ $question_id ? 'Update Question' : 'Add Question' }}" 
                wire:click="saveQuestion" 
                class="btn-primary"
                spinner="saveQuestion" />
        </x-slot:actions>
    </x-modal>
</div>
