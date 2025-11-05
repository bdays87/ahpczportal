<div>
    <x-dropdown icon="o-wallet" label="Topup Wallet" class="btn-primary">
        <x-menu-item title="Paynow" @click="$wire.paynowmodal = true"  icon="o-credit-card" />
        @if(auth()->user()->accounttype_id == 1)
        <x-menu-item title="Manual" @click="$wire.manualmodal= true"  icon="o-banknotes" />
        @endif
    </x-dropdown>
    <x-modal title="Paynow" wire:model="paynowmodal">
        @if($errormessage)
        <x-alert class="alert-error mt-3" title="Error" :description="$errormessage" />
        @endif
    <x-form wire:submit.prevent="paynow">
        <div class="grid  gap-2">
            <x-input label="Amount" wire:model="amount" />
            <x-select label="Currency" wire:model="currency" :options="$currencies??[]" option-label="name" option-value="id" placeholder="Select" />
        </div>
        <x-slot:actions>
            <x-button label="Cancel" class="btn-sm btn-outline btn-error" wire:click="$set('paynowmodal', false)" />
            <x-button label="Pay" class="btn-sm btn-info btn-outline" type="submit" spinner="paynow" />
        </x-slot:actions>
    </x-form>
    </x-modal>

    <x-modal title="Manual" wire:model="manualmodal">
        @if($errormessage)
        <x-alert class="alert-error mt-3" title="Error" :description="$errormessage" />
        @endif
    <x-form wire:submit.prevent="savemanual">
        <div class="grid  gap-2">
            <x-input label="Amount" wire:model="amount" />
            <x-select label="Mode" wire:model="mode" :options="[['id'=>'CASH', 'name'=>'Cash'], ['id'=>'SWIPE', 'name'=>'Swipe']]" option-label="name" option-value="id" />
            <x-select label="Currency" wire:model="currency" :options="$currencies??[]" option-label="name" option-value="id" placeholder="Select" />
        </div>
        <x-slot:actions>
            <x-button label="Cancel" class="btn-sm btn-outline btn-error" wire:click="$set('manualmodal', false)" />
            <x-button label="Pay" class="btn-sm btn-info btn-outline" type="submit" spinner="cash" />
        </x-slot:actions>
    </x-form>
    </x-modal>
</div>
