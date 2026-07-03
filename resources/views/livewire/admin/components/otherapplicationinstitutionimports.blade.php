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
                            <div class="flex items-center justify-between px-3 py-2 gap-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="truncate">{{ $employee['name'] }}</span>
                                    <span class="text-xs text-gray-400">{{ $employee['regnumber'] }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if($i === 0)
                                        <x-badge value="In-charge" class="badge-primary badge-sm" />
                                    @else
                                        <select wire:model="employees.{{ $i }}.role" class="select select-bordered select-xs">
                                            <option value="EMPLOYEE">Employee</option>
                                            <option value="RESIDENT_SCIENTIST">Resident Scientist</option>
                                        </select>
                                    @endif
                                    <x-button icon="o-x-mark" class="btn-xs btn-ghost text-error"
                                        wire:click="removeemployee({{ $employee['id'] }})" />
                                </div>
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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <x-input label="Registration date" type="date" wire:model="assignRegistrationDate"
                    hint="The invoice is generated & settled for this date." />
                <x-select label="Employment type (applies to all)" wire:model="employmenttype"
                    :options="[['id'=>'PERMANENT','name'=>'Permanent'],['id'=>'CONTRACT','name'=>'Contract'],['id'=>'LOCUM','name'=>'Locum']]"
                    option-label="name" option-value="id" />
            </div>
            @error('assignRegistrationDate') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            @error('employees') <p class="text-sm text-red-600">{{ $message }}</p> @enderror

            {{-- Services / tests offered --}}
            <div class="border-t border-gray-200 pt-3">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs text-gray-500 uppercase font-medium">Services / tests offered</p>
                    <x-button label="Add service" icon="o-plus" class="btn-xs btn-outline" wire:click="addservice" />
                </div>
                @forelse($services as $si => $service)
                    <div class="border border-gray-200 rounded-lg p-2 mb-2">
                        <div class="flex items-center gap-2">
                            <x-input placeholder="Service / test name (e.g. Haematology)" wire:model="services.{{ $si }}.name" class="flex-1" />
                            <x-button icon="o-trash" class="btn-xs btn-ghost text-error" wire:click="removeservice({{ $si }})" />
                        </div>
                        <x-input placeholder="Description (optional)" wire:model="services.{{ $si }}.description" class="mt-1" />
                        {{-- Sub-tests --}}
                        <div class="ml-4 mt-2">
                            @foreach($service['subtests'] ?? [] as $sti => $subtest)
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-xs text-gray-400">&#8627;</span>
                                    <x-input placeholder="Sub-test name" wire:model="services.{{ $si }}.subtests.{{ $sti }}.name" class="flex-1" />
                                    <x-button icon="o-x-mark" class="btn-xs btn-ghost text-error" wire:click="removesubtest({{ $si }}, {{ $sti }})" />
                                </div>
                            @endforeach
                            <x-button label="Add sub-test" icon="o-plus" class="btn-xs btn-ghost" wire:click="addsubtest({{ $si }})" />
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-400">No services added — the institution name will be used as the default service.</p>
                @endforelse
            </div>

            {{-- Accreditations --}}
            <div class="border-t border-gray-200 pt-3">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs text-gray-500 uppercase font-medium">Accreditations</p>
                    <x-button label="Add accreditation" icon="o-plus" class="btn-xs btn-outline" wire:click="addaccreditation" />
                </div>
                @forelse($accreditations as $ai => $accreditation)
                    <div class="flex items-center gap-2 mb-2">
                        <x-input placeholder="Accreditation (e.g. SADCAS, National Certification Programme)" wire:model="accreditations.{{ $ai }}.name" class="flex-1" />
                        <x-input placeholder="Level (e.g. Level 2)" wire:model="accreditations.{{ $ai }}.level" class="w-40" />
                        <x-button icon="o-trash" class="btn-xs btn-ghost text-error" wire:click="removeaccreditation({{ $ai }})" />
                    </div>
                @empty
                    <p class="text-xs text-gray-400">No accreditations added.</p>
                @endforelse
            </div>
        </div>

        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.assignmodal = false" />
            <x-button label="Assign & Push (Approved)" icon="o-check" class="btn-primary" wire:click="assign" spinner="assign"
                :disabled="count($employees) === 0" />
        </x-slot:actions>
    </x-modal>
</div>
