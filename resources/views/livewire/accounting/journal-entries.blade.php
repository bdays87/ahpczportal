<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />

    <x-card title="Journal Entries" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            <x-input placeholder="Search reference…" wire:model.live="search" icon="o-magnifying-glass" />
            <x-select wire:model.live="filterStatus" placeholder="All Status"
                :options="[
                    ['id'=>'DRAFT','name'=>'Draft'],
                    ['id'=>'POSTED','name'=>'Posted'],
                    ['id'=>'REVERSED','name'=>'Reversed'],
                ]" option-label="name" option-value="id" />
            <x-button icon="o-plus" label="New Entry" class="btn-primary" wire:click="$set('modal',true)" spinner />
        </x-slot:menu>

        <x-table :headers="$headers" :rows="$entries" with-pagination>
            @scope('cell_status', $entry)
                @php
                    $cls = match($entry->status) {
                        'DRAFT'    => 'badge-ghost',
                        'POSTED'   => 'badge-success',
                        'REVERSED' => 'badge-warning',
                        default    => 'badge-info',
                    };
                @endphp
                <span class="badge {{ $cls }}">{{ $entry->status }}</span>
            @endscope

            @scope('cell_total_debit', $entry)
                <span class="font-mono">{{ number_format($entry->total_debit, 2) }}</span>
            @endscope

            @scope('cell_total_credit', $entry)
                <span class="font-mono">{{ number_format($entry->total_credit, 2) }}</span>
            @endscope

            @scope('actions', $entry)
                <div class="flex gap-1">
                    <x-button icon="o-eye" class="btn-xs btn-info btn-outline"
                        wire:click="viewEntry({{ $entry->id }})" spinner />
                    @if($entry->status === 'DRAFT')
                        <x-button icon="o-pencil" class="btn-xs btn-ghost btn-outline"
                            wire:click="edit({{ $entry->id }})" spinner />
                        <x-button icon="o-check" class="btn-xs btn-success btn-outline" tooltip="Post"
                            wire:click="post({{ $entry->id }})" wire:confirm="Post this entry?" spinner />
                        <x-button icon="o-trash" class="btn-xs btn-error btn-outline"
                            wire:click="delete({{ $entry->id }})" wire:confirm="Delete?" spinner />
                    @elseif($entry->status === 'POSTED')
                        <x-button icon="o-arrow-uturn-left" class="btn-xs btn-warning btn-outline" tooltip="Reverse"
                            wire:click="openReverseModal({{ $entry->id }})" spinner />
                    @endif
                </div>
            @endscope

            <x-slot:empty>
                <x-alert class="alert-info" title="No journal entries found." />
            </x-slot:empty>
        </x-table>
    </x-card>

    {{-- Create / Edit Modal --}}
    <x-modal wire:model="modal" title="{{ $id ? 'Edit Entry' : 'New Journal Entry' }}" box-class="max-w-6xl">
        <x-form wire:submit="save">
            <div class="grid grid-cols-3 gap-3 mb-4">
                <x-input label="Entry Date" wire:model="entry_date" type="date" />
                <x-select label="Currency" wire:model="currency_id" :options="$currencies"
                    option-label="name" option-value="id" />
                <x-input label="Exchange Rate" wire:model="exchange_rate" type="number" step="0.000001" />
                <x-input label="Description" wire:model="description" class="col-span-3" />
                <x-textarea label="Narration" wire:model="narration" rows="2" class="col-span-3" />
            </div>

            <div class="mb-3 font-semibold text-lg">Journal Lines</div>
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th style="width: 30%">Account</th>
                            <th style="width: 20%">Cost Center</th>
                            <th style="width: 25%">Description</th>
                            <th style="width: 10%" class="text-right">Debit</th>
                            <th style="width: 10%" class="text-right">Credit</th>
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
                                <td>
                                    <select wire:model="lines.{{ $index }}.cost_center_id" class="select select-sm select-bordered w-full">
                                        <option value="">(none)</option>
                                        @foreach($costCenters as $cc)
                                            <option value="{{ $cc->id }}">{{ $cc->code }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input wire:model="lines.{{ $index }}.description" class="input input-sm input-bordered w-full" /></td>
                                <td><input wire:model="lines.{{ $index }}.debit" type="number" step="0.01" class="input input-sm input-bordered w-full text-right" /></td>
                                <td><input wire:model="lines.{{ $index }}.credit" type="number" step="0.01" class="input input-sm input-bordered w-full text-right" /></td>
                                <td>
                                    <x-button icon="o-trash" class="btn-xs btn-ghost" wire:click="removeLine({{ $index }})" spinner />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right font-semibold">Totals:</td>
                            <td class="text-right font-mono font-semibold">{{ number_format($totals['debit'], 2) }}</td>
                            <td class="text-right font-mono font-semibold">{{ number_format($totals['credit'], 2) }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="6">
                                @if($totals['balanced'])
                                    <span class="badge badge-success">✓ Balanced</span>
                                @else
                                    <span class="badge badge-error">✗ Not Balanced</span>
                                @endif
                            </td>
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

    {{-- View Entry Modal --}}
    <x-modal wire:model="viewModal" title="Journal Entry — {{ $selectedEntry?->reference_number }}" box-class="max-w-4xl">
        @if($selectedEntry)
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div><strong>Date:</strong> {{ $selectedEntry->entry_date->format('d M Y') }}</div>
                <div><strong>Status:</strong> <span class="badge badge-success">{{ $selectedEntry->status }}</span></div>
                <div class="col-span-2"><strong>Description:</strong> {{ $selectedEntry->description }}</div>
            </div>
            <table class="table table-sm">
                <thead><tr><th>Account</th><th>Description</th><th class="text-right">Debit</th><th class="text-right">Credit</th></tr></thead>
                <tbody>
                    @foreach($selectedEntry->lines as $line)
                        <tr>
                            <td>{{ $line->account?->code }} — {{ $line->account?->name }}</td>
                            <td>{{ $line->description }}</td>
                            <td class="text-right font-mono">{{ number_format($line->debit, 2) }}</td>
                            <td class="text-right font-mono">{{ number_format($line->credit, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="text-right font-semibold">Totals:</td>
                        <td class="text-right font-mono font-semibold">{{ number_format($selectedEntry->total_debit, 2) }}</td>
                        <td class="text-right font-mono font-semibold">{{ number_format($selectedEntry->total_credit, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        @endif
        <x-slot:actions>
            <x-button label="Close" @click="$wire.viewModal=false" />
        </x-slot:actions>
    </x-modal>

    {{-- Reverse Modal --}}
    <x-modal wire:model="reverseModal" title="Reverse Journal Entry">
        <x-form wire:submit="reverse">
            <x-input label="Reason for Reversal" wire:model="reverseReason" placeholder="Explain why this entry is being reversed" />
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.reverseModal=false" />
                <x-button label="Reverse Entry" type="submit" class="btn-warning" spinner="reverse" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
