<div class="max-w-3xl mx-auto my-8 px-4">

    <div class="no-print flex justify-end gap-2 mb-4">
        <x-button label="Print" icon="o-printer" class="btn-primary" onclick="window.print()" />
        <x-button label="Close" icon="o-x-mark" class="btn-ghost" onclick="window.close()" />
    </div>

    <div class="bg-white shadow rounded-box border border-gray-200 p-8 print:shadow-none print:border-0">
        {{-- Header --}}
        <div class="flex items-center justify-between border-b-2 border-gray-800 pb-4 mb-6">
            <div class="flex items-center gap-3">
                <img src="{{ asset(config('app.logo')) }}" alt="Logo" class="h-14 w-auto">
                <div>
                    <div class="font-bold text-lg">{{ config('app.name') }}</div>
                    <div class="text-xs text-gray-500">{{ config('app.title') }}</div>
                </div>
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold uppercase tracking-wide">Receipt</div>
                <div class="text-sm text-gray-500">Invoice {{ $invoice->invoice_number }}</div>
            </div>
        </div>

        {{-- Customer & invoice summary --}}
        <div class="grid grid-cols-2 gap-6 mb-6 text-sm">
            <div>
                <div class="text-xs uppercase text-gray-400 font-semibold mb-1">Billed To</div>
                <div class="font-semibold">{{ $invoice->customer->name }} {{ $invoice->customer->surname }}</div>
                <div>Reg No: {{ $invoice->customer->regnumber ?? '—' }}</div>
                <div>{{ $invoice->customer->email }}</div>
                <div>{{ $invoice->customer->phone }}</div>
            </div>
            <div class="text-right">
                <div class="text-xs uppercase text-gray-400 font-semibold mb-1">Invoice Details</div>
                <div>Date: {{ $invoice->created_at->format('d M Y') }}</div>
                <div>Description: {{ $invoice->description ?? '—' }}</div>
                <div>Status:
                    <span class="inline-block px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-xs font-semibold">{{ $invoice->status }}</span>
                </div>
            </div>
        </div>

        {{-- Duplicate warning — never printed, screen only --}}
        @php($totalpaid = $invoice->receipts->sum('amount'))
        @if($totalpaid > $invoice->amount)
        <div class="no-print mb-4 rounded-box border border-amber-300 bg-amber-50 text-amber-800 text-sm px-4 py-3">
            <b>Total receipted ({{ $invoice->currency->name }} {{ number_format($totalpaid, 2) }}) is more than the invoice amount
            ({{ $invoice->currency->name }} {{ number_format($invoice->amount, 2) }}).</b> This usually means a receipt was captured
            twice by mistake — use the delete button below to remove the extra one.
        </div>
        @endif

        {{-- Receipts table --}}
        <table class="w-full text-sm border-collapse mb-6">
            <thead>
                <tr class="border-b-2 border-gray-800 text-left">
                    <th class="py-2">Date</th>
                    <th class="py-2">Receipt No</th>
                    <th class="py-2 text-right">Amount</th>
                    <th class="py-2 no-print"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoice->receipts as $receipt)
                <tr class="border-b border-gray-200">
                    <td class="py-2">{{ $receipt->created_at->format('d M Y') }}</td>
                    <td class="py-2 font-mono">{{ $receipt->receipt_number }}</td>
                    <td class="py-2 text-right">{{ $receipt->currency->name ?? $invoice->currency->name }} {{ number_format($receipt->amount, 2) }}</td>
                    <td class="py-2 text-right no-print">
                        @can('invoices.receipt')
                        <x-button icon="o-trash" class="btn-xs btn-outline btn-error" tooltip="Delete this receipt"
                            wire:click="deletereceipt({{ $receipt->id }})"
                            wire:confirm="Delete receipt {{ $receipt->receipt_number }}? Only do this for a genuine mistake or duplicate — this cannot be undone from here." spinner />
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-4 text-center text-gray-400">No receipts recorded for this invoice</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Totals --}}
        <div class="flex justify-end">
            <table class="text-sm w-64">
                <tr>
                    <td class="py-1 text-gray-500">Invoice Amount</td>
                    <td class="py-1 text-right">{{ $invoice->currency->name }} {{ number_format($invoice->amount, 2) }}</td>
                </tr>
                <tr class="font-bold border-t border-gray-300">
                    <td class="py-2">Total Paid</td>
                    <td class="py-2 text-right">{{ $invoice->currency->name }} {{ number_format($invoice->receipts->sum('amount'), 2) }}</td>
                </tr>
            </table>
        </div>

        <div class="mt-10 pt-4 border-t border-gray-200 text-xs text-gray-400 text-center">
            This is a system-generated receipt from {{ config('app.name') }} — {{ config('app.url') }}
        </div>
    </div>
</div>
