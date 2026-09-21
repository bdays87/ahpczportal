<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />

    <x-card title="Tax Rates" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            <x-input placeholder="Search…" wire:model.live="search" icon="o-magnifying-glass" />
            <x-button icon="o-plus" label="New Tax Rate" class="btn-primary" wire:click="$set('modal',true)" spinner />
        </x-slot:menu>

        <x-table :headers="$headers" :rows="$taxRates">
            @scope('cell_rate', $tax)
                <span class="font-mono">{{ number_format($tax->rate, 2) }}%</span>
            @endscope

            @scope('cell_status', $tax)
                <span class="badge {{ $tax->status === 'active' ? 'badge-success' : 'badge-ghost' }}">
                    {{ strtoupper($tax->status) }}
                </span>
            @endscope

            @scope('actions', $tax)
                <div class="flex gap-1">
                    <x-button icon="o-pencil" class="btn-xs btn-ghost btn-outline"
                        wire:click="edit({{ $tax->id }})" spinner />
                    <x-button icon="o-trash" class="btn-xs btn-error btn-outline"
                        wire:click="delete({{ $tax->id }})" wire:confirm="Delete this tax rate?" spinner />
                </div>
            @endscope

            <x-slot:empty>
                <x-alert class="alert-info" title="No tax rates found." />
            </x-slot:empty>
        </x-table>
    </x-card>

    {{-- Create / Edit Modal --}}
    <x-modal wire:model="modal" title="{{ $id ? 'Edit Tax Rate' : 'New Tax Rate' }}">
        <x-form wire:submit="save">
            <x-input label="Tax Code *" wire:model="code" placeholder="e.g., VAT15" />
            <x-input label="Name *" wire:model="name" placeholder="e.g., VAT 15%" />
            <x-input label="Rate (%) *" wire:model="rate" type="number" step="0.01" placeholder="e.g., 15.00" />
            <x-textarea label="Description" wire:model="description" rows="2" />
            <x-select label="Tax Liability Account *" wire:model="tax_account_id" :options="$taxAccounts" option-label="name" option-value="id" />
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
