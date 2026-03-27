<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />

    <x-card title="Institution Services" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            <x-button label="New" responsive icon="o-plus" class="btn-outline" @click="$wire.modal = true" />
        </x-slot:menu>

        <x-table :headers="$headers" :rows="$records">
            @scope('cell_status', $record)
                <x-badge value="{{ ucfirst($record->status) }}"
                    class="{{ $record->status === 'active' ? 'badge-success' : 'badge-error' }}" />
            @endscope

            @scope('cell_description', $record)
                <span class="text-sm text-gray-500">{{ $record->description ?? '—' }}</span>
            @endscope

            @scope('actions', $record)
                <div class="flex items-center space-x-2">
                    <x-button icon="o-pencil" class="btn-sm btn-info btn-outline"
                        wire:click="edit({{ $record->id }})" spinner />
                    <x-button icon="o-trash" class="btn-sm btn-outline btn-error"
                        wire:click="delete({{ $record->id }})" wire:confirm="Are you sure?" spinner />
                </div>
            @endscope

            <x-slot:empty>
                <x-alert class="alert-error" title="No institution services found." />
            </x-slot:empty>
        </x-table>
    </x-card>

    <x-modal wire:model="modal" title="{{ $id ? 'Edit Institution Service' : 'New Institution Service' }}">
        <x-form wire:submit="save">
            <x-select label="Institution" wire:model="institution_id"
                :options="$institutions" option-label="name" option-value="id"
                placeholder="Select Institution" />
            <x-input label="Name" wire:model="name" placeholder="e.g. Laboratory Testing" />
            <x-textarea label="Description" wire:model="description" placeholder="Brief description of the service" rows="3" />
            <x-select label="Status" wire:model="status"
                :options="$statusoptions" option-label="label" option-value="id" />

            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.modal = false" />
                <x-button label="{{ $id ? 'Update' : 'Save' }}" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
