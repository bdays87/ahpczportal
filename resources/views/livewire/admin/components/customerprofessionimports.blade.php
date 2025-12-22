<div>
    
    <x-card title="Customer Professions Imports" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            <x-input placeholder="Search" wire:model.live="search" />
            <x-button label="New" responsive icon="o-plus" class="btn-outline" @click="$wire.editmodal = true" />
            <x-button label="Import" responsive icon="o-plus" class="btn-outline" @click="$wire.importmodal = true" />
        </x-slot:menu>
        <x-table :headers="$headers" :rows="$customerprofessionimports" with-pagination>
            @scope('actions', $customerprofessionimport)
            <div class="flex items-center space-x-2">
                <x-button icon="o-pencil" class="btn-sm btn-info btn-outline" 
                    wire:click="edit({{ $customerprofessionimport->id }})" spinner />
                <x-button icon="o-trash" class="btn-sm btn-error btn-outline" 
                    wire:click="delete({{ $customerprofessionimport->id }})" wire:confirm="Are you sure?" spinner />
            </div>
            @endscope
            @scope('cell_proceeded', $customerprofessionimport)
            <x-badge class="{{ $customerprofessionimport->proceeded == 'Y' ? 'badge-success' : 'badge-error' }}" value="{{ $customerprofessionimport->proceeded == 'Y' ? 'Yes' : 'No' }}" />
            @endscope
            <x-slot:empty>
                <x-alert class="alert-error" title="No customer professions imports found." />
            </x-slot:empty>
        </x-table>
    </x-card>
    <x-modal title="{{ $id ? 'Edit' : 'Add' }} Customer Profession" wire:model="editmodal">
        <x-form wire:submit.prevent="savecustomerprofession">
            <x-input label="RegNumber" wire:model="regnumber" />
            <x-input label="Prefix" wire:model="prefix" />
            <x-input label="Status" wire:model="status" />
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.modal = false" />
                <x-button label="{{ $id ? 'Update' : 'Save' }}" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>
    <x-modal title="Import Customer Professions" wire:model="importmodal">
        <x-form wire:submit.prevent="saveimport">
            <x-input label="File" wire:model="file" type="file" />
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.importmodal = false" />
                <x-button label="Import" type="submit" class="btn-primary" spinner="import" />
            </x-slot:actions>
        </x-form>
    </x-modal>


    </div>
