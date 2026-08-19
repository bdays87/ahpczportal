<div>
    <x-card title="Invoices" subtitle="Current year first" class="mt-5 border-2 border-gray-200" separator>
        <div class="overflow-x-auto rounded-box border border-gray-100">
            <table class="table table-zebra table-sm">
                <thead>
                    <tr class="bg-base-200/70">
                        <th>Date</th>
                        <th>Invoice No</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Receipt No</th>
                        <th class="text-right sticky right-0 z-20 bg-base-200 shadow-[-4px_0_6px_-2px_rgba(0,0,0,0.15)]"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                    <tr class="group hover:bg-base-200 transition-colors">
                        <td class="whitespace-nowrap">{{ $invoice->created_at->format('d M Y') }}</td>
                        <td class="font-mono text-xs whitespace-nowrap">{{ $invoice->invoice_number }}</td>
                        <td class="whitespace-normal break-words max-w-xs">{{ $invoice->description ?? '—' }}</td>
                        <td class="whitespace-nowrap font-semibold">{{ $invoice->currency->name ?? '' }} {{ number_format($invoice->amount, 2) }}</td>
                        <td class="whitespace-nowrap">
                            <x-badge value="{{ $invoice->status }}" class="badge-sm whitespace-nowrap {{
                                $invoice->status == 'PAID' ? 'badge-success' :
                                ($invoice->status == 'REJECTED' ? 'badge-error' :
                                ($invoice->status == 'AWAITING' ? 'badge-info' : 'badge-warning'))
                            }}" />
                        </td>
                        <td class="whitespace-nowrap">
                            @if($invoice->receipts->isNotEmpty())
                            <div class="flex flex-col gap-0.5">
                                @foreach($invoice->receipts as $receipt)
                                <span class="font-mono text-xs text-success">{{ $receipt->receipt_number }}</span>
                                @endforeach
                            </div>
                            @else
                            <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="text-right sticky right-0 z-10 bg-base-100 group-hover:bg-base-200 transition-colors shadow-[-4px_0_6px_-2px_rgba(0,0,0,0.08)]">
                            @can('invoices.receipt')
                            @if($invoice->status == 'PAID')
                            <x-button icon="o-printer" class="btn-xs btn-outline" tooltip="Print receipt"
                                link="{{ route('invoices.receipt.print', $invoice->uuid) }}" external />
                            @endif
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-6 text-gray-400">No invoices found for this customer.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</div>
