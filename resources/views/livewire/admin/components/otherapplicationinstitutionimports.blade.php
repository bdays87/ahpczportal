<div>
    {{-- Upload --}}
    <div x-data="{ uploading: false }"
         x-on:livewire-upload-start="uploading = true"
         x-on:livewire-upload-finish="uploading = false"
         x-on:livewire-upload-error="uploading = false">
        <x-form wire:submit="saveimport">
            <p class="text-sm text-gray-500 mb-2">
                Upload the institutions (Facility Report) CSV. The report title and header rows are detected automatically.
                Each row is staged as <strong>PENDING</strong> with no owner — assign the practitioner-in-charge afterwards to push it
                into Other Applications as approved.
            </p>
            <div class="flex items-end gap-2">
                <x-input label="Institutions CSV (.csv)" wire:model="file" type="file" accept=".csv" class="flex-1" />
                <x-button label="Import" icon="o-arrow-up-tray" type="submit" class="btn-primary" spinner="saveimport"
                    x-bind:disabled="uploading" />
            </div>
            <p x-show="uploading" class="text-sm text-blue-600 mt-1 flex items-center gap-1">
                <span class="loading loading-spinner loading-xs"></span> Uploading file, please wait...
            </p>
        </x-form>
    </div>

    <div class="mt-4">
        <x-input placeholder="Search institution, reg no, city, province" wire:model.live.debounce.400ms="search" icon="o-magnifying-glass" />
    </div>

    <x-table :headers="$headers" :rows="$imports" with-pagination class="mt-3">
        @scope('cell_incharge', $import)
            {{ $import->customer ? trim($import->customer->name.' '.$import->customer->surname) : '—' }}
        @endscope

        @scope('cell_status', $import)
            @if($import->status === 'APPROVED')
                <x-badge value="APPROVED" class="badge-success badge-soft" />
            @else
                <x-badge value="PENDING" class="badge-warning badge-soft" />
            @endif
        @endscope

        @scope('cell_action', $import)
            <div class="flex items-center gap-2">
                @if($import->processed !== 'Y')
                    <x-button label="Assign & Approve" icon="o-user-plus" class="btn-xs btn-primary"
                        wire:click="openassign({{ $import->id }})" spinner />
                    <x-button icon="o-trash" class="btn-xs btn-outline btn-error"
                        wire:click="delete({{ $import->id }})" wire:confirm="Delete this staged institution?" spinner />
                @else
                    <x-badge value="Pushed" class="badge-ghost" />
                @endif
            </div>
        @endscope

        <x-slot:empty>
            <x-alert class="alert-warning" title="No institutions imported yet." />
        </x-slot:empty>
    </x-table>

    {{-- Assign employees modal --}}
    <x-modal wire:model="assignmodal" title="Assign Employees" box-class="max-w-xl"
        subtitle="{{ $assigninstitutionname }}">
        <div class="space-y-3">
            {{-- Selected employees (first = practitioner-in-charge) --}}
            <div>
                <p class="text-xs text-gray-500 uppercase font-medium mb-1">Employees ({{ count($employees) }})</p>
                @if(count($employees) > 0)
                    <div class="border border-gray-200 rounded-lg divide-y">
                        @foreach($employees as $i => $employee)
                            <div class="flex items-center justify-between px-3 py-2">
                                <div class="flex items-center gap-2">
                                    @if($i === 0)
                                        <x-badge value="In-charge" class="badge-primary badge-sm" />
                                    @else
                                        <span class="text-xs text-gray-400 w-16">Employee</span>
                                    @endif
                                    <span>{{ $employee['name'] }}</span>
                                    <span class="text-xs text-gray-400">{{ $employee['regnumber'] }}</span>
                                </div>
                                <x-button icon="o-x-mark" class="btn-xs btn-ghost text-error"
                                    wire:click="removeemployee({{ $employee['id'] }})" />
                            </div>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-400 mt-1">The first employee is the practitioner-in-charge.</p>
                @else
                    <p class="text-sm text-gray-400">No employees added yet. Search and add the practitioner-in-charge first.</p>
                @endif
            </div>

            {{-- Search to add --}}
            <x-input placeholder="Search practitioner by name or reg number to add" wire:model.live.debounce.400ms="customersearch" icon="o-magnifying-glass" />
            @if($customersearch && strlen($customersearch) >= 2)
                <div class="max-h-52 overflow-auto border border-gray-200 rounded-lg divide-y">
                    @forelse($customers as $customer)
                        @php $already = collect($employees)->contains(fn($e) => $e['id'] == $customer->id); @endphp
                        <button type="button" @disabled($already)
                            wire:click="addemployee({{ $customer->id }}, @js(trim($customer->name.' '.$customer->surname)), @js($customer->regnumber))"
                            class="w-full text-left px-3 py-2 flex justify-between items-center {{ $already ? 'opacity-40 cursor-not-allowed' : 'hover:bg-base-200' }}">
                            <span>{{ trim($customer->name.' '.$customer->surname) }}</span>
                            <span class="text-xs text-gray-400">{{ $customer->regnumber }} {{ $already ? '• added' : '' }}</span>
                        </button>
                    @empty
                        <div class="px-3 py-2 text-sm text-gray-400">No practitioners found.</div>
                    @endforelse
                </div>
            @endif

            <x-select label="Employment type (applies to all)" wire:model="employmenttype"
                :options="[['id'=>'PERMANENT','name'=>'Permanent'],['id'=>'CONTRACT','name'=>'Contract'],['id'=>'LOCUM','name'=>'Locum']]"
                option-label="name" option-value="id" />

            @error('employees') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.assignmodal = false" />
            <x-button label="Assign & Push (Approved)" icon="o-check" class="btn-primary" wire:click="assign" spinner="assign"
                :disabled="count($employees) === 0" />
        </x-slot:actions>
    </x-modal>
</div>
