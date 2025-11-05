<div class="space-y-6">
    <!-- Basic Information -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
            <x-input 
                wire:model="title" 
                label="Activity Title" 
                placeholder="Enter activity title"
                class="input-bordered" />
        </div>
        
        <div>
            <x-select 
                wire:model="type" 
                label="Activity Type"
                :options="[
                    ['id' => 'VIDEO', 'name' => 'Video'],
                    ['id' => 'ARTICLE', 'name' => 'Article'],
                    ['id' => 'ATTACHMENT', 'name' => 'Attachment']
                ]"
                option-value="id"
                option-label="name"
                class="select-bordered" />
        </div>
        
        <div>
            <x-select 
                wire:model="status" 
                label="Status"
                :options="[
                    ['id' => 'DRAFT', 'name' => 'Draft'],
                    ['id' => 'PUBLISHED', 'name' => 'Published'],
                    ['id' => 'ARCHIVED', 'name' => 'Archived']
                ]"
                option-value="id"
                option-label="name"
                class="select-bordered" />
        </div>
        
        <div>
            <x-input 
                wire:model="points" 
                label="Points" 
                type="number"
                min="1"
                max="100"
                placeholder="Points awarded"
                class="input-bordered" />
        </div>
        
        <div>
            <x-input 
                wire:model="duration_minutes" 
                label="Duration (minutes)" 
                type="number"
                min="1"
                placeholder="Estimated duration"
                class="input-bordered" />
        </div>
    </div>

    <!-- Description -->
    <div>
        <x-textarea 
            wire:model="description" 
            label="Description" 
            placeholder="Enter activity description"
            rows="4"
            class="textarea-bordered" />
    </div>

    <!-- Professions -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Associated Professions</label>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-2 max-h-40 overflow-y-auto border rounded-lg p-3">
            @foreach($professions as $profession)
            <label class="flex items-center space-x-2 cursor-pointer">
                <input 
                    type="checkbox" 
                    wire:model="profession_ids" 
                    value="{{ $profession['id'] }}"
                    class="checkbox checkbox-sm" />
                <span class="text-sm">{{ $profession['name'] }}</span>
            </label>
            @endforeach
        </div>
        @error('profession_ids') 
            <span class="text-error text-sm">{{ $message }}</span> 
        @enderror
    </div>

    <!-- Content based on type -->
    @if($type === 'VIDEO')
    <div>
        <x-input 
            wire:model="content_url" 
            label="Video URL" 
            type="url"
            placeholder="https://youtube.com/watch?v=..."
            class="input-bordered" />
        <div class="text-xs text-gray-500 mt-1">
            Enter a valid video URL (YouTube, Vimeo, etc.)
        </div>
    </div>
    @endif

    @if($type === 'ARTICLE')
    <div>
        <x-textarea 
            wire:model="content_text" 
            label="Article Content" 
            placeholder="Enter the article content here..."
            rows="8"
            class="textarea-bordered" />
    </div>
    @endif

    @if($type === 'ATTACHMENT')
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Upload File</label>
        <input 
            type="file" 
            wire:model="attachment_file"
            class="file-input file-input-bordered w-full"
            accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx" />
        <div class="text-xs text-gray-500 mt-1">
            Supported formats: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX (Max: 10MB)
        </div>
        @error('attachment_file') 
            <span class="text-error text-sm">{{ $message }}</span> 
        @enderror
    </div>
    @endif
</div>

<x-slot:actions>
    <x-button label="Cancel" wire:click="closeModals" class="btn-ghost" />
    <x-button 
        label="{{ $activity_id ? 'Update' : 'Create' }}" 
        wire:click="save" 
        class="btn-primary"
        spinner="save" />
</x-slot:actions>
