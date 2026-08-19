<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />
    @can('invoices.access')
    <x-card separator class="mt-5 border-2 border-gray-200">
        <x-slot:title>
            <div class="flex items-center gap-2">
                <span>Paid Invoices</span>
                <span class="badge badge-primary badge-outline font-semibold">{{ $invoices->total() }}</span>
            </div>
        </x-slot:title>
        <x-slot:menu>
            <x-input placeholder="Search customer, invoice or receipt no..." icon="o-magnifying-glass" wire:model.live.debounce.400ms="search" clearable />
            <x-select wire:model.live="year" :options="$years" option-label="name" option-value="id" placeholder="All Years" icon="o-calendar" class="w-36" />
            <x-select wire:model.live="currency_id" :options="$currencies" option-label="name" option-value="id" placeholder="All Currencies" icon="o-currency-dollar" class="w-40" />
            @can('invoices.receipt')
            <x-button label="Check for Duplicate Receipts" icon="o-shield-exclamation" class="btn-outline btn-warning" wire:click="scanduplicates" spinner="scanduplicates" />
            @endcan
        </x-slot:menu>

        <x-table :headers="$headers" :rows="$invoices" with-pagination striped class="[&_th]:!bg-base-200/70 [&_th]:!font-semibold [&_td]:align-top
            [&_thead_th:last-child]:sticky [&_thead_th:last-child]:right-0 [&_thead_th:last-child]:z-20 [&_thead_th:last-child]:!bg-base-200
            [&_tbody_td:last-child]:sticky [&_tbody_td:last-child]:right-0 [&_tbody_td:last-child]:z-10 [&_tbody_td:last-child]:!bg-base-100
            [&_tbody_td:last-child]:shadow-[-4px_0_6px_-2px_rgba(0,0,0,0.08)]">
            @scope('cell_created_at', $invoice)
            {{ $invoice->created_at->format('d M Y') }}
            @endscope
            @scope('cell_invoice_number', $invoice)
            <span class="font-mono text-xs text-gray-600 break-all">{{ $invoice->invoice_number }}</span>
            @endscope
            @scope('cell_customer', $invoice)
            <div class="font-semibold">{{ $invoice->customer->name }} {{ $invoice->customer->surname }}</div>
            <div class="text-xs text-gray-500">{{ $invoice->customer->regnumber }}</div>
            @endscope
            @scope('cell_description', $invoice)
            {{ $invoice->description ?? '—' }}
            @endscope
            @scope('cell_amount', $invoice)
            <span class="font-semibold whitespace-nowrap">{{ $invoice->currency->name }} {{ number_format($invoice->amount, 2) }}</span>
            @endscope
            @scope('cell_receipt_number', $invoice)
            @if($invoice->receipts->isNotEmpty())
            <div class="flex flex-col gap-0.5">
                @foreach($invoice->receipts as $receipt)
                <span class="font-mono text-xs text-success break-all">{{ $receipt->receipt_number }}</span>
                @endforeach
            </div>
            @else
            <span class="text-gray-400">—</span>
            @endif
            @endscope
            @scope('actions', $invoice)
            <div class="flex items-center justify-end gap-2">
                <x-button icon="o-eye" class="btn-sm btn-info btn-outline" tooltip="View details"
                    wire:click="view({{ $invoice->id }})" spinner />
                <x-button icon="o-printer" class="btn-sm btn-outline" tooltip="Print receipt"
                    link="{{ route('invoices.receipt.print', $invoice->uuid) }}" external />
            </div>
            @endscope
            <x-slot:empty>
                <div class="flex flex-col items-center justify-center py-10 gap-2 text-gray-400">
                    <x-icon name="o-banknotes" class="w-10 h-10" />
                    <span>No paid invoices found{{ $search ? " for \"{$search}\"" : '' }}{{ $year ? " in {$year}" : '' }}.</span>
                </div>
            </x-slot:empty>
        </x-table>
    </x-card>

    @if($totals->isNotEmpty())
    <x-card title="Totals" subtitle="For the invoices currently listed above" separator class="mt-5 border-2 border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($totals as $total)
            <div class="stats shadow">
                <div class="stat">
                    <div class="stat-figure text-primary">
                        <x-icon name="o-banknotes" class="w-8 h-8" />
                    </div>
                    <div class="stat-title">{{ $total->currency->name ?? 'Unknown' }} Total Paid</div>
                    <div class="stat-value text-primary">{{ number_format($total->total_amount, 2) }}</div>
                    <div class="stat-desc">{{ $total->invoice_count }} invoice{{ $total->invoice_count == 1 ? '' : 's' }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </x-card>
    @endif
    @else
    <x-alert class="alert-error mt-3" title="You do not have permission to view invoices." />
    @endcan

    <x-modal title="Paid Invoice" wire:model="modal" box-class="max-w-3xl">
        @if($invoice)
        <table class="table table-compact">
            <tr>
                <td class="font-semibold">Date</td>
                <td>{{ $invoice->created_at->format('d M Y H:i') }}</td>
            </tr>
            <tr>
                <td class="font-semibold">Customer</td>
                <td>{{ $invoice->customer->name }} {{ $invoice->customer->surname }} ({{ $invoice->customer->regnumber }})</td>
            </tr>
            <tr>
                <td class="font-semibold">Invoice Number</td>
                <td>{{ $invoice->invoice_number }}</td>
            </tr>
            <tr>
                <td class="font-semibold">Description</td>
                <td class="whitespace-normal break-words">{{ $invoice->description ?? '—' }}</td>
            </tr>
            <tr>
                <td class="font-semibold">Amount</td>
                <td>{{ $invoice->currency->name }} {{ number_format($invoice->amount, 2) }}</td>
            </tr>
            <tr>
                <td class="font-semibold">Status</td>
                <td><x-badge value="{{ $invoice->status }}" class="badge-success badge-sm" /></td>
            </tr>
        </table>

        <x-card title="Receipts" separator class="mt-4">
            @php($totalpaid = $invoice->receipts->sum('amount'))
            @if($totalpaid > $invoice->amount)
            <x-alert icon="o-exclamation-triangle" class="alert-warning mb-3"
                title="Total receipted ({{ $invoice->currency->name }} {{ number_format($totalpaid, 2) }}) is more than the invoice amount ({{ $invoice->currency->name }} {{ number_format($invoice->amount, 2) }}) — likely a duplicate receipt." />
            @endif
            <table class="table table-compact w-full">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Receipt No</th>
                        <th>Amount</th>
                        @can('invoices.receipt')
                        <th></th>
                        @endcan
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoice->receipts as $receipt)
                    <tr>
                        <td>{{ $receipt->created_at->format('d M Y') }}</td>
                        <td class="font-mono">{{ $receipt->receipt_number }}</td>
                        <td>{{ $receipt->currency->name ?? $invoice->currency->name }} {{ number_format($receipt->amount, 2) }}</td>
                        @can('invoices.receipt')
                        <td class="text-right">
                            <x-button icon="o-trash" class="btn-xs btn-outline btn-error" tooltip="Delete this receipt"
                                wire:click="deletereceipt({{ $receipt->id }})"
                                wire:confirm="Delete receipt {{ $receipt->receipt_number }}? This can only be undone by re-capturing the payment. Only do this for a genuine mistake/duplicate." spinner />
                        </td>
                        @endcan
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-gray-400">No receipts recorded</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </x-card>

        <x-slot:actions>
            <x-button label="Print Receipt" icon="o-printer" class="btn-primary"
                link="{{ route('invoices.receipt.print', $invoice->uuid) }}" external />
        </x-slot:actions>
        @endif
    </x-modal>

    <x-modal title="Duplicate Receipts Found" wire:model="duplicatesmodal" box-class="max-w-4xl">
        <x-alert icon="o-information-circle" class="alert-info mb-3"
            title="These receipts share the exact same receipt number — a sign the same payment was recorded more than once. The oldest of each group is kept; the rest are marked for removal." />

        @foreach($duplicategroups as $group)
        <x-card class="mb-3" separator>
            <div class="flex items-center justify-between mb-2">
                <div>
                    <div class="font-semibold">{{ $group['customer'] }} — Invoice {{ $group['invoice_number'] }}</div>
                    <div class="text-xs text-gray-500">Receipt No {{ $group['receipt_number'] }} · Invoice amount {{ $group['currency'] }} {{ number_format($group['invoice_amount'], 2) }}</div>
                </div>
            </div>
            <table class="table table-compact w-full">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th></th>
                        @can('invoices.receipt')
                        <th></th>
                        @endcan
                    </tr>
                </thead>
                <tbody>
                    @foreach($group['receipts'] as $receipt)
                    <tr>
                        <td>{{ $receipt['created_at'] }}</td>
                        <td>{{ $group['currency'] }} {{ number_format($receipt['amount'], 2) }}</td>
                        <td>
                            @if($receipt['keep'])
                            <x-badge value="Keep" class="badge-success badge-sm" />
                            @else
                            <x-badge value="Duplicate" class="badge-error badge-sm" />
                            @endif
                        </td>
                        @can('invoices.receipt')
                        <td class="text-right">
                            @if(! $receipt['keep'])
                            <x-button icon="o-trash" class="btn-xs btn-outline btn-error" tooltip="Delete this one"
                                wire:click="deletereceipt({{ $receipt['id'] }})"
                                wire:confirm="Delete this duplicate receipt?" spinner />
                            @endif
                        </td>
                        @endcan
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </x-card>
        @endforeach

        <x-slot:actions>
            <x-button label="Close" @click="$wire.duplicatesmodal = false" />
            @can('invoices.receipt')
            <x-button label="Remove All Duplicates" icon="o-trash" class="btn-error"
                wire:click="removeduplicates"
                wire:confirm="This will delete every receipt marked \"Duplicate\" above, keeping only the earliest of each group. Continue?" spinner="removeduplicates" />
            @endcan
        </x-slot:actions>
    </x-modal>
</div>
