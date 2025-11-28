<div>
    <x-button icon="o-academic-cap" class="btn-sm btn-warning btn-outline" 
        wire:click="showmodal" spinner />
    <x-modal title="Qualifications" wire:model="modal" box-class="max-w-3xl">
        <x-input placeholder="Search..." wire:model.live="search" />
       
        <x-table :headers="$headers" :rows="$qualifications">
            @scope('actions', $qualification)
            <div class="flex items-center space-x-2">
                <x-button icon="o-pencil" class="btn-sm btn-info btn-outline" 
                    wire:click="edit({{ $qualification->id }})" spinner />
                <x-button icon="o-trash" class="btn-sm btn-outline btn-error" 
                    wire:click="delete({{ $qualification->id }})" wire:confirm="Are you sure?" spinner />
            </div>
            @endscope
            <x-slot:empty>
                <x-alert class="alert-error" title="No qualifications found." />
            </x-slot:empty>
        </x-table>
        <x-slot:actions>
            <x-button icon="o-plus" class="btn-sm btn-primary btn-circle" 
                wire:click="newqualification" spinner /> 
        </x-slot:actions>
    </x-modal>
    <x-modal title="{{ $id ? 'Edit qualification' : 'New qualification' }}" wire:model="modifymodal">
        <x-form wire:submit="save">
            <div class="grid gap-2">
                <x-input label="Name" wire:model="name" />
                <x-select label="Institution" wire:model="institution_id" :options="$institutions" option-label="name" option-value="id" placeholder="Select Institution" />
            </div>
      
        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.modifymodal = false" />
            <x-button label="{{ $id ? 'Update' : 'Save' }}" type="submit" class="btn-primary" spinner="save" />
        </x-slot:actions>
    </x-form>
    </x-modal>
</div>
