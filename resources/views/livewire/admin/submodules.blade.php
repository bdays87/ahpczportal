<div>
 <x-breadcrumbs :items="$breadcrumbs"    class="bg-base-300 p-3 rounded-box mt-2" />

 <x-card title="Submodules" separator class="mt-5 border-2 border-gray-200">
    <x-slot:menu>
        <x-button label="New" responsive icon="o-plus" class="btn-outline" @click="$wire.modal = true" />
    </x-slot:menu>

    <x-table :headers="$headers" :rows="$systemmodule->submodules">
        @scope('actions', $submodule)
        <div class="flex items-center space-x-2">
            <x-button icon="o-pencil" class="btn-sm btn-info btn-outline" 
                wire:click="edit({{ $submodule->id }})" spinner />
            <x-button icon="o-trash" class="btn-sm btn-outline btn-error" 
                wire:click="delete({{ $submodule->id }})" confirm="Are you sure?" spinner />
                <livewire:admin.components.permissions :submodule_id="$submodule->id"/>
        </div>
        @endscope
        <x-slot:empty>
            <x-alert class="alert-error" title="No submodules found." />
        </x-slot:empty>
    </x-table>
</x-card>
<x-modal title="{{ $id ? 'Edit Submodule' : 'New Submodule' }}" wire:model="modal">
    <x-form wire:submit.prevent="save">
        <div class="grid lg:grid-cols-2 gap-2">
            <x-input label="Name" wire:model="name" />
            <x-input label="Url" wire:model="url" />
            <x-input label="Icon" wire:model="icon" />
            <x-input label="Default Permission" wire:model="default_permission" />
        </div>
  
    <x-slot:actions>
        <x-button label="Cancel" @click="$wire.modal = false" />
        <x-button label="{{ $id ? 'Update' : 'Save' }}" type="submit" class="btn-primary" spinner="save" />
    </x-slot:actions>
    </x-form>
</x-modal>

</div>
