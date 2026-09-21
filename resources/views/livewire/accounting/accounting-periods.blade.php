<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />

    <x-card title="Accounting Periods" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            <x-button icon="o-plus" label="New Period" class="btn-primary" wire:click="$set('modal',true)" spinner />
        </x-slot:menu>

        <x-table :headers="$headers" :rows="$periods">
            @scope('cell_status', $period)
                @php
                    $cls = match($period->status) {
                        'OPEN'   => 'badge-success',
                        'CLOSED' => 'badge-warning',
                        'LOCKED' => 'badge-error',
                        default  => 'badge-ghost',
                    };
                @endphp
                <span class="badge {{ $cls }}">{{ $period->status }}</span>
            @endscope

            @scope('actions', $period)
                <div class="flex gap-1">
                    @if($period->status === 'OPEN')
                        <x-button icon="o-pencil" class="btn-xs btn-info btn-outline"
                            wire:click="edit({{ $period->id }})" spinner />
                        <x-button icon="o-lock-closed" class="btn-xs btn-warning btn-outline"
                            wire:click="close({{ $period->id }})" wire:confirm="Close this period?" spinner />
                        <x-button icon="o-trash" class="btn-xs btn-error btn-outline"
                            wire:click="delete({{ $period->id }})" wire:confirm="Delete this period?" spinner />
                    @elseif($period->status === 'CLOSED')
                        <x-button icon="o-lock-closed" class="btn-xs btn-error btn-outline"
                            wire:click="lock({{ $period->id }})" wire:confirm="Lock this period? This cannot be undone." spinner />
                    @endif
                </div>
            @endscope

            <x-slot:empty>
                <x-alert class="alert-info" title="No accounting periods found. Create one to get started." />
            </x-slot:empty>
        </x-table>
    </x-card>

    {{-- Create / Edit Modal --}}
    <x-modal wire:model="modal" title="{{ $id ? 'Edit Period' : 'New Accounting Period' }}">
        <x-form wire:submit="save">
            <div class="grid grid-cols-2 gap-3">
                <x-input label="Period Name" wire:model="name" placeholder="e.g. January 2026" class="col-span-2" />
                <x-input label="Year"  wire:model="year"  type="number" />
                <x-select label="Month" wire:model="month" :options="collect(range(1,12))->map(fn($m)=>['id'=>$m,'name'=>date('F',mktime(0,0,0,$m,1))])" option-label="name" option-value="id" />
                <x-input label="Start Date" wire:model="start_date" type="date" />
                <x-input label="End Date"   wire:model="end_date"   type="date" />
            </div>
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.modal=false" />
                <x-button label="Save" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
