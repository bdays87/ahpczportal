<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />

    <x-card title="Suppliers" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            <x-input placeholder="Search suppliers…" wire:model.live="search" icon="o-magnifying-glass" />
            <x-button icon="o-plus" label="New Supplier" class="btn-primary" wire:click="$set('modal',true)" spinner />
        </x-slot:menu>

        <x-table :headers="$headers" :rows="$suppliers">
            @scope('actions', $supplier)
                <div class="flex gap-1">
                    <x-button icon="o-eye" class="btn-xs btn-info btn-outline"
                        wire:click="viewSupplier({{ $supplier->id }})" spinner />
                    <x-button icon="o-pencil" class="btn-xs btn-ghost btn-outline"
                        wire:click="edit({{ $supplier->id }})" spinner />
                    <x-button icon="o-trash" class="btn-xs btn-error btn-outline"
                        wire:click="delete({{ $supplier->id }})" wire:confirm="Delete this supplier?" spinner />
                </div>
            @endscope
            <x-slot:empty>
                <x-alert class="alert-info" title="No suppliers found." />
            </x-slot:empty>
        </x-table>
    </x-card>

    {{-- Create / Edit Modal --}}
    <x-modal wire:model="modal" title="{{ $id ? 'Edit Supplier' : 'New Supplier' }}" box-class="max-w-3xl">
        <x-form wire:submit="save">
            <div class="grid grid-cols-2 gap-3">
                <x-input label="Supplier Code" wire:model="code" placeholder="Auto-generated if empty" />
                <x-input label="Supplier Name *" wire:model="name" />
                <x-input label="Contact Person" wire:model="contact_person" />
                <x-input label="Email" wire:model="email" type="email" />
                <x-input label="Phone" wire:model="phone" />
                <x-input label="Tax Number" wire:model="tax_number" />
                <x-input label="Address" wire:model="address" class="col-span-2" />
                <x-input label="City" wire:model="city" />
                <x-input label="Country" wire:model="country" />
                <x-select label="Currency *" wire:model="currency_id" :options="$currencies" option-label="name" option-value="id" />
                <x-select label="Default AP Account" wire:model="ap_account_id" :options="$apAccounts" option-label="name" option-value="id" placeholder="(select)" />
                <x-select label="Default Tax Rate" wire:model="tax_rate_id" :options="$taxRates" option-label="name" option-value="id" placeholder="(none)" />
                <x-input label="Credit Limit" wire:model="credit_limit" type="number" step="0.01" />
                <x-input label="Payment Terms (Days)" wire:model="payment_terms" type="number" />
                <div class="col-span-2 divider">Banking Details</div>
                <x-input label="Bank Name" wire:model="bank_name" />
                <x-input label="Account Number" wire:model="bank_account_number" />
                <x-input label="Branch" wire:model="bank_branch" />
                <x-select label="Status" wire:model="status"
                    :options="[['id'=>'active','name'=>'Active'],['id'=>'inactive','name'=>'Inactive']]"
                    option-label="name" option-value="id" />
            </div>
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.modal=false" />
                <x-button label="Save" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>

    {{-- View Supplier Modal --}}
    <x-modal wire:model="viewModal" title="Supplier Details — {{ $selectedSupplier?->name }}" box-class="max-w-3xl">
        @if($selectedSupplier)
            <div class="grid grid-cols-2 gap-3">
                <div><strong>Code:</strong> {{ $selectedSupplier->code }}</div>
                <div><strong>Email:</strong> {{ $selectedSupplier->email }}</div>
                <div><strong>Phone:</strong> {{ $selectedSupplier->phone }}</div>
                <div><strong>Tax Number:</strong> {{ $selectedSupplier->tax_number }}</div>
                <div class="col-span-2"><strong>Address:</strong> {{ $selectedSupplier->address }}, {{ $selectedSupplier->city }}, {{ $selectedSupplier->country }}</div>
                <div><strong>Payment Terms:</strong> {{ $selectedSupplier->payment_terms }} days</div>
                <div><strong>Credit Limit:</strong> {{ number_format($selectedSupplier->credit_limit, 2) }} {{ $selectedSupplier->currency?->name }}</div>
            </div>
            <div class="divider">Recent Invoices</div>
            <table class="table table-xs">
                <thead><tr><th>Invoice #</th><th>Date</th><th>Amount</th><th>Balance</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($selectedSupplier->invoices->take(5) as $inv)
                        <tr>
                            <td>{{ $inv->invoice_number }}</td>
                            <td>{{ $inv->invoice_date->format('d M Y') }}</td>
                            <td>{{ number_format($inv->total_amount, 2) }}</td>
                            <td>{{ number_format($inv->balance_due, 2) }}</td>
                            <td><span class="badge badge-sm">{{ $inv->status }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-gray-400">No invoices</td></tr>
                    @endforelse
                </tbody>
            </table>
        @endif
        <x-slot:actions>
            <x-button label="Close" @click="$wire.viewModal=false" />
        </x-slot:actions>
    </x-modal>
</div>
