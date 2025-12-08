<div>
    <x-button label="Settle invoice" icon="o-arrow-right"  class="btn btn-success w-full" wire:click="$set('paymentmodal',true)"/>
    <x-modal wire:model="paymentmodal" title="Receipting" separator>
    
        <table class="table  table-zebra">
            <tbody>
                <tr>
                    <td>{{ $invoice?->description }}</td>
                    <td>{{ $invoice?->currency->name }}{{ $invoice?->amount }}</td>
                </tr>
            </tbody> 
        </table>
        <x-card title="Invoice Settlement" separator class="border-2 rounded-lg mt-2 border-gray-200" progress-indicator>
           <x-form wire:submit="settleinvoice">
            <div class="grid gap-2">
            <x-select wire:model.live="currency_id" :options="$currencies" option-label="name" option-value="id" placeholder="Select" />
           <x-input  label="Exchange Rate" disabled value="{{ $exchangerate?->rate }}" />
            <x-input prefix="{{ $prefix }}" label="Total payable" value="{{ $totalpable }}" max="{{ $totalpable }}" type="number" wire:model="totalpable" />
            <x-input prefix="{{ $prefix }}" label="Wallet balance" disabled value="{{ $walletbalance ?? 0 }}" />
           </div>
      
            @if($walletbalance <= 0)
                <x-alert class="alert-error" title="Insufficient wallet balance" />
            @else
            <x-button label="Make payment" icon="o-arrow-right"  class="btn mt-2 w-full btn-success" type="submit" spinner/>
            @endif
           </x-form>
        
        </x-card> 
        </x-modal>
</div>
