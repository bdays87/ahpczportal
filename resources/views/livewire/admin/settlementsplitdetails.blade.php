<div>
    
    <x-breadcrumbs :items="$breadcrumbs"    class="bg-base-300 p-3 rounded-box mt-2" />
    @can('settlementsplits.access')
    <x-card title="Settlement Splits" separator class="mt-5 border-2 border-gray-200">
       <x-slot:menu>
        @can('settlementsplits.modify')
        <x-button label="New" responsive icon="o-plus" class="btn-outline" @click="$wire.modal = true" />
        @endcan
    </x-slot:menu>
    <x-table :headers="$headers" :rows="$records">
        @scope('actions', $record)
        @can('settlementsplits.modify')
        <div class="flex items-center space-x-2">
            <x-button icon="o-pencil" class="btn-sm btn-info btn-outline" 
                wire:click="edit({{ $record->id }})" spinner />
            <x-button icon="o-trash" class="btn-sm btn-outline btn-error" 
                wire:click="delete({{ $record->id }})" wire:confirm="Are you sure?" spinner />
        </div>
        @endcan
        @endscope
        <x-slot:empty>
            <x-alert class="alert-error" title="No settlement splits found." />
        </x-slot:empty>
    </x-table>
    </x-card>
    @else
    <x-alert class="alert-error mt-3" title="You do not have permission to view settlement splits." />
    @endcan
    <x-modal wire:model="modal" title="{{ $id ? 'Edit' : 'New' }}Settlement Split Details">
        <x-form wire:submit.prevent="save">
            <div class="grid grid-cols-2 gap-2">
            <x-input label="Type" wire:model="type" />
            <x-select label="Currency" wire:model="currency_id" :options="$currencies" option-label="name" option-value="id" placeholder="Select Currency" />
            <x-select label="Employment Location" wire:model="employmentlocation_id" :options="$employmentlocations" option-label="name" option-value="id" placeholder="Select Employment Location" />
            <x-input label="Percentage" type="number" min="0" max="100" wire:model="percentage" />
            </div>      
        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.modal = false" />
            <x-button label="{{ $id ? 'Update' : 'Save' }}" type="submit" class="btn-primary" spinner="save" />
        </x-slot:actions>
    </x-form>
    </x-modal>
</div>
