<div>
    @if(auth()->user()->customer?->customer)
    <div class="relative inline-block">
        <div class="avatar">
            <div class="w-16 h-16 rounded-full ring ring-primary ring-offset-base-100 ring-offset-2">
                <img src="{{ $imageurl }}" 
                     alt="{{ auth()->user()->customer->customer->name }}" />  
            </div>
        </div>
        <button 
            wire:click="openUploadModal"
            class="absolute bottom-0 right-0 btn btn-xs btn-circle btn-primary"
            title="Upload Profile Picture">
            <x-icon name="o-camera" class="w-4 h-4" />
        </button>
    </div>

    <!-- Upload Modal -->
    <x-modal wire:model="uploadModal" title="Upload Profile Picture" box-class="max-w-md">
        <div class="space-y-4">
            @if($profile)
            <div class="flex justify-center">
                <img src="{{ $profile->temporaryUrl() }}" alt="Preview" class="w-32 h-32 rounded-full object-cover" />
            </div>
            @endif

            <x-input 
                type="file" 
                wire:model="profile" 
                label="Profile Picture" 
                accept="image/*"
                hint="Max size: 2MB. Supported formats: JPEG, PNG, JPG, GIF" />
        </div>

        <x-slot:actions>
            <x-button label="Cancel" wire:click="$set('uploadModal', false)" class="btn-ghost" />
            <x-button 
                label="Upload" 
                wire:click="uploadProfile" 
                class="btn-primary" 
                spinner="uploadProfile"
                :disabled="!$profile" />
        </x-slot:actions>
    </x-modal>
    @endif
</div>

