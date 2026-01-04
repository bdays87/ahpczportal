<div>
    <x-card title="Customer Imports" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            <x-input placeholder="Search" wire:model.live="search" />
            <x-button label="Import" responsive icon="o-plus" class="btn-outline" @click="$wire.modal = true" />
            <x-button label="New" responsive icon="o-plus" class="btn-outline" @click="$wire.modifymodal = true" />
        </x-slot:menu>
        <x-table :headers="$headers" :rows="$customerimports" with-pagination>
            @scope('actions', $customerimport)
            <div class="flex items-center space-x-2">
                <x-button icon="o-pencil" class="btn-sm btn-info btn-outline" 
                    wire:click="edit({{ $customerimport->id }})" spinner />
                    <x-button icon="o-trash" class="btn-sm btn-error btn-outline" 
                    wire:click="delete({{ $customerimport->id }})" wire:confirm="Are you sure?" spinner />
            </div>
            @endscope
            @scope('cell_email', $customerimport)
            {{$customerimport->email }}
            @endscope
            @scope('cell_hasaccount', $customerimport)
            <x-badge class="{{ $customerimport->user != null ? 'badge-success' : 'badge-error' }}" value="{{ $customerimport->user != null ? 'Yes' : 'No' }}" />
            @endscope
        
        <x-slot:empty>
            <x-alert class="alert-error" title="No customer imports found." />
        </x-slot:empty>
        </x-table>
    </x-card>

    <x-modal title="Import Customer" wire:model="modal">
        <x-form wire:submit.prevent="saveimport">
            <x-input label="File" wire:model="file" type="file" />
            <x-select label="Type" wire:model="type" :options="$typelist" placeholder="Select Type" />
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.modal = false" />
                <x-button label="Import" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>
    <x-modal title="{{ $id ? 'Edit' : 'Add' }} Customer" wire:model="modifymodal">
        <x-form wire:submit.prevent="save">
            <div class="grid grid-cols-2 gap-2">
            <x-input label="Name" wire:model="name" />
            <x-input label="Surname" wire:model="surname" />
            <x-input label="RegNumber" wire:model="regnumber" />
            <x-select label="Gender" wire:model="gender" :options="[['id'=>'MALE','name'=>'MALE'],['id'=>'FEMALE','name'=>'FEMALE']]" option-label="name" option-value="id" />
            <x-input label="Email" wire:model="email" />
            </div>
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.modifymodal = false" />
                <x-button label="{{ $id ? 'Update' : 'Save' }}" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
