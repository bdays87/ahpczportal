<div>
    <x-breadcrumbs :items="[
        [
            'label' => 'Dashboard',
            'icon' => 'o-home',
            'link' => route('dashboard'),
        ],
        [
            'label' => 'Profile Settings',
        ]
    ]" class="bg-base-300 p-3 rounded-box mt-2" />
    
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
</div>
