<div>
    <x-breadcrumbs :items="$breadcrumbs"    class="bg-base-300 p-3 rounded-box mt-2" />
    @can('registrationfees.access')
    <x-card title="Registration Fees" separator class="mt-5 border-2 border-gray-200">
       <x-slot:menu>
        @can('registrationfees.modify')
        <x-button label="New" responsive icon="o-plus" class="btn-outline" @click="$wire.modal = true" />
        @endcan
    </x-slot:menu>
    <x-table :headers="$headers" :rows="$records">
        @scope('actions', $record)
        @can('registrationfees.modify')
        <div class="flex items-center space-x-2">
            <x-button icon="o-pencil" class="btn-sm btn-info btn-outline" 
                wire:click="edit({{ $record->id }})" spinner />
            <x-button icon="o-trash" class="btn-sm btn-outline btn-error" 
                wire:click="delete({{ $record->id }})" wire:confirm="Are you sure?" spinner />
        </div>
        @endcan
        @endscope
        <x-slot:empty>
            <x-alert class="alert-error" title="No registration fees found." />
        </x-slot:empty>
    </x-table>
    </x-card>
    @else
    <x-alert class="alert-error mt-3" title="You do not have permission to view registration fees." />
    @endcan

    <x-modal wire:model="modal" title="{{ $id ? 'Edit Registration Fee' : 'New Registration Fee' }}">
        <x-form wire:submit="{{ $id ? 'update' : 'save' }}">
            <div class="grid grid-cols-2 gap-2">
            <x-select label="Customer Type" wire:model="customertype_id" :options="$customertypelist" option-label="name" option-value="id" placeholder="Select Customer Type" />
            <x-select label="Currency" wire:model="currency_id" :options="$currencylist" option-label="name" option-value="id" placeholder="Select Currency" />
            <x-select label="Employment Location" wire:model="employmentlocation_id" :options="$employmentlocationlist" option-label="name" option-value="id" placeholder="Select Employment Location" />
            <x-input label="General Ledger" wire:model="generalledger" />
            <x-input label="Amount" wire:model="amount" />
            <x-input label="Year" wire:model="year" />
            </div>
         
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.modal = false" />
                <x-button label="{{ $id ? 'Update' : 'Save' }}" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
