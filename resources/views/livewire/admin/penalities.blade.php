<div>
    <x-breadcrumbs :items="$breadcrumbs"    class="bg-base-300 p-3 rounded-box mt-2" />

    @can('penalties.access')
    <x-card title="Penalities" class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            @can('penalties.modify')
            <x-button label="New" responsive icon="o-plus" class="btn-outline" @click="$wire.modal = true" />
            @endcan
        </x-slot:menu>
        <x-table :headers="$headers" :rows="$penalities">
            @scope('cell_lowerlimit', $penality)
            {{ $penality->lowerlimit }} months
            @endscope
            @scope('cell_upperlimit', $penality)
            {{ $penality->upperlimit }} months
            @endscope
            @scope('cell_penalty', $penality)
            {{ $penality->penalty }}%
            @endscope
            @scope('actions', $penality)
            @can('penalties.modify')
            <div class="flex items-center space-x-2">
                <x-button icon="o-pencil" class="btn-sm btn-info btn-outline" 
                    wire:click="edit({{ $penality->id }})" spinner />
                <x-button icon="o-trash" class="btn-sm btn-outline btn-error" 
                    wire:click="delete({{ $penality->id }})" wire:confirm="Are you sure?" spinner />
            </div>
            @endcan
            @endscope
            <x-slot:empty>
                <x-alert class="alert-error" title="No penalities found." />
            </x-slot:empty>
        </x-table>
    </x-card>
    @else
    <x-alert class="alert-error mt-3" title="You do not have permission to view penalities." />
    @endcan
    <x-modal title="{{ $id ? 'Edit' : 'New' }} Penality" wire:model="modal">
        <x-form wire:submit.prevent="save">
            <div class="grid grid-cols-2 gap-2">
                <x-select label="Tire" wire:model="tire_id" :options="$tires" option-label="name" option-value="id" placeholder="Select" />
                <x-input type="number" label="Penality(%)" min="0" max="100" wire:model="penality" />
                <x-input type="number" label="Lower Limit(months)" wire:model="lowerlimit" />
                <x-input type="number" label="Upper Limit(months)" wire:model="upperlimit" />
                
            </div>
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.modal = false" />
                <x-button label="{{ $id ? 'Update' : 'Save' }}" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
