<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />

    <x-card title="Cost Centers" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            <x-input placeholder="Search…" wire:model.live="search" icon="o-magnifying-glass" />
            <x-button icon="o-plus" label="New Cost Center" class="btn-primary" wire:click="$set('modal',true)" spinner />
        </x-slot:menu>

        <x-table :headers="$headers" :rows="$costCenters">
            @scope('cell_status', $cc)
                <span class="badge {{ $cc->status === 'active' ? 'badge-success' : 'badge-ghost' }}">
                    {{ strtoupper($cc->status) }}
                </span>
            @endscope

            @scope('actions', $cc)
                <div class="flex gap-1">
                    <x-button icon="o-pencil" class="btn-xs btn-ghost btn-outline"
                        wire:click="edit({{ $cc->id }})" spinner />
                    <x-button icon="o-trash" class="btn-xs btn-error btn-outline"
                        wire:click="delete({{ $cc->id }})" wire:confirm="Delete this cost center?" spinner />
                </div>
            @endscope

            <x-slot:empty>
                <x-alert class="alert-info" title="No cost centers found." />
            </x-slot:empty>
        </x-table>
    </x-card>

    {{-- Create / Edit Modal --}}
    <x-modal wire:model="modal" title="{{ $id ? 'Edit Cost Center' : 'New Cost Center' }}">
        <x-form wire:submit="save">
            <x-input label="Cost Center Code *" wire:model="code" placeholder="e.g., CC001" />
            <x-input label="Name *" wire:model="name" placeholder="e.g., Headquarters" />
            <x-textarea label="Description" wire:model="description" rows="3" />
            <x-select label="Status" wire:model="status"
                :options="[['id'=>'active','name'=>'Active'],['id'=>'inactive','name'=>'Inactive']]"
                option-label="name" option-value="id" />

            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.modal=false" />
                <x-button label="Save" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
