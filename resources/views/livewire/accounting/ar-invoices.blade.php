<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />

    <x-card title="Accounts Receivable Invoices" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            <x-input placeholder="Search…" wire:model.live="search" icon="o-magnifying-glass" />
            <x-select wire:model.live="filterStatus" placeholder="All Status"
                :options="[
                    ['id'=>'DRAFT','name'=>'Draft'],
                    ['id'=>'POSTED','name'=>'Posted'],
                    ['id'=>'PARTIALLY_PAID','name'=>'Partially Paid'],
                    ['id'=>'PAID','name'=>'Paid'],
                    ['id'=>'OVERDUE','name'=>'Overdue'],
                ]" option-label="name" option-value="id" />
            <x-button icon="o-plus" label="New Invoice" class="btn-primary" wire:click="$set('modal',true)" spinner />
        </x-slot:menu>

        <x-table :headers="$headers" :rows="$invoices" with-pagination>
            @scope('cell_status', $inv)
                @php
                    $cls = match($inv->status) {
                        'DRAFT'          => 'badge-ghost',
                        'POSTED'         => 'badge-info',
                        'PARTIALLY_PAID' => 'badge-warning',
                        'PAID'           => 'badge-success',
                        'OVERDUE'        => 'badge-error',
                        default          => 'badge-ghost',
                    };
                @endphp
                <span class="badge {{ $cls }}">{{ $inv->status }}</span>
            @endscope

            @scope('actions', $inv)
                <div class="flex gap-1">
                    <x-button icon="o-eye" class="btn-xs btn-info btn-outline"
                        wire:click="viewInvoice({{ $inv->id }})" spinner />
                    @if($inv->status === 'DRAFT')
                        <x-button icon="o-pencil" class="btn-xs btn-ghost btn-outline"
                            wire:click="edit({{ $inv->id }})" spinner />
                        <x-button icon="o-check" class="btn-xs btn-success btn-outline" tooltip="Post"
                            wire:click="post({{ $inv->id }})" wire:confirm="Post this invoice?" spinner />
                        <x-button icon="o-x-mark" class="btn-xs btn-warning btn-outline" tooltip="Cancel"
                            wire:click="cancel({{ $inv->id }})" wire:confirm="Cancel this invoice?" spinner />
                        <x-button icon="o-trash" class="btn-xs btn-error btn-outline"
                            wire:click="delete({{ $inv->id }})" wire:confirm="Delete?" spinner />
                    @endif
                </div>
            @endscope

            <x-slot:empty>
                <x-alert class="alert-info" title="No invoices found." />
            </x-slot:empty>
        </x-table>
    </x-card>

    {{-- Ageing Summary --}}
    <div class="grid grid-cols-5 gap-3 mt-5">
        <x-stat title="Current" value="{{ number_format($ageingData['current'],2) }}" class="bg-base-200" />
        <x-stat title="1-30 Days" value="{{ number_format($ageingData['1_30'],2) }}" class="bg-base-200" />
        <x-stat title="31-60 Days" value="{{ number_format($ageingData['31_60'],2) }}" class="bg-base-200" />
        <x-stat title="61-90 Days" value="{{ number_format($ageingData['61_90'],2) }}" class="bg-base-200" />
        <x-stat title="Over 90 Days" value="{{ number_format($ageingData['over_90'],2) }}" class="bg-base-200" />
    </div>

    {{-- Create / Edit Modal --}}
    <x-modal wire:model="modal" title="{{ $id ? 'Edit Invoice' : 'New AR Invoice' }}" box-class="max-w-3xl">
        <x-form wire:submit="save">
            <div class="grid grid-cols-2 gap-3">
                <x-choices-offline label="Customer *" wire:model="customer_id" :options="$customers" 
                    option-label="display_name" single searchable />
                <x-input label="Customer Reference" wire:model="customer_reference" />
                <x-input label="Invoice Date *" wire:model="invoice_date" type="date" />
                <x-input label="Due Date *" wire:model="due_date" type="date" />
                <x-choices-offline label="Currency *" wire:model="currency_id" :options="$currencies" 
                    option-label="name" single searchable />
                <x-input label="Exchange Rate" wire:model="exchange_rate" type="number" step="0.000001" />
                <x-input label="Description" wire:model="description" class="col-span-2" />
                <x-choices-offline label="Revenue Account *" wire:model="revenue_account_id" :options="$revenueAccounts" 
                    option-label="name" single searchable />
                <x-choices-offline label="AR Account *" wire:model="ar_account_id" :options="$arAccounts" 
                    option-label="name" single searchable />
                <x-choices-offline label="Cost Center" wire:model="cost_center_id" :options="$costCenters" 
                    option-label="name" single searchable placeholder="(none)" />
                <x-choices-offline label="Tax Rate" wire:model.live="tax_rate_id" :options="$taxRates" 
                    option-label="name" single searchable placeholder="(none)" />
                <x-input label="Subtotal *" wire:model.live="subtotal" type="number" step="0.01" />
                <x-input label="Tax Amount" wire:model.live="tax_amount" type="number" step="0.01" />
                <x-input label="Discount" wire:model.live="discount_amount" type="number" step="0.01" />
                <div class="font-semibold text-lg">Total: {{ number_format($total_amount, 2) }}</div>
                <x-textarea label="Notes" wire:model="notes" rows="2" class="col-span-2" />
            </div>
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.modal=false" />
                <x-button label="Save" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>

    {{-- View Invoice Modal --}}
    <x-modal wire:model="viewModal" title="AR Invoice — {{ $selectedInvoice?->invoice_number }}" box-class="max-w-2xl">
        @if($selectedInvoice)
            <div class="grid grid-cols-2 gap-3">
                <div><strong>Customer:</strong> {{ $selectedInvoice->customer?->full_name }}</div>
                <div><strong>Customer Ref:</strong> {{ $selectedInvoice->customer_reference }}</div>
                <div><strong>Invoice Date:</strong> {{ $selectedInvoice->invoice_date->format('d M Y') }}</div>
                <div><strong>Due Date:</strong> {{ $selectedInvoice->due_date->format('d M Y') }}</div>
                <div><strong>Subtotal:</strong> {{ number_format($selectedInvoice->subtotal, 2) }}</div>
                <div><strong>Tax:</strong> {{ number_format($selectedInvoice->tax_amount, 2) }}</div>
                <div><strong>Total:</strong> {{ number_format($selectedInvoice->total_amount, 2) }}</div>
                <div><strong>Balance:</strong> {{ number_format($selectedInvoice->balance_due, 2) }}</div>
                <div class="col-span-2"><strong>Status:</strong> <span class="badge badge-success">{{ $selectedInvoice->status }}</span></div>
            </div>
        @endif
        <x-slot:actions>
            <x-button label="Close" @click="$wire.viewModal=false" />
        </x-slot:actions>
    </x-modal>
</div>
