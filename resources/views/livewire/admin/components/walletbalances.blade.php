<div>
    <div class="grid lg:grid-cols-2 gap-2 mt-2 mb-2">
    @foreach ($balances as $balance)
   
     <div class="p-3 rounded-lg outline outline-1 outline-gray-200 text-center ">
        <div class="text-sm text-gray-500">{{ $balance['currency'] }} wallet balance</div>
       <div class="text-lg font-bold">{{ number_format($balance['balance'], 2) }}</div>
       <div><x-button label="Topup" icon="o-arrow-right" class="btn btn-xs btn-primary btn-outline" wire:click="opentopup({{ $balance['currency_id'] }})" /></div>
    </div>
    @endforeach
    </div>

    <x-modal title="Topup {{ $currency }} wallet" separator wire:model="topupmodal">
        @if($errormessage)
        <x-alert class="alert-error mt-3" title="Error" :description="$errormessage" />
        @endif
        <x-form wire:submit="processtopup">
            <x-input label="Amount" type="number"  prefix="{{ $currency }}" min="1" wire:model="amount" />
            <x-card title="Select payment method" separator>
            <x-checkbox label="Paynow" wire:model.live="paynow" hint="Ecocash, OneMoney,Telecash,Innbucks,Omari,Zimswitch,Visa" left />
            @if(auth()->user()->accounttype_id == 1)
            <x-checkbox label="cash" wire:model.live="cash" left />
            <x-checkbox label="Swipe" wire:model.live="swipe" left />
            @endif
            </x-card>
            <x-button label="Topup" icon="o-arrow-right" class="btn btn-primary btn-outline" type="submit" spinner="processtopup" />
        </x-form>
    </x-modal>
</div>
 