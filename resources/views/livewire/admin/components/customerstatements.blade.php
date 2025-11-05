<div>
   <x-card title="Customer Statement" class="mt-5 border-2 border-gray-200" separator>
    <table class="table table-zebra">
        <thead>
            <tr>
                <th>Date</th>
                <th>Source</th>
                <th>Debit</th>
                <th>Credit</th>
                <th>Balance</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($statement as $receipt)
            <tr>
                <td>{{ $receipt->created_at }}</td>
                <td>{{ $receipt->source }}</td>
                <td>{{ $receipt->currency?->name }}{{ $receipt->amount }}</td>
                <td>{{ $receipt->currency?->name }}{{ $receipt->receipts?->sum('amount') }}</td>
                <td>{{ $receipt->currency?->name }}{{ $receipt->amount - $receipt->receipts?->sum('amount') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center text-red-500 p-3 bg-red-50">No records found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
   
   </x-card>
</div>
