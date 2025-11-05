<div>
    @can('renewalconfiguration.access')
    <x-card title="Renewal Documents" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            @can('renewalconfiguration.modify')
            <x-button label="New" responsive icon="o-plus" class="btn-outline" @click="$wire.modal = true" />
            @endcan
        </x-slot:menu>
        <x-table :headers="$headers" :rows="$records">
            @scope('actions', $record)
            @can('renewalconfiguration.modify')
            <div class="flex items-center space-x-2">
                <x-button icon="o-pencil" class="btn-sm btn-info btn-outline" 
                    wire:click="edit({{ $record->id }})" spinner />
                <x-button icon="o-trash" class="btn-sm btn-outline btn-error" 
                    wire:click="delete({{ $record->id }})" wire:confirm="Are you sure?" spinner />
            </div>
            @endcan
            @endscope
            <x-slot:empty>
                <x-alert class="alert-error" title="No renewal documents found." />
            </x-slot:empty>
        </x-table>
    </x-card>
    @else
    <x-alert class="alert-error mt-3" title="You do not have permission to view renewal documents." />
    @endcan

    <x-modal wire:model="modal" title="{{ $id ? 'Edit Renewal Document' : 'New Renewal Document' }}">
        <x-form wire:submit="{{ $id ? 'update' : 'save' }}">
            <div class="grid grid-cols-2 gap-2">
                <x-select label="Tire" wire:model="tire_id" :options="$tirelist" option-label="name" option-value="id" placeholder="Select Tire" />
                <x-select label="Register Type" wire:model="registertype_id" :options="$registertypelist" option-label="name" option-value="id" placeholder="Select Register Type" />
                <x-select label="Document" wire:model="document_id" :options="$documentlist" option-label="name" option-value="id" placeholder="Select Document" />
                <x-select label="Application type" wire:model="applicationtype_id" :options="$applicationtypelist" option-label="name" option-value="id" placeholder="Select Application Type" />
            </div>
         
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.modal = false" />
                <x-button label="{{ $id ? 'Update' : 'Save' }}" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
