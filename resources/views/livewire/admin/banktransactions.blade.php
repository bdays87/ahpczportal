<div>
    <x-breadcrumbs :items="$breadcrumbs"  class="bg-base-300 p-3 rounded-box mt-2"/>
    
    <x-card title="Bank Transactions" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            <x-input placeholder="Search transactions..." wire:model.live="search" icon="o-magnifying-glass" />
            <x-input placeholder="Search customer..." wire:model.live="customerSearch" icon="o-user" />
            @can('configurations.modify')
            <x-button icon="o-plus" label="New" class="btn-primary btn-sm" 
                wire:click="$set('modal', true)" spinner />
            <x-button icon="o-arrow-down-tray" label="Import" class="btn-primary btn-sm" 
                wire:click="$set('importmodal', true)" spinner />
            @endcan
        </x-slot:menu>

        {{-- Status Filter Tabs --}}
        <x-tabs wire:model="statusFilter" class="mb-4">
            <x-tab name="all" label="All" icon="o-list-bullet">
                <x-table :headers="$headers" :rows="$banktransactions" with-pagination>
                    @scope('cell_transaction_date', $txn)
                        {{ $txn->transaction_date->format('d M Y') }}
                    @endscope
                    
                    @scope('cell_amount', $txn)
                        <span class="font-mono">{{ $txn->currency?->code ?? 'USD' }} {{ number_format($txn->amount, 2) }}</span>
                    @endscope
                    
                    @scope('cell_customer.name', $txn)
                        @if($txn->customer)
                            <span class="text-xs">{{ $txn->customer->name }} {{ $txn->customer->surname }}</span>
                        @else
                            <span class="text-gray-400 text-xs">-</span>
                        @endif
                    @endscope
                    
                    @scope('cell_status', $txn)
                        <x-badge :value="$txn->status" 
                            class="{{ $txn->status === 'CLAIMED' ? 'badge-success' : 'badge-warning' }}" />
                    @endscope
                    
                    @scope('actions', $txn)
                        <div class="flex items-center gap-1">
                            @can('banktransactions.modify')
                                @if($txn->status === 'PENDING')
                                    <x-button icon="o-check" class="btn-xs btn-success btn-outline" 
                                        wire:click="claimTransaction({{ $txn->id }})" 
                                        tooltip="Claim to Customer" spinner />
                                    <x-button icon="o-pencil" class="btn-xs btn-info btn-outline" 
                                        wire:click="edit({{ $txn->id }})" spinner />
                                    <x-button icon="o-trash" class="btn-xs btn-error btn-outline" 
                                        wire:click="delete({{ $txn->id }})" wire:confirm="Delete?" spinner />
                                @else
                                    <x-button icon="o-x-mark" class="btn-xs btn-warning btn-outline" 
                                        wire:click="unclaimTransaction({{ $txn->id }})" 
                                        tooltip="Unclaim" wire:confirm="Unclaim this transaction?" spinner />
                                @endif
                            @endcan
                        </div>
                    @endscope
                    
                    <x-slot:empty>
                        <x-alert class="alert-info" title="No transactions found." />
                    </x-slot:empty>
                </x-table>
            </x-tab>
            
            <x-tab name="PENDING" label="Pending" icon="o-clock">
                <x-table :headers="$headers" :rows="$banktransactions" with-pagination>
                    @scope('cell_transaction_date', $txn)
                        {{ $txn->transaction_date->format('d M Y') }}
                    @endscope
                    
                    @scope('cell_amount', $txn)
                        <span class="font-mono">{{ $txn->currency?->code ?? 'USD' }} {{ number_format($txn->amount, 2) }}</span>
                    @endscope
                    
                    @scope('cell_customer.name', $txn)
                        <span class="text-gray-400 text-xs">Not claimed</span>
                    @endscope
                    
                    @scope('cell_status', $txn)
                        <x-badge value="PENDING" class="badge-warning" />
                    @endscope
                    
                    @scope('actions', $txn)
                        <div class="flex items-center gap-1">
                            @can('banktransactions.modify')
                                <x-button icon="o-check" class="btn-xs btn-success btn-outline" 
                                    wire:click="claimTransaction({{ $txn->id }})" 
                                    tooltip="Claim" spinner />
                                <x-button icon="o-pencil" class="btn-xs btn-info btn-outline" 
                                    wire:click="edit({{ $txn->id }})" spinner />
                                <x-button icon="o-trash" class="btn-xs btn-error btn-outline" 
                                    wire:click="delete({{ $txn->id }})" wire:confirm="Delete?" spinner />
                            @endcan
                        </div>
                    @endscope
                    
                    <x-slot:empty>
                        <x-alert class="alert-info" title="No pending transactions." />
                    </x-slot:empty>
                </x-table>
            </x-tab>
            
            <x-tab name="CLAIMED" label="Claimed" icon="o-check-circle">
                <x-table :headers="$headers" :rows="$banktransactions" with-pagination>
                    @scope('cell_transaction_date', $txn)
                        {{ $txn->transaction_date->format('d M Y') }}
                    @endscope
                    
                    @scope('cell_amount', $txn)
                        <span class="font-mono">{{ $txn->currency?->code ?? 'USD' }} {{ number_format($txn->amount, 2) }}</span>
                    @endscope
                    
                    @scope('cell_customer.name', $txn)
                        @if($txn->customer)
                            <span class="text-xs">{{ $txn->customer->name }} {{ $txn->customer->surname }}</span>
                        @else
                            <span class="text-gray-400 text-xs">-</span>
                        @endif
                    @endscope
                    
                    @scope('cell_status', $txn)
                        <x-badge value="CLAIMED" class="badge-success" />
                    @endscope
                    
                    @scope('actions', $txn)
                        <div class="flex items-center gap-1">
                            @can('banktransactions.modify')
                                <x-button icon="o-x-mark" class="btn-xs btn-warning btn-outline" 
                                    wire:click="unclaimTransaction({{ $txn->id }})" 
                                    tooltip="Unclaim" wire:confirm="Unclaim?" spinner />
                            @endcan
                        </div>
                    @endscope
                    
                    <x-slot:empty>
                        <x-alert class="alert-info" title="No claimed transactions." />
                    </x-slot:empty>
                </x-table>
            </x-tab>
        </x-tabs>
    </x-card>

    {{-- Claim Transaction Modal --}}
    <x-modal wire:model="claimModal" title="Claim Transaction to Customer">
        <x-form wire:submit="saveClaimCustomer">
            <x-choices-offline label="Customer *" wire:model="customer_id" :options="$customers" 
                option-label="name" single searchable />
            
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.claimModal = false" />
                <x-button label="Claim" type="submit" class="btn-primary" spinner="saveClaimCustomer" />
            </x-slot:actions>
        </x-form>
    </x-modal>

    <x-modal wire:model="modal" title="{{ $id ? 'Edit Bank Transaction' : 'New Bank Transaction' }}">
        <x-form wire:submit="save">
            <div class="grid grid-cols-2 gap-2">
                <x-input label="Statement Reference" wire:model="statementreference" />             
                <x-select label="Bank" wire:model.live="bank_id" :options="$banks" option-label="name" option-value="id" placeholder="Select Bank" />
                <x-select label="Account" wire:model="accountnumber" :options="$accounts" option-label="account_number" option-value="account_number" placeholder="Select Account" />
                <x-input label="Source Reference" wire:model="source_reference" />
                <x-input label="Transaction Date" wire:model="transaction_date" type="date" />
                <x-input label="Amount" wire:model="amount" />
            </div>
            <div class="grid  gap-2">
                <x-input label="Description" wire:model="description" />
            </div>
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.modal = false" />
                <x-button label="Save" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>
    {{-- Import Bank Statement Modal --}}
    <x-modal wire:model="importmodal" title="Import Bank Statement" box-class="max-w-5xl">
        @if($importStep === 1)
            {{-- Step 1: Upload File --}}
            <div x-data="{ uploading: false, fileReady: false }"
                 x-on:livewire-upload-start="uploading = true; fileReady = false"
                 x-on:livewire-upload-finish="uploading = false; fileReady = true"
                 x-on:livewire-upload-error="uploading = false; fileReady = false">
                
                <x-alert class="alert-info mb-4">
                    <x-slot:title>Upload your bank statement CSV or Excel file</x-slot:title>
                    Supported formats: CSV, XLS, XLSX (max 10MB)
                </x-alert>

                <x-form wire:submit="uploadStatement">
                    <div class="grid grid-cols-2 gap-4">
                        <x-select label="Bank Type *" wire:model="bankType" :options="$supportedBanks" 
                            option-label="name" option-value="id" />
                        <x-select label="Bank Account *" wire:model="bank_id" :options="$banks" 
                            option-label="name" option-value="id" placeholder="Select Bank" />
                    </div>
                    
                    <x-file wire:model="file" label="Bank Statement File *" 
                        accept=".csv,.xls,.xlsx" hint="CSV, XLS, or XLSX files only" />
                    
                    <p x-show="uploading" class="text-sm text-blue-600 mt-2 flex items-center gap-2">
                        <span class="loading loading-spinner loading-xs"></span> Uploading and parsing file...
                    </p>
                    <p x-show="fileReady && !uploading" class="text-sm text-green-600 font-medium mt-2">
                        ✓ File uploaded successfully
                    </p>

                    <x-slot:actions>
                        <x-button label="Cancel" @click="$wire.importmodal = false; $wire.resetImport()" />
                        <x-button label="Next: Preview" type="submit" class="btn-primary" 
                            spinner="uploadStatement" x-bind:disabled="!fileReady" />
                    </x-slot:actions>
                </x-form>
            </div>

        @elseif($importStep === 2)
            {{-- Step 2: Preview & Select Transactions --}}
            <div>
                <x-alert class="alert-success mb-4">
                    <x-slot:title>{{ count($previewData['transactions'] ?? []) }} transactions found</x-slot:title>
                    @if(!empty($previewData['metadata']))
                        Account: {{ $previewData['metadata']['account_number'] ?? 'N/A' }} | 
                        {{ $previewData['metadata']['account_name'] ?? '' }}
                    @endif
                </x-alert>

                <div class="flex justify-between items-center mb-3">
                    <div class="text-sm">
                        <strong>{{ count($selectedTransactions) }}</strong> of 
                        <strong>{{ count($previewData['transactions'] ?? []) }}</strong> selected
                    </div>
                    <div class="space-x-2">
                        <x-button label="Select All" wire:click="selectAll" class="btn-xs" />
                        <x-button label="Deselect All" wire:click="deselectAll" class="btn-xs" />
                    </div>
                </div>

                <div class="overflow-x-auto max-h-96">
                    <table class="table table-sm table-zebra">
                        <thead class="sticky top-0 bg-base-200">
                            <tr>
                                <th><input type="checkbox" class="checkbox checkbox-sm" 
                                    wire:click="selectAll" /></th>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Reference</th>
                                <th class="text-right">Debit</th>
                                <th class="text-right">Credit</th>
                                <th class="text-right">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(($previewData['transactions'] ?? []) as $index => $txn)
                                <tr wire:key="txn-{{ $index }}" 
                                    class="{{ in_array($index, $selectedTransactions) ? 'bg-success/10' : '' }}">
                                    <td>
                                        <input type="checkbox" class="checkbox checkbox-sm"
                                            wire:click="toggleTransaction({{ $index }})"
                                            @checked(in_array($index, $selectedTransactions)) />
                                    </td>
                                    <td class="text-xs">{{ $txn['transaction_date'] }}</td>
                                    <td class="text-xs">{{ \Str::limit($txn['description'], 40) }}</td>
                                    <td class="text-xs">{{ $txn['reference'] }}</td>
                                    <td class="text-right text-xs font-mono text-error">
                                        {{ $txn['debit'] > 0 ? number_format($txn['debit'], 2) : '' }}
                                    </td>
                                    <td class="text-right text-xs font-mono text-success">
                                        {{ $txn['credit'] > 0 ? number_format($txn['credit'], 2) : '' }}
                                    </td>
                                    <td class="text-right text-xs font-mono">
                                        {{ number_format($txn['balance'], 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <x-slot:actions>
                    <x-button label="Back" wire:click="resetImport" />
                    <x-button label="Import Selected" wire:click="executeImport" class="btn-primary" 
                        spinner="executeImport" />
                </x-slot:actions>
            </div>

        @elseif($importStep === 3)
            {{-- Step 3: Import Progress (handled by executeImport method) --}}
            <div class="text-center py-8">
                <span class="loading loading-spinner loading-lg"></span>
                <p class="mt-4">Importing transactions...</p>
            </div>
        @endif
    </x-modal>
</div>
