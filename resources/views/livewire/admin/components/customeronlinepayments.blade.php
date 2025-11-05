<div>
    <x-card title="Paynow Transactions" class="mt-5 border-2 border-gray-200">
        <table class="table table-zebra table-xs">
            <thead>
                <tr>
                 
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Payment Date</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transactions as $transaction)
                <tr>
                    
                    <td>{{ $transaction->customer->name }} {{ $transaction->customer->surname }}</td>
                    <td>{{ $transaction->currency->name }}{{ $transaction->amount }}</td>
                    <td>{{ $transaction->created_at }}</td>
                    <td>{{ $transaction->status }}</td>
                    <td>
                        <div class="flex items-center space-x-2">
                            @if (strtolower($transaction->status) == 'pending')
                            <x-button icon="o-eye" class="btn-sm btn-info btn-outline" 
                            wire:click="viewtransaction({{ $transaction->id }})" spinner />
                            @endif
                        </div>
                    </td>
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
