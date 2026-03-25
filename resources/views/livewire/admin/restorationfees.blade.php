<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />

    <x-card title="Restoration Fees" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            <x-button label="New" responsive icon="o-plus" class="btn-outline" @click="$wire.modal = true" />
        </x-slot:menu>

        <x-table :headers="$headers" :rows="$fees">
            @scope('cell_status', $fee)
                <x-badge value="{{ ucfirst($fee->status) }}"
                    class="{{ $fee->status === 'active' ? 'badge-success' : 'badge-error' }}" />
            @endscope

            @scope('actions', $fee)
                <div class="flex items-center space-x-2">
                    <x-button icon="o-pencil" class="btn-sm btn-info btn-outline"
                        wire:click="edit({{ $fee->id }})" spinner />
                    <x-button icon="o-trash" class="btn-sm btn-outline btn-error"
                        wire:click="delete({{ $fee->id }})" wire:confirm="Are you sure?" spinner />
                </div>
            @endscope

            <x-slot:empty>
                <x-alert class="alert-error" title="No restoration fees found." />
            </x-slot:empty>
        </x-table>
    </x-card>

    <x-modal wire:model="modal" title="{{ $id ? 'Edit Restoration Fee' : 'New Restoration Fee' }}">
        <x-form wire:submit="save">
            <x-input label="Name" wire:model="name" placeholder="Enter fee name" />
            <x-input label="Amount" wire:model="amount" type="number" step="0.01" min="0" placeholder="Enter amount" />
            <x-select label="Status" wire:model="status" :options="$statusoptions" option-label="label" option-value="id" />

            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.modal = false" />
                <x-button label="{{ $id ? 'Update' : 'Save' }}" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
