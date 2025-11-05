<div>
    <x-breadcrumbs :items="$breadcrumbs"    class="bg-base-300 p-3 rounded-box mt-2" />
    @can('currency.access')
    <x-card title="Currencies" separator class="mt-5 border-2 border-gray-200">
       <x-slot:menu>
        @can('currency.modify')
        <x-button label="New" responsive icon="o-plus" class="btn-outline" @click="$wire.modal = true" />
        @endcan
    </x-slot:menu>
    <x-table :headers="$headers" :rows="$currencies">
        @scope('actions', $currency)
        @can('currency.modify')
        <div class="flex items-center space-x-2">
            <x-button icon="o-pencil" class="btn-sm btn-info btn-outline" 
                wire:click="edit({{ $currency->id }})" spinner />
            <x-button icon="o-trash" class="btn-sm btn-outline btn-error" 
                wire:click="delete({{ $currency->id }})" confirm="Are you sure?" spinner />
        </div>
        @endcan
        @endscope
        <x-slot:empty>
            <x-alert class="alert-error" title="No currencies found." />
        </x-slot:empty>
    </x-table>
    </x-card>
    @else
    <x-alert class="alert-error mt-3" title="You do not have permission to view currencies." />
    @endcan
    <x-modal title="{{ $id ? 'Edit' : 'New' }} Currency" wire:model="modal">
        <x-form wire:submit.prevent="save">
            <div class="grid  gap-2">
            <x-input label="Name" wire:model="name" />
            <x-select label="Status" wire:model="status" placeholder="Select Status" :options="[['id'=>'active', 'name'=>'Active'], ['id'=>'inactive', 'name'=>'Inactive']]" option-label="name" option-value="id" />
            </div>
      
        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.modal = false" />
            <x-button label="{{ $id ? 'Update' : 'Save' }}" type="submit" class="btn-primary" spinner="save" />
        </x-slot:actions>
    </x-form>
    </x-modal>
</div>
