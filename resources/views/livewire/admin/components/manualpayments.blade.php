<div>
   <x-card title="Manual Payments" class="mt-5 border-2 border-gray-200">
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Year</th>
                <th>Mode</th>
                <th>Amount</th>
                <th>Captured by</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($manualpayments as $manualpayment)
                <tr>
                    <td>{{ $manualpayment->created_at }}</td>
                    <td>{{ $manualpayment->year }}</td>
                    <td>{{ $manualpayment->mode }}</td>
                    <td>{{ $manualpayment->amount }}</td>
                    <td>{{ $manualpayment->receiptby->name }} {{ $manualpayment->receiptby->surname }}</td>
                    <td>
                        <div class="flex items-center space-x-2">
                            @can('manualpayments.delete')
                            <x-button icon="o-trash" class="btn-sm btn-outline btn-error" 
                            wire:click="deletemanualpayment({{ $manualpayment->id }})" wire:confirm="Are you sure?" spinner />
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center p-5 bg-red-300 text-red-500">No manual payments found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
   </x-card>
</div>
