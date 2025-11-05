<div>

    <x-button label="Attach Payment" icon="o-paper-clip" class="btn w-full btn-secondary mt-2" wire:click="getattachpayments"/>
    <x-modal wire:model="attachmodal" title="Attach Payments" separator>
        <x-file wire:model.live="paymentfile" label="Receipt" hint="Only PDF" accept="application/pdf" />
         <x-hr />
        <table class="table table-zebra">
            <thead>
                <tr>
                    <th>Document</th>
                    <th></th> 
                </tr>
            </thead>
            <tbody>
                @forelse ($proofofpayments as $proofofpayment)
                <tr>
                    <td>{{ $proofofpayment->created_at }}</td>
                    <td class="flex justify-end items-center space-x-2">
                        <x-button icon="o-trash" class="btn-sm btn-outline btn-error" 
                            wire:click="deleteattachpayment({{ $proofofpayment->id }})" spinner />
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" class="text-center p-2 bg-red-400 text-red">No documents assigned</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <x-slot:actions>
            @if($proofofpayments->count() > 0)
            <x-button label="Submit for receipting" icon="o-paper-clip" class="btn btn-secondary" wire:confirm="Are you sure you want to submit for receipting?" wire:click="submitforverification"/>
            @endif
        </x-slot:actions>
    </x-modal>
</div>
