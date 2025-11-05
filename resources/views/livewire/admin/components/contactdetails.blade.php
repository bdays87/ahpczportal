<div>
    <x-card title="Contact details" class="mt-5 border-2 border-gray-200">  
    <x-slot:menu>
        <x-button class="btn-primary btn-circle" icon="o-plus" separator wire:click="$set('modal', true)" responsive />
    </x-slot:menu>
    <x-table :headers="$headers" :rows="$customer->contactdetails" class="table table-xs">
    @scope('actions', $contactdetail)
    <div class="flex items-center space-x-2">
        @can('configurations.modify')
        <x-button icon="o-pencil" class="btn-sm btn-info btn-outline" 
            wire:click="edit({{ $contactdetail->id }})" spinner />
        <x-button icon="o-trash" class="btn-sm btn-outline btn-error" 
            wire:click="delete({{ $contactdetail->id }})" wire:confirm="Are you sure?" spinner />
        @endcan
    </div>
    @endscope
    @scope('cell_dates', $contactdetail)
    {{ $contactdetail->start_date }} - {{ $contactdetail->end_date }}
    @endscope
    <x-slot:empty>
        <x-alert class="alert-error" title="No contact details found." />
    </x-slot:empty>
    </x-table>
    </x-card>
    <x-modal wire:model="modal" title="{{ $id ? 'Edit' : 'Add' }} Contact Detail">
        <x-form wire:submit.prevent="save">
            <div class="grid gap-2">
        <x-input label="Name" wire:model="name" />
        <x-input label="Relationship" wire:model="relationship" />
        <x-input label="Primary Contact" wire:model="primarycontact" />
        <x-input label="Secondary Contact" wire:model="secondarycontact" />
        <x-input label="Email" wire:model="email" />
        </div>
        <x-slot:actions>
            <x-button label="Cancel" class="btn-sm btn-outline btn-error" wire:click="$set('modal', false)" />
            <x-button label="Save" class="btn-sm btn-info btn-outline" type="submit" spinner="save" />
        </x-slot:actions>
        </x-form>
    </x-modal>
</div>
