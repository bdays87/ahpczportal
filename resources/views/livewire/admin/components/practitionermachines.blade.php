<div>
    <x-card title="Machines used" subtitle="Equipment practitioners have used, with the period of use. Records are kept even when a machine is no longer in use." separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            @unless($customer)
                <x-input placeholder="Search machine, serial no, practitioner" wire:model.live.debounce.400ms="search" icon="o-magnifying-glass" class="input-sm" />
                <x-select wire:model.live="statusfilter" placeholder="All statuses" option-label="name" option-value="id"
                    :options="[['id'=>'IN_USE','name'=>'In use'],['id'=>'NOT_IN_USE','name'=>'Not in use']]" class="select-sm" />
            @endunless
            <x-button label="Add machine" icon="o-plus" class="btn-sm btn-primary" wire:click="openmodal" />
        </x-slot:menu>

        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        @unless($customer) <th>Practitioner</th> @endunless
                        <th>Machine</th>
                        <th>Make / Model</th>
                        <th>Serial no</th>
                        <th>Asset tag</th>
                        <th>Location</th>
                        <th>Period</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($machines as $machine)
                        <tr wire:key="machine-{{ $machine->id }}">
                            @unless($customer)
                                <td>
                                    {{ trim(($machine->customer->name ?? '').' '.($machine->customer->surname ?? '')) ?: '—' }}
                                    <div class="text-xs text-gray-400">{{ $machine->customer->regnumber ?? '' }}</div>
                                </td>
                            @endunless
                            <td>{{ $machine->name }}</td>
                            <td>{{ trim($machine->make.' '.$machine->model) ?: '—' }}</td>
                            <td>{{ $machine->serial_number ?: '—' }}</td>
                            <td>{{ $machine->asset_tag ?: '—' }}</td>
                            <td>{{ $machine->location ?: '—' }}</td>
                            <td>
                                {{ $machine->date_from?->format('d M Y') ?? '—' }}
                                to
                                {{ $machine->date_to?->format('d M Y') ?? 'present' }}
                            </td>
                            <td>
                                @if($machine->status === 'IN_USE')
                                    <x-badge value="In use" class="badge-success badge-soft" />
                                @else
                                    <x-badge value="Not in use" class="badge-ghost" />
                                @endif
                            </td>
                            <td class="whitespace-nowrap">
                                <x-button icon="o-pencil" class="btn-xs btn-outline" wire:click="openmodal({{ $machine->id }})" />
                                @if($machine->status === 'IN_USE')
                                    <x-button label="Retire" class="btn-xs btn-outline btn-warning" wire:click="retire({{ $machine->id }})"
                                        wire:confirm="Mark this machine as no longer in use (ends today)?" spinner />
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center text-gray-400">No machines recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $machines->links() }}</div>
    </x-card>

    <x-modal wire:model="modal" title="{{ $machine_id ? 'Edit machine' : 'Add machine' }}" separator box-class="max-w-2xl">
        @unless($customer)
            <div class="mb-3">
                <p class="text-xs text-gray-500 uppercase font-medium mb-1">Practitioner</p>
                @if($machine_customer_id)
                    <div class="flex items-center justify-between border border-gray-200 rounded-lg px-3 py-2">
                        <span>{{ $machine_customer_name }}</span>
                        <x-button icon="o-x-mark" class="btn-xs btn-ghost text-error" wire:click="clearcustomer" />
                    </div>
                @else
                    <x-input placeholder="Search practitioner by name or reg number" wire:model.live.debounce.400ms="customersearch" icon="o-magnifying-glass" />
                    @if(strlen(trim((string) $customersearch)) >= 2)
                        <div class="max-h-48 overflow-auto border border-gray-200 rounded-lg divide-y mt-1">
                            @forelse($customers as $c)
                                <button type="button" wire:click="pickcustomer({{ $c->id }})" class="w-full text-left px-3 py-2 flex justify-between hover:bg-base-200">
                                    <span>{{ trim($c->name.' '.$c->surname) }}</span>
                                    <span class="text-xs text-gray-400">{{ $c->regnumber }}</span>
                                </button>
                            @empty
                                <div class="px-3 py-2 text-sm text-gray-400">No practitioners found.</div>
                            @endforelse
                        </div>
                    @endif
                @endif
                @error('machine_customer_id') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        @endunless

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <x-input label="Machine name" wire:model="name" placeholder="e.g. Haematology analyser" />
            <x-input label="Serial number" wire:model="serial_number" />
            <x-input label="Make" wire:model="make" />
            <x-input label="Model" wire:model="model" />
            <x-input label="Asset tag" wire:model="asset_tag" />
            <x-input label="Location" wire:model="location" />
            <x-input label="Used from" type="date" wire:model="date_from" />
            <x-input label="Used until" type="date" wire:model="date_to" hint="Leave empty while still in use." />
            <x-select label="Status" wire:model="status" option-label="name" option-value="id"
                :options="[['id'=>'IN_USE','name'=>'In use'],['id'=>'NOT_IN_USE','name'=>'Not in use']]" />
        </div>
        <x-textarea label="Notes" wire:model="notes" class="mt-3" />
        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.modal = false" />
            <x-button label="Save" class="btn-primary" wire:click="save" spinner="save" />
        </x-slot:actions>
    </x-modal>
</div>
