<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />

    <x-card title="Budgets" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            <x-input placeholder="Search…" wire:model.live="search" icon="o-magnifying-glass" />
            <x-button icon="o-plus" label="New Budget" class="btn-primary" wire:click="$set('modal',true)" spinner />
        </x-slot:menu>

        <x-table :headers="$headers" :rows="$budgets">
            @scope('cell_status', $budget)
                @php
                    $cls = match($budget->status) {
                        'DRAFT'    => 'badge-ghost',
                        'ACTIVE'   => 'badge-success',
                        'CLOSED'   => 'badge-warning',
                        default    => 'badge-info',
                    };
                @endphp
                <span class="badge {{ $cls }}">{{ $budget->status }}</span>
            @endscope

            @scope('actions', $budget)
                <div class="flex gap-1">
                    <x-button icon="o-eye" class="btn-xs btn-info btn-outline"
                        wire:click="viewBudget({{ $budget->id }})" spinner />
                    @if($budget->status === 'DRAFT')
                        <x-button icon="o-pencil" class="btn-xs btn-ghost btn-outline"
                            wire:click="edit({{ $budget->id }})" spinner />
                        <x-button icon="o-check" class="btn-xs btn-success btn-outline" tooltip="Activate"
                            wire:click="activate({{ $budget->id }})" wire:confirm="Activate this budget?" spinner />
                        <x-button icon="o-trash" class="btn-xs btn-error btn-outline"
                            wire:click="delete({{ $budget->id }})" wire:confirm="Delete?" spinner />
                    @elseif($budget->status === 'ACTIVE')
                        <x-button icon="o-lock-closed" class="btn-xs btn-warning btn-outline" tooltip="Close"
                            wire:click="close({{ $budget->id }})" wire:confirm="Close this budget?" spinner />
                    @endif
                </div>
            @endscope

            <x-slot:empty>
                <x-alert class="alert-info" title="No budgets found." />
            </x-slot:empty>
        </x-table>
    </x-card>

    {{-- Create / Edit Modal --}}
    <x-modal wire:model="modal" title="{{ $id ? 'Edit Budget' : 'New Budget' }}" box-class="max-w-5xl">
        <x-form wire:submit="save">
            <div class="grid grid-cols-3 gap-3 mb-4">
                <x-input label="Budget Name *" wire:model="name" class="col-span-3" />
                <x-input label="Start Date *" wire:model="start_date" type="date" />
                <x-input label="End Date *" wire:model="end_date" type="date" />
                <x-select label="Cost Center" wire:model="cost_center_id" :options="$costCenters" option-label="name" option-value="id" placeholder="(none)" />
                <x-textarea label="Notes" wire:model="notes" rows="2" class="col-span-3" />
            </div>

            <div class="mb-3 font-semibold text-lg">Budget Lines</div>
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th style="width: 40%">Account</th>
                            <th style="width: 15%" class="text-right">Jan-Mar</th>
                            <th style="width: 15%" class="text-right">Apr-Jun</th>
                            <th style="width: 15%" class="text-right">Jul-Sep</th>
                            <th style="width: 15%" class="text-right">Oct-Dec</th>
                            <th style="width: 5%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lines as $index => $line)
                            <tr wire:key="line-{{ $index }}">
                                <td>
                                    <select wire:model="lines.{{ $index }}.account_id" class="select select-sm select-bordered w-full">
                                        <option value="">Select Account</option>
                                        @foreach($accounts as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->code }} — {{ $acc->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input wire:model="lines.{{ $index }}.q1_amount" type="number" step="0.01" class="input input-sm input-bordered w-full text-right" /></td>
                                <td><input wire:model="lines.{{ $index }}.q2_amount" type="number" step="0.01" class="input input-sm input-bordered w-full text-right" /></td>
                                <td><input wire:model="lines.{{ $index }}.q3_amount" type="number" step="0.01" class="input input-sm input-bordered w-full text-right" /></td>
                                <td><input wire:model="lines.{{ $index }}.q4_amount" type="number" step="0.01" class="input input-sm input-bordered w-full text-right" /></td>
                                <td>
                                    <x-button icon="o-trash" class="btn-xs btn-ghost" wire:click="removeLine({{ $index }})" spinner />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="text-right font-semibold">Totals:</td>
                            <td class="text-right font-mono font-semibold">{{ number_format($totals['q1'], 2) }}</td>
                            <td class="text-right font-mono font-semibold">{{ number_format($totals['q2'], 2) }}</td>
                            <td class="text-right font-mono font-semibold">{{ number_format($totals['q3'], 2) }}</td>
                            <td class="text-right font-mono font-semibold">{{ number_format($totals['q4'], 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <x-button icon="o-plus" label="Add Line" wire:click="addLine" class="btn-sm mt-2" spinner />

            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.modal=false" />
                <x-button label="Save" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>

    {{-- View Budget Modal --}}
    <x-modal wire:model="viewModal" title="Budget — {{ $selectedBudget?->name }}" box-class="max-w-4xl">
        @if($selectedBudget)
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div><strong>Period:</strong> {{ $selectedBudget->start_date->format('d M Y') }} — {{ $selectedBudget->end_date->format('d M Y') }}</div>
                <div><strong>Status:</strong> <span class="badge badge-success">{{ $selectedBudget->status }}</span></div>
                @if($selectedBudget->cost_center_id)
                    <div><strong>Cost Center:</strong> {{ $selectedBudget->costCenter?->name }}</div>
                @endif
            </div>
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Account</th>
                        <th class="text-right">Q1</th>
                        <th class="text-right">Q2</th>
                        <th class="text-right">Q3</th>
                        <th class="text-right">Q4</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($selectedBudget->lines as $line)
                        <tr>
                            <td>{{ $line->account?->code }} — {{ $line->account?->name }}</td>
                            <td class="text-right font-mono">{{ number_format($line->q1_amount, 2) }}</td>
                            <td class="text-right font-mono">{{ number_format($line->q2_amount, 2) }}</td>
                            <td class="text-right font-mono">{{ number_format($line->q3_amount, 2) }}</td>
                            <td class="text-right font-mono">{{ number_format($line->q4_amount, 2) }}</td>
                            <td class="text-right font-mono">{{ number_format($line->annual_amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        <x-slot:actions>
            <x-button label="Close" @click="$wire.viewModal=false" />
        </x-slot:actions>
    </x-modal>
</div>
