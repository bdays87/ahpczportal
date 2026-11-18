<div>
<x-breadcrumbs :items="$breadcrumbs"    class="bg-base-300 p-3 rounded-box mt-2" />

<x-card title="Application Sessions" separator class="mt-5 border-2 border-gray-200">
    <x-slot:menu>
        <x-button label="New" responsive icon="o-plus" class="btn-outline" @click="$wire.modal = true" />
    </x-slot:menu>

    <x-table :headers="$headers" :rows="$applicationsessions">
        @scope('cell_renewal_window_label', $applicationsession)
        {{ $applicationsession->renewal_window_label }}
        @endscope
        @scope('cell_is_active', $applicationsession)
            @if($applicationsession->is_active)
                @if($applicationsession->isRenewalWindowOpen())
                    <x-badge value="Active - Window Open" class="badge-success" />
                @else
                    <x-badge value="Active - Window Closed" class="badge-warning" />
                @endif
            @else
                <x-badge value="Inactive" class="badge-neutral" />
            @endif
        @endscope
        @scope('cell_user', $applicationsession)
        {{ $applicationsession->user->name }} {{ $applicationsession->user->surname }}
        @endscope
        @scope('actions', $applicationsession)
        <div class="flex items-center space-x-2">
            @can('session.modify')
            <x-button
                icon="{{ $applicationsession->is_active ? 'o-pause' : 'o-play' }}"
                class="btn-sm {{ $applicationsession->is_active ? 'btn-warning' : 'btn-success' }} btn-outline"
                wire:click="toggleactive({{ $applicationsession->id }})"
                tooltip="{{ $applicationsession->is_active ? 'Deactivate' : 'Activate' }}"
                spinner />
            <x-button icon="o-pencil" class="btn-sm btn-info btn-outline"
                wire:click="edit({{ $applicationsession->id }})" spinner />
            <x-button icon="o-trash" class="btn-sm btn-outline btn-error"
                wire:click="delete({{ $applicationsession->id }})" confirm="Are you sure?" spinner />
            @endcan
        </div>
        @endscope
        <x-slot:empty>
            <x-alert class="alert-error" title="No application sessions found." />
        </x-slot:empty>
    </x-table>
</x-card>

<x-modal wire:model="modal"  title="{{ $id ? 'Edit Application Session' : 'New Application Session' }}">
    <x-form wire:submit="save">
        <div class="grid gap-4">
            <x-input label="Renewal Year" wire:model="year" type="number" placeholder="e.g. 2027" hint="The year practitioners are renewing for" />

            <div class="grid grid-cols-2 gap-4">
                <x-input
                    label="Renewal Window Opens"
                    wire:model="renewal_window_start_date"
                    type="date"
                    icon="o-calendar"
                />
                <x-input
                    label="Renewal Window Closes"
                    wire:model="renewal_window_end_date"
                    type="date"
                    icon="o-calendar"
                />
            </div>

            <x-alert icon="o-information-circle" class="alert-info text-sm">
                Practitioners can only renew their certificates between these dates. Multiple sessions can be active at the same time.
                <br>
                <strong>Example:</strong> Activate both 2026 (Jan-Dec 2026) and 2027 (Oct 2026 - Mar 2027). Late renewers can still renew for 2026, while those already renewed can renew for 2027.
            </x-alert>
        </div>

        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.modal = false" />
            <x-button label="{{ $id ? 'Update' : 'Save' }}" type="submit" class="btn-primary" spinner="save" />
        </x-slot:actions>
    </x-form>
</x-modal>
</div>
