<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />
    <x-card title="Institutions" separator class="mt-5 border-2 border-gray-200" separator progress-indicator>
        <x-slot:menu>
            <x-input placeholder="Search" wire:model.live="search" />
            <x-button label="New" responsive icon="o-plus" class="btn-outline" @click="$wire.modal = true" />
        </x-slot:menu>
        <x-table :headers="$headers" :rows="$institutions" with-pagination>
            <x-slot:empty>
                <x-alert class="alert-error" title="No institutions found." />
            </x-slot:empty>
            @scope('actions', $institution)
            <div class="flex items-center space-x-2">
                <x-button icon="o-pencil" class="btn-sm btn-info btn-outline" 
                    wire:click="edit({{ $institution->id }})" spinner />
                <x-button icon="o-trash" class="btn-sm btn-outline btn-error" 
                    wire:click="delete({{ $institution->id }})" wire:confirm="Are you sure?" spinner />
                </div>
            @endscope
        </x-table>
    </x-card>
    <x-modal title="{{ $id ? 'Edit Institution' : 'New Institution' }}" wire:model="modal">
        <x-form wire:submit="save">
            <x-input label="Name" wire:model="name" />
            <x-select label="Accredited" wire:model="accredited" :options="[['id'=>'Y','name'=>'Yes'],['id'=>'N','name'=>'No']]" option-label="name" option-value="id" placeholder="Select" />
     
        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.modal = false" />
            <x-button label="{{ $id ? 'Update' : 'Save' }}" type="submit" class="btn-primary" spinner="save" />
        </x-slot:actions>
        </x-form>
    </x-modal>
</div>
