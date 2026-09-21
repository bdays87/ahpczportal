<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />

    <x-card title="Chart of Accounts" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            <x-input placeholder="Search code or name…" wire:model.live="search" icon="o-magnifying-glass" />
            <x-select wire:model.live="filterType" placeholder="All Types"
                :options="[
                    ['id'=>'ASSET','name'=>'Asset'],
                    ['id'=>'LIABILITY','name'=>'Liability'],
                    ['id'=>'EQUITY','name'=>'Equity'],
                    ['id'=>'INCOME','name'=>'Income'],
                    ['id'=>'EXPENSE','name'=>'Expense'],
                ]" option-label="name" option-value="id" />
            <x-button icon="o-plus" label="New Account" class="btn-primary" wire:click="$set('modal',true)" spinner />
        </x-slot:menu>

        <x-table :headers="$headers" :rows="$accounts">
            @scope('cell_account_type', $account)
                @php
                    $cls = match($account->account_type) {
                        'ASSET'     => 'badge-info',
                        'LIABILITY' => 'badge-warning',
                        'EQUITY'    => 'badge-secondary',
                        'INCOME'    => 'badge-success',
                        'EXPENSE'   => 'badge-error',
                        default     => 'badge-ghost',
                    };
                @endphp
                <span class="badge {{ $cls }}">{{ $account->account_type }}</span>
            @endscope

            @scope('cell_normal_balance', $account)
                <span class="badge badge-outline">{{ $account->normal_balance }}</span>
            @endscope

            @scope('actions', $account)
                <div class="flex gap-1">
                    <x-button icon="o-list-bullet" class="btn-xs btn-info btn-outline" tooltip="View Ledger"
                        wire:click="viewLedger({{ $account->id }})" spinner />
                    <x-button icon="o-pencil" class="btn-xs btn-ghost btn-outline"
                        wire:click="edit({{ $account->id }})" spinner />
                    <x-button icon="o-trash" class="btn-xs btn-error btn-outline"
                        wire:click="delete({{ $account->id }})" wire:confirm="Delete this account?" spinner />
                </div>
            @endscope

            <x-slot:empty>
                <x-alert class="alert-info" title="No accounts found." />
            </x-slot:empty>
        </x-table>
    </x-card>

    {{-- Create / Edit Modal --}}
    <x-modal wire:model="modal" title="{{ $id ? 'Edit Account' : 'New Account' }}" box-class="max-w-2xl">
        <x-form wire:submit="save">
            <div class="grid grid-cols-2 gap-3">
                <x-input label="Account Code" wire:model="code" placeholder="e.g. 1001" />
                <x-select label="Account Type" wire:model.live="account_type"
                    :options="[
                        ['id'=>'ASSET','name'=>'Asset'],
                        ['id'=>'LIABILITY','name'=>'Liability'],
                        ['id'=>'EQUITY','name'=>'Equity'],
                        ['id'=>'INCOME','name'=>'Income'],
                        ['id'=>'EXPENSE','name'=>'Expense'],
                    ]" option-label="name" option-value="id" />
                <x-input label="Account Name" wire:model="name" class="col-span-2" />
                <x-input label="Sub-type" wire:model="account_subtype" placeholder="e.g. Current Asset" />
                <x-select label="Normal Balance" wire:model="normal_balance"
                    :options="[['id'=>'DEBIT','name'=>'Debit'],['id'=>'CREDIT','name'=>'Credit']]"
                    option-label="name" option-value="id" />
                <x-select label="Parent Account" wire:model="parent_id" :options="$parentAccounts"
                    option-label="name" option-value="id" placeholder="(none)" />
                <x-select label="Cost Center" wire:model="cost_center_id" :options="$costCenters"
                    option-label="name" option-value="id" placeholder="(none)" />
                <x-select label="Currency" wire:model="currency_id" :options="$currencies"
                    option-label="name" option-value="id" placeholder="(default)" />
                <x-select label="Status" wire:model="status"
                    :options="[['id'=>'active','name'=>'Active'],['id'=>'inactive','name'=>'Inactive']]"
                    option-label="name" option-value="id" />
                <div class="flex gap-4 items-center col-span-2">
                    <x-checkbox label="Header Account (no direct posting)" wire:model="is_header" />
                    <x-checkbox label="Allow Direct Posting" wire:model="allow_direct_posting" />
                </div>
                <x-textarea label="Description" wire:model="description" class="col-span-2" rows="2" />
            </div>
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.modal=false" />
                <x-button label="Save" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>

    {{-- Ledger Modal --}}
    <x-modal wire:model="ledgerModal" title="General Ledger — {{ $selectedAccount?->code }} {{ $selectedAccount?->name }}" box-class="max-w-4xl">
        <div class="flex gap-3 mb-4">
            <x-input label="From" wire:model.live="ledgerFrom" type="date" />
            <x-input label="To"   wire:model.live="ledgerTo"   type="date" />
        </div>
        <div class="overflow-x-auto">
            <table class="table table-xs">
                <thead>
                    <tr>
                        <th>Date</th><th>Reference</th><th>Description</th>
                        <th class="text-right">Debit</th><th class="text-right">Credit</th>
                        <th class="text-right">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @php $running = 0; $isDebit = in_array($selectedAccount?->account_type,['ASSET','EXPENSE']); @endphp
                    @forelse($ledgerEntries as $line)
                        @php $running += $isDebit ? ($line->debit - $line->credit) : ($line->credit - $line->debit); @endphp
                        <tr>
                            <td>{{ $line->journalEntry?->entry_date?->format('d M Y') }}</td>
                            <td>{{ $line->journalEntry?->reference_number }}</td>
                            <td>{{ $line->description ?? $line->journalEntry?->description }}</td>
                            <td class="text-right">{{ number_format($line->debit,2) }}</td>
                            <td class="text-right">{{ number_format($line->credit,2) }}</td>
                            <td class="text-right font-semibold">{{ number_format($running,2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-gray-400">No entries found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-slot:actions>
            <x-button label="Close" @click="$wire.ledgerModal=false" />
        </x-slot:actions>
    </x-modal>
</div>
