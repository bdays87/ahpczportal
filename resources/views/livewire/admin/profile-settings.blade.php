<div>
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'icon' => 'o-home', 'link' => route('dashboard')],
        ['label' => 'Profile Settings']
    ]" class="bg-base-300 p-3 rounded-box mt-2" />

    {{-- Profile info --}}
    <x-card title="Profile Settings" separator class="mt-5 border-2 border-gray-200">
        <x-form wire:submit="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input label="Name" wire:model="name" />
                <x-input label="Surname" wire:model="surname" />
                <x-input label="Email" wire:model="email" type="email" />
                <x-input label="Phone" wire:model="phone" />
            </div>
            <x-slot:actions>
                <x-button label="Cancel" link="{{ route('dashboard') }}" class="btn-outline" />
                <x-button label="Save" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-card>

    {{-- Change password --}}
    <x-card title="Change Password" separator class="mt-5 border-2 border-gray-200">
        <x-form wire:submit="changepassword">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-password label="Current Password" wire:model="current_password" />
                <x-password label="New Password" wire:model="new_password" />
                <x-password label="Confirm New Password" wire:model="new_password_confirmation" />
            </div>
            @error('current_password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
            <x-slot:actions>
                <x-button label="Change Password" type="submit" class="btn-warning" spinner="changepassword" icon="o-lock-closed" />
            </x-slot:actions>
        </x-form>
    </x-card>
</div>
