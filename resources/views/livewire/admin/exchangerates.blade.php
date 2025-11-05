<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />
    @can('exchangerate.access')
    <x-card title="Exchange Rates" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            @can('exchangerate.create')
            <x-button label="New" responsive icon="o-plus" class="btn-outline" @click="$wire.modal = true" />
            @endcan
        </x-slot:menu>
        <x-table :headers="$headers" :rows="$exchangerates">
            @scope('actions', $exchangerate)
           
            <div class="flex items-center space-x-2">
                @can('exchangerate.update')
                <x-button icon="o-pencil" class="btn-sm btn-info btn-outline" 
                    wire:click="edit({{ $exchangerate->id }})" spinner />
                @endcan
                @can('exchangerate.delete')
                <x-button icon="o-trash" class="btn-sm btn-outline btn-error" 
                    wire:click="delete({{ $exchangerate->id }})" confirm="Are you sure?" spinner />
                @endcan
            </div>
          
            @endscope
            <x-slot:empty>
                <x-alert class="alert-error" title="No exchange rates found." />
            </x-slot:empty>
        </x-table>
    </x-card>   
    @else
    <x-alert class="alert-error mt-3" title="You do not have permission to view exchange rates." />
    @endcan
    <x-modal :title="$id ? 'Edit Exchange Rate' : 'New Exchange Rate'" wire:model="modal">
        <x-form wire:submit="save">
            <div class="grid grid-cols-2 gap-2">
            <x-select label="Base Currency" wire:model="base_currency_id" placeholder="Select Base Currency" :options="$currencies" option-label="name" option-value="id" />
            <x-select label="Secondary Currency" wire:model="secondary_currency_id" placeholder="Select Secondary Currency" :options="$currencies" option-label="name" option-value="id" />
            <x-input label="Rate" wire:model="rate" />
            <x-input label="Start Date" type="date" wire:model="start_date" />
            <x-input label="End Date" type="date" wire:model="end_date" />
            </div>
      
        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.modal = false" />
            <x-button label="{{ $id ? 'Update' : 'Save' }}" type="submit" class="btn-primary" spinner="save" />
        </x-slot:actions>
    </x-form>
    </x-modal>
</div>
