<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-5">
        {{-- Customers Stats --}}
        <x-card title="Customers Sync" class="border-2 border-primary">
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span>Pending:</span>
                    <x-badge value="{{ $stats['customers']['pending'] }}" class="badge-warning" />
                </div>
                <div class="flex justify-between">
                    <span>Synced:</span>
                    <x-badge value="{{ $stats['customers']['synced'] }}" class="badge-success" />
                </div>
                <div class="flex justify-between">
                    <span>Failed:</span>
                    <x-badge value="{{ $stats['customers']['failed'] }}" class="badge-error" />
                </div>
            </div>
        </x-card>

        {{-- Invoices Stats --}}
        <x-card title="Invoices Sync" class="border-2 border-info">
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span>Pending:</span>
                    <x-badge value="{{ $stats['invoices']['pending'] }}" class="badge-warning" />
                </div>
                <div class="flex justify-between">
                    <span>Synced:</span>
                    <x-badge value="{{ $stats['invoices']['synced'] }}" class="badge-success" />
                </div>
                <div class="flex justify-between">
                    <span>Failed:</span>
                    <x-badge value="{{ $stats['invoices']['failed'] }}" class="badge-error" />
                </div>
            </div>
        </x-card>

        {{-- Statements Stats --}}
        <x-card title="Sage Statements" class="border-2 border-accent">
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span>Total Balance:</span>
                    <span class="font-bold">USD {{ number_format($stats['statements']['total_balance'], 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>With Balance:</span>
                    <x-badge value="{{ $stats['statements']['customers_with_balance'] }}" class="badge-info" />
                </div>
            </div>
        </x-card>
    </div>

    {{-- Tabs and Filters --}}
    <x-card class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            <x-input type="text" placeholder="Search" wire:model.live="search" icon="o-magnifying-glass" />
            
            <x-select wire:model.live="filterStatus" :options="[
                ['value' => 'all', 'label' => 'All Status'],
                ['value' => 'pending', 'label' => 'Pending'],
                ['value' => 'synced', 'label' => 'Synced'],
                ['value' => 'failed', 'label' => 'Failed'],
            ]" option-value="value" option-label="label" />
        </x-slot:menu>

        {{-- Tabs --}}
        <div class="flex justify-between items-center mb-4">
            <div class="tabs tabs-boxed">
                <a class="tab {{ $activeTab === 'customers' ? 'tab-active' : '' }}" 
                   wire:click="setTab('customers')">
                    Customers
                </a>
                <a class="tab {{ $activeTab === 'invoices' ? 'tab-active' : '' }}" 
                   wire:click="setTab('invoices')">
                    Invoices
                </a>
            </div>

            {{-- Bulk Action Buttons --}}
            <div class="flex gap-2">
                @if($activeTab === 'customers')
                    <x-button icon="o-arrow-up-tray" 
                             label="Push All Approved" 
                             class="btn-sm btn-primary" 
                             wire:click="pushAllCustomers" 
                             wire:confirm="Queue all approved customers with approved applications for sync?"
                             spinner />
                    
                    @if($filterStatus === 'failed')
                        <x-button icon="o-arrow-path" 
                                 label="Retry All Failed" 
                                 class="btn-sm btn-warning" 
                                 wire:click="retryAllFailed" 
                                 wire:confirm="Retry all failed customer syncs?"
                                 spinner />
                    @endif

                    @if(count($selectedCustomers) > 0)
                        <x-button icon="o-arrow-up-tray" 
                                 label="Push Selected ({{ count($selectedCustomers) }})" 
                                 class="btn-sm btn-accent" 
                                 wire:click="pushSelectedCustomers" 
                                 spinner />
                    @endif
                @else
                    <x-button icon="o-arrow-up-tray" 
                             label="Push All Paid" 
                             class="btn-sm btn-primary" 
                             wire:click="pushAllInvoices" 
                             wire:confirm="Queue all paid invoices for sync?"
                             spinner />
                    
                    @if($filterStatus === 'failed')
                        <x-button icon="o-arrow-path" 
                                 label="Retry All Failed" 
                                 class="btn-sm btn-warning" 
                                 wire:click="retryAllFailed" 
                                 wire:confirm="Retry all failed invoice syncs?"
                                 spinner />
                    @endif

                    @if(count($selectedInvoices) > 0)
                        <x-button icon="o-arrow-up-tray" 
                                 label="Push Selected ({{ count($selectedInvoices) }})" 
                                 class="btn-sm btn-accent" 
                                 wire:click="pushSelectedInvoices" 
                                 spinner />
                    @endif
                @endif
            </div>
        </div>

        {{-- Customers Table --}}
        @if($activeTab === 'customers')
        <table class="table table-zebra">
            <thead>
                <tr>
                    <th>
                        <input type="checkbox" wire:model.live="selectAll" class="checkbox checkbox-sm" />
                    </th>
                    <th>Customer</th>
                    <th>Email</th>
                    <th>Profession</th>
                    <th>Sage Code</th>
                    <th>Status</th>
                    <th>Synced At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $profession)
                <tr>
                    <td>
                        <input type="checkbox" 
                               wire:model.live="selectedCustomers" 
                               value="{{ $profession->id }}" 
                               class="checkbox checkbox-sm" />
                    </td>
                    <td>{{ $profession->customer->name }} {{ $profession->customer->surname }}</td>
                    <td>{{ $profession->customer->email }}</td>
                    <td>{{ $profession->profession->name ?? 'N/A' }}</td>
                    <td>
                        @if($profession->sage_customer_code)
                            <span class="font-mono text-sm">{{ $profession->sage_customer_code }}</span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $statusClass = match($profession->sage_sync_status) {
                                'SYNCED' => 'badge-success',
                                'PENDING', 'SYNCING' => 'badge-warning',
                                'FAILED' => 'badge-error',
                                default => 'badge-ghost'
                            };
                        @endphp
                        <x-badge :value="$profession->sage_sync_status ?? 'PENDING'" :class="$statusClass" />
                    </td>
                    <td>
                        @if($profession->sage_synced_at)
                            {{ $profession->sage_synced_at->format('Y-m-d H:i') }}
                        @else
                            <span class="text-gray-400">Not synced</span>
                        @endif
                    </td>
                    <td>
                        @if($profession->sage_sync_status === 'FAILED')
                            <x-button icon="o-arrow-path" 
                                     label="Retry" 
                                     class="btn-xs btn-warning" 
                                     wire:click="retrySyncCustomer({{ $profession->id }})" 
                                     spinner />
                            
                            @if($profession->sage_sync_error)
                                <x-button icon="o-exclamation-triangle" 
                                         label="Error" 
                                         class="btn-xs btn-error btn-outline" 
                                         onclick="alert('{{ addslashes($profession->sage_sync_error) }}')" />
                            @endif
                        @else
                            <x-button icon="o-arrow-up-tray" 
                                     label="Push" 
                                     class="btn-xs btn-ghost" 
                                     wire:click="retrySyncCustomer({{ $profession->id }})" 
                                     spinner />
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-gray-500 p-5">
                        No customers found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $customers->links() }}
        </div>
        @endif

        {{-- Invoices Table --}}
        @if($activeTab === 'invoices')
        <table class="table table-zebra">
            <thead>
                <tr>
                    <th>
                        <input type="checkbox" wire:model.live="selectAll" class="checkbox checkbox-sm" />
                    </th>
                    <th>Invoice #</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Sage Invoice #</th>
                    <th>Outstanding</th>
                    <th>Status</th>
                    <th>Synced At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                <tr>
                    <td>
                        <input type="checkbox" 
                               wire:model.live="selectedInvoices" 
                               value="{{ $invoice->id }}" 
                               class="checkbox checkbox-sm" />
                    </td>
                    <td class="font-mono text-sm">{{ $invoice->invoice_number }}</td>
                    <td>{{ $invoice->customer->name ?? 'N/A' }} {{ $invoice->customer->surname ?? '' }}</td>
                    <td>{{ $invoice->currency->name ?? 'USD' }} {{ number_format($invoice->amount, 2) }}</td>
                    <td>
                        @if($invoice->sage_invoice_number)
                            <span class="font-mono text-sm">{{ $invoice->sage_invoice_number }}</span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td>
                        @if($invoice->sage_outstanding !== null)
                            USD {{ number_format($invoice->sage_outstanding, 2) }}
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $statusClass = match($invoice->sage_sync_status) {
                                'SYNCED' => 'badge-success',
                                'PENDING', 'SYNCING' => 'badge-warning',
                                'FAILED' => 'badge-error',
                                default => 'badge-ghost'
                            };
                        @endphp
                        <x-badge :value="$invoice->sage_sync_status ?? 'PENDING'" :class="$statusClass" />
                    </td>
                    <td>
                        @if($invoice->sage_synced_at)
                            {{ $invoice->sage_synced_at->format('Y-m-d H:i') }}
                        @else
                            <span class="text-gray-400">Not synced</span>
                        @endif
                    </td>
                    <td>
                        @if($invoice->sage_sync_status === 'FAILED')
                            <x-button icon="o-arrow-path" 
                                     label="Retry" 
                                     class="btn-xs btn-warning" 
                                     wire:click="retrySyncInvoice({{ $invoice->id }})" 
                                     spinner />
                            
                            @if($invoice->sage_sync_error)
                                <x-button icon="o-exclamation-triangle" 
                                         label="Error" 
                                         class="btn-xs btn-error btn-outline" 
                                         onclick="alert('{{ addslashes($invoice->sage_sync_error) }}')" />
                            @endif
                        @else
                            <x-button icon="o-arrow-up-tray" 
                                     label="Push" 
                                     class="btn-xs btn-ghost" 
                                     wire:click="retrySyncInvoice({{ $invoice->id }})" 
                                     spinner />
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-gray-500 p-5">
                        No invoices found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $invoices->links() }}
        </div>
        @endif
    </x-card>
</div>
