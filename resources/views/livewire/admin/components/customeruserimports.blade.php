<div>
    <x-card title="Customer Users" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            <x-input placeholder="Search" wire:model.live="search" />
            <x-button label="Import" responsive icon="o-plus" class="btn-outline" @click="$wire.importmodal = true" />
            <x-button label="New" responsive icon="o-plus" class="btn-outline" @click="$wire.modifymodal = true" />
        </x-slot:menu>
        <x-table :headers="$headers" :rows="$customerusers" with-pagination>
            @scope('actions', $customeruser)
            <div class="flex items-center space-x-2">
                <x-button icon="o-pencil" class="btn-sm btn-info btn-outline" 
                    wire:click="edit({{ $customeruser->id }})" spinner />
                    <x-button icon="o-trash" class="btn-sm btn-error btn-outline" 
                    wire:click="delete({{ $customeruser->id }})" wire:confirm="Are you sure?" spinner />
            </div>
            @endscope
            @scope('cell_email', $customeruser)
            {{$customeruser->email }}
            @endscope
        <x-slot:empty>
            <x-alert class="alert-error" title="No customer users found." />
        </x-slot:empty>
        </x-table>
    </x-card>

<x-modal title="{{ $id ? 'Edit' : 'Add' }} Customer User" wire:model="modifymodal">
    <x-form wire:submit.prevent="save">
        <div class="grid  gap-2">
            <x-input label="Name" wire:model="name" />
            <x-input label="Surname" wire:model="surname" />
            <x-input label="RegNumber" wire:model="regnumber" />
            <x-input label="Email" wire:model="email" />
            <x-input label="Password" wire:model="password" />
        </div>
        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.modifymodal = false" />
            <x-button label="{{ $id ? 'Update' : 'Save' }}" type="submit" class="btn-primary" spinner="save" />
        </x-slot:actions>
    </x-form>
</x-modal>

<x-modal title="Import Customer Users" wire:model="importmodal">
    <x-form wire:submit.prevent="saveimport">
        <x-input label="File" wire:model="file" type="file" />
        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.importmodal = false" />
            <x-button label="Import" type="submit" class="btn-primary" spinner="import" />
        </x-slot:actions>
    </x-form>
</x-modal>
</div>