<div>
    <x-breadcrumbs :items="$breadcrumbs"    class="bg-base-300 p-3 rounded-box mt-2" />
    @can('applicationfees.access')
    <x-card title="Application Fees" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            @can('applicationfees.modify')
            <x-button label="New" responsive icon="o-plus" class="btn-outline" @click="$wire.modal = true" />
            @endcan
        </x-slot:menu>
        <x-table :headers="$headers" :rows="$applicationfees">
            @scope('actions', $applicationfee)
            @can('applicationfees.modify')
            <div class="flex items-center space-x-2">
                <x-button icon="o-pencil" class="btn-sm btn-info btn-outline" 
                    wire:click="edit({{ $applicationfee->id }})" spinner />
                <x-button icon="o-trash" class="btn-sm btn-outline btn-error" 
                    wire:click="delete({{ $applicationfee->id }})" wire:confirm="Are you sure?" spinner />
            </div>
            @endcan
            @endscope
            <x-slot:empty>
                <x-alert class="alert-error" title="No application fees found." />
            </x-slot:empty>
        </x-table>
    </x-card>
    @else
    <x-alert class="alert-error mt-3" title="You do not have permission to view application fees." />
    @endcan

    <x-modal wire:model="modal" title="{{ $id ? 'Edit Application Fee' : 'New Application Fee' }}">
        <x-form wire:submit="save">
            <div class="grid grid-cols-2 gap-2">
                <x-select label="Name" wire:model="name" :options="[['id'=>'NEW','label'=>'New Application Fee'], ['id'=>'RENEWAL','label'=>'Renewal Fee'], ['id'=>'MAINTANCE','label'=>'Maintenance Fee']]
                " option-label="label" option-value="id" placeholder="Select Name" />
            <x-select label="Tire" wire:model="tire_id" :options="$tires" option-label="name" option-value="id" placeholder="Select Tire" />
            <x-select label="Currency" wire:model="currency_id" :options="$currencies" option-label="name" option-value="id" placeholder="Select Currency" />
            <x-select label="Register Type" wire:model="registertype_id" :options="$registertypes" option-label="name" option-value="id" placeholder="Select Register Type" />
            <x-select label="Employment Location" wire:model="employmentlocation_id" :options="$employmentlocations" option-label="name" option-value="id" placeholder="Select Employment Location" />
         
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
