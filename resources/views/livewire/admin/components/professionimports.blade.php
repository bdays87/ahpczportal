<div>
    <x-card title="Profession Imports" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            <x-input placeholder="Search" wire:model.live="search" />
            <x-button label="Import" responsive icon="o-plus" class="btn-outline" @click="$wire.modal = true" />
            <x-button label="New" responsive icon="o-plus" class="btn-outline" @click="$wire.editmodal = true" />
        </x-slot:menu>
        <x-table :headers="$headers" :rows="$professionimports" with-pagination>
            @scope('actions', $professionimport)
            <div class="flex items-center space-x-2">
                <x-button icon="o-pencil" class="btn-sm btn-info btn-outline" 
                    wire:click="edit({{ $professionimport->id }})" spinner />
                <x-button icon="o-trash" class="btn-sm btn-error btn-outline" 
                    wire:click="delete({{ $professionimport->id }})" wire:confirm="Are you sure?" spinner />
            </div>
            @endscope
            @scope('cell_proceeded', $professionimport)
            <x-badge class="{{ $professionimport->proceeded == 'Y' ? 'badge-success' : 'badge-error' }}" value="{{ $professionimport->proceeded == 'Y' ? 'Yes' : 'No' }}" />
            @endscope
            <x-slot:empty>
                <x-alert class="alert-error" title="No profession imports found." />
            </x-slot:empty>
        </x-table>
    </x-card>
<x-modal title="Import Profession" wire:model="modal">
    <div x-data="{ uploading: false, fileReady: false }"
         x-on:livewire-upload-start="uploading = true; fileReady = false"
         x-on:livewire-upload-finish="uploading = false; fileReady = true"
         x-on:livewire-upload-error="uploading = false; fileReady = false">
        <x-form wire:submit.prevent="saveprofessionimport">
            <x-input label="File" wire:model="file" type="file" />
            <p x-show="uploading" class="text-sm text-blue-600 mt-1 flex items-center gap-1">
                <span class="loading loading-spinner loading-xs"></span> Uploading file, please wait...
            </p>
            <p x-show="fileReady && !uploading" class="text-sm text-green-600 font-medium mt-1">
                ✓ File ready to submit
            </p>
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.modal = false" wire:loading.attr="disabled" wire:target="saveprofessionimport" />
                <x-button label="Import" type="submit" class="btn-primary" spinner="saveprofessionimport"
                    wire:loading.attr="disabled" wire:target="saveprofessionimport"
                    x-bind:disabled="uploading || !fileReady" />
            </x-slot:actions>
        </x-form>
    </div>
</x-modal>
<x-modal title="{{ $id ? 'Edit' : 'Add' }} Profession" wire:model="editmodal">
    <x-form wire:submit.prevent="saveprofession">
        <x-input label="Name" wire:model="name" />
        <x-input label="Prefix" wire:model="prefix" />
        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.editmodal = false" />
            <x-button label="{{ $id ? 'Update' : 'Save' }}" type="submit" class="btn-primary" spinner="save" />
        </x-slot:actions>
    </x-form>
</x-modal>
</div>
