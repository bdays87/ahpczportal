<div>
    @if($customer)
    <div class="grid lg:grid-cols-2 gap-2 mt-2 mb-2">
    @foreach ($balances as $balance)
   
     <div class="p-3 rounded-lg outline outline-1 outline-gray-200 text-center ">
        <div class="text-sm text-gray-500">{{ $balance['currency'] }} wallet balance</div>
       <div class="text-lg font-bold">{{ number_format($balance['balance'], 2) }}</div>
       @if(config('generalutils.wallet_topup'))
       <div><x-button label="Topup" icon="o-arrow-right" class="btn btn-xs btn-primary btn-outline" wire:click="opentopup({{ $balance['currency_id'] }})" /></div>
       @endif
    </div>
    @endforeach
    </div>
    @endif

    <x-modal title="Topup {{ $currency }} wallet" separator wire:model="topupmodal">
        @if($errormessage)
        <x-alert class="alert-error mt-3" title="Error" :description="$errormessage" />
        @endif
        <x-form wire:submit="processtopup">
            <x-input label="Amount" type="number"  prefix="{{ $currency }}" min="1" wire:model="amount" />
            @if(auth()->user()->accounttype_id == 1)
            <x-select label="SelectMode" wire:model="mode" :options="[['id'=>'PAYNOW', 'name'=>'Paynow'], ['id'=>'CASH', 'name'=>'Cash'], ['id'=>'SWIPE', 'name'=>'Swipe']]" option-label="name" option-value="id" placeholder="Select" />
            @else
            <x-select label="SelectMode" wire:model="mode" :options="[['id'=>'PAYNOW', 'name'=>'Paynow']]" option-label="name" option-value="id" placeholder="Select" />
            @endif
                <x-button label="Process Topup" icon="o-arrow-right" class="btn btn-primary " type="submit" spinner="processtopup" />
        </x-form>
    </x-modal>
</div>
 