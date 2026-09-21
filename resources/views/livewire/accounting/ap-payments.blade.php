<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />

    <x-card title="Accounts Payable Payments" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            <x-input placeholder="Search…" wire:model.live="search" icon="o-magnifying-glass" />
            <x-button icon="o-plus" label="New Payment" class="btn-primary" wire:click="$set('modal',true)" spinner />
        </x-slot:menu>

        <x-table :headers="$headers" :rows="$payments" with-pagination>
            @scope('cell_payment_method', $payment)
                <span class="badge badge-outline">{{ strtoupper($payment->payment_method) }}</span>
            @endscope

            @scope('actions', $payment)
                <div class="flex gap-1">
                    <x-button icon="o-eye" class="btn-xs btn-info btn-outline"
                        wire:click="viewPayment({{ $payment->id }})" spinner />
                    @if($payment->status === 'DRAFT')
                        <x-button icon="o-pencil" class="btn-xs btn-ghost btn-outline"
                            wire:click="edit({{ $payment->id }})" spinner />
                        <x-button icon="o-trash" class="btn-xs btn-error btn-outline"
                            wire:click="delete({{ $payment->id }})" wire:confirm="Delete?" spinner />
                    @endif
                </div>
            @endscope

            <x-slot:empty>
                <x-alert class="alert-info" title="No payments found." />
            </x-slot:empty>
        </x-table>
    </x-card>

    {{-- Create / Edit Modal --}}
    <x-modal wire:model="modal" title="{{ $id ? 'Edit Payment' : 'New AP Payment' }}" box-class="max-w-4xl">
        <x-form wire:submit="save">
            <div class="grid grid-cols-2 gap-3 mb-4">
                <x-choices-offline label="Supplier *" wire:model.live="supplier_id" :options="$suppliers" 
                    option-label="name" single searchable />
                <x-input label="Payment Date *" wire:model="payment_date" type="date" />
                <x-choices-offline label="Payment Method *" wire:model="payment_method"
                    :options="[
                        ['id'=>'cash','name'=>'Cash'],
                        ['id'=>'check','name'=>'Check'],
                        ['id'=>'bank_transfer','name'=>'Bank Transfer'],
                        ['id'=>'mobile_money','name'=>'Mobile Money'],
                    ]" option-label="name" single searchable />
                <x-input label="Reference Number" wire:model="reference_number" />
                <x-choices-offline label="Bank Account" wire:model="bank_account_id" :options="$bankAccounts" 
                    option-label="name" single searchable placeholder="(cash payment)" />
                <x-input label="Amount *" wire:model="amount" type="number" step="0.01" />
                <x-textarea label="Notes" wire:model="notes" rows="2" class="col-span-2" />
            </div>

            <div class="mb-3 font-semibold text-lg">Allocate to Invoices</div>
            @if($supplier_id && $unpaidInvoices)
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
                <x-alert class="alert-info" title="Select a supplier to view unpaid invoices" />
            @endif

            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.modal=false" />
                <x-button label="Save" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>

    {{-- View Payment Modal --}}
    <x-modal wire:model="viewModal" title="AP Payment — {{ $selectedPayment?->payment_number }}" box-class="max-w-3xl">
        @if($selectedPayment)
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div><strong>Supplier:</strong> {{ $selectedPayment->supplier?->name }}</div>
                <div><strong>Payment Date:</strong> {{ $selectedPayment->payment_date->format('d M Y') }}</div>
                <div><strong>Method:</strong> {{ strtoupper($selectedPayment->payment_method) }}</div>
                <div><strong>Reference:</strong> {{ $selectedPayment->reference_number }}</div>
                <div><strong>Amount:</strong> {{ number_format($selectedPayment->amount, 2) }}</div>
                <div><strong>Status:</strong> <span class="badge badge-success">{{ $selectedPayment->status }}</span></div>
            </div>
            <div class="divider">Invoice Allocations</div>
            <table class="table table-sm">
                <thead><tr><th>Invoice #</th><th>Date</th><th class="text-right">Allocated</th></tr></thead>
                <tbody>
                    @foreach($selectedPayment->allocations as $alloc)
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
