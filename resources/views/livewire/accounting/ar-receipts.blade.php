<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />

    <x-card title="Accounts Receivable Receipts" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            <x-input placeholder="Search…" wire:model.live="search" icon="o-magnifying-glass" />
            <x-button icon="o-plus" label="New Receipt" class="btn-primary" wire:click="$set('modal',true)" spinner />
        </x-slot:menu>

        <x-table :headers="$headers" :rows="$receipts" with-pagination>
            @scope('cell_payment_method', $receipt)
                <span class="badge badge-outline">{{ strtoupper($receipt->payment_method) }}</span>
            @endscope

            @scope('actions', $receipt)
                <div class="flex gap-1">
                    <x-button icon="o-eye" class="btn-xs btn-info btn-outline"
                        wire:click="viewReceipt({{ $receipt->id }})" spinner />
                    @if($receipt->status === 'DRAFT')
                        <x-button icon="o-pencil" class="btn-xs btn-ghost btn-outline"
                            wire:click="edit({{ $receipt->id }})" spinner />
                        <x-button icon="o-trash" class="btn-xs btn-error btn-outline"
                            wire:click="delete({{ $receipt->id }})" wire:confirm="Delete?" spinner />
                    @endif
                </div>
            @endscope

            <x-slot:empty>
                <x-alert class="alert-info" title="No receipts found." />
            </x-slot:empty>
        </x-table>
    </x-card>

    {{-- Create / Edit Modal --}}
    <x-modal wire:model="modal" title="{{ $id ? 'Edit Receipt' : 'New AR Receipt' }}" box-class="max-w-4xl">
        <x-form wire:submit="save">
            <div class="grid grid-cols-2 gap-3 mb-4">
                <x-choices-offline label="Customer *" wire:model.live="customer_id" :options="$customers" 
                    option-label="display_name" single searchable />
                <x-input label="Receipt Date *" wire:model="receipt_date" type="date" />
                <x-choices-offline label="Payment Method *" wire:model="payment_method"
                    :options="[
                        ['id'=>'cash','name'=>'Cash'],
                        ['id'=>'check','name'=>'Check'],
                        ['id'=>'bank_transfer','name'=>'Bank Transfer'],
                        ['id'=>'mobile_money','name'=>'Mobile Money'],
                        ['id'=>'credit_card','name'=>'Credit Card'],
                    ]" option-label="name" single searchable />
                <x-input label="Reference Number" wire:model="reference_number" />
                <x-choices-offline label="Bank Account" wire:model="bank_account_id" :options="$bankAccounts" 
                    option-label="name" single searchable placeholder="(cash receipt)" />
                <x-input label="Amount *" wire:model="amount" type="number" step="0.01" />
                <x-textarea label="Notes" wire:model="notes" rows="2" class="col-span-2" />
            </div>

            <div class="mb-3 font-semibold text-lg">Allocate to Invoices</div>
            @if($customer_id && $unpaidInvoices)
                <div class="overflow-x-auto">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Date</th>
                                <th class="text-right">Total</th>
                                <th class="text-right">Balance Due</th>
                                <th class="text-right">Allocate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($unpaidInvoices as $index => $inv)
                                <tr wire:key="invoice-{{ $inv['id'] }}">
                                    <td>{{ $inv['invoice_number'] }}</td>
                                    <td>{{ $inv['invoice_date'] }}</td>
                                    <td class="text-right font-mono">{{ number_format($inv['total_amount'], 2) }}</td>
                                    <td class="text-right font-mono">{{ number_format($inv['balance_due'], 2) }}</td>
                                    <td class="text-right">
                                        <input wire:model="allocations.{{ $inv['id'] }}" 
                                            type="number" step="0.01" max="{{ $inv['balance_due'] }}"
                                            class="input input-sm input-bordered w-32 text-right" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-right font-semibold">Total Allocated:</td>
                                <td class="text-right font-mono font-semibold">{{ number_format($totalAllocated, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <x-alert class="alert-info" title="Select a customer to view unpaid invoices" />
            @endif

            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.modal=false" />
                <x-button label="Save" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>

    {{-- View Receipt Modal --}}
    <x-modal wire:model="viewModal" title="AR Receipt — {{ $selectedReceipt?->receipt_number }}" box-class="max-w-3xl">
        @if($selectedReceipt)
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div><strong>Customer:</strong> {{ $selectedReceipt->customer?->full_name }}</div>
                <div><strong>Receipt Date:</strong> {{ $selectedReceipt->receipt_date->format('d M Y') }}</div>
                <div><strong>Method:</strong> {{ strtoupper($selectedReceipt->payment_method) }}</div>
                <div><strong>Reference:</strong> {{ $selectedReceipt->reference_number }}</div>
                <div><strong>Amount:</strong> {{ number_format($selectedReceipt->amount, 2) }}</div>
                <div><strong>Status:</strong> <span class="badge badge-success">{{ $selectedReceipt->status }}</span></div>
            </div>
            <div class="divider">Invoice Allocations</div>
            <table class="table table-sm">
                <thead><tr><th>Invoice #</th><th>Date</th><th class="text-right">Allocated</th></tr></thead>
                <tbody>
                    @foreach($selectedReceipt->allocations as $alloc)
                        <tr>
                            <td>{{ $alloc->invoice?->invoice_number }}</td>
                            <td>{{ $alloc->invoice?->invoice_date->format('d M Y') }}</td>
                            <td class="text-right font-mono">{{ number_format($alloc->allocated_amount, 2) }}</td>
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
