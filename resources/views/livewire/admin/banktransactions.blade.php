<div>
    <x-breadcrumbs :items="$breadcrumbs"  class="bg-base-300 p-3 rounded-box mt-2"/>
    <x-card title="Bank Transactions" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            <x-input placeholder="Search" wire:model.live="search" />
            @can('configurations.modify')
            
            <x-button icon="o-plus" label="New Bank Transaction" class="btn-primary" 
                wire:click="$set('modal', true)" spinner />
                <x-button icon="o-plus" label="Import Bank Transaction" class="btn-primary" 
                wire:click="$set('importmodal', true)" spinner />
            @endcan
        </x-slot:menu>
      
        <x-table :headers="$headers" :rows="$banktransactions">
            @scope('actions', $banktransaction)
            <div class="flex items-center space-x-2">
                @can('banktransactions.modify')
                @if($banktransaction->status =="PENDING")
                <x-button icon="o-pencil" class="btn-sm btn-info btn-outline" 
                    wire:click="edit({{ $banktransaction->id }})" spinner />
                <x-button icon="o-trash" class="btn-sm btn-outline btn-error" 
                    wire:click="delete({{ $banktransaction->id }})" wire:confirm="Are you sure?" spinner />
                    @endif
                @endcan
            </div>
            @endscope
            <x-slot:empty>
                <x-alert class="alert-error" title="No bank transactions found." />
            </x-slot:empty>
        </x-table>

    
    </x-card>

    <x-modal wire:model="modal" title="{{ $id ? 'Edit Bank Transaction' : 'New Bank Transaction' }}">
        <x-form wire:submit="save">
            <div class="grid grid-cols-2 gap-2">
                <x-input label="Statement Reference" wire:model="statementreference" />             
                <x-select label="Bank" wire:model.live="bank_id" :options="$banks" option-label="name" option-value="id" placeholder="Select Bank" />
                <x-select label="Account" wire:model="accountnumber" :options="$accounts" option-label="account_number" option-value="account_number" placeholder="Select Account" />
                <x-input label="Source Reference" wire:model="source_reference" />
                <x-input label="Transaction Date" wire:model="transaction_date" type="date" />
                <x-input label="Amount" wire:model="amount" />
            </div>
            <div class="grid  gap-2">
                <x-input label="Description" wire:model="description" />
            </div>
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.modal = false" />
                <x-button label="Save" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>
    <x-modal wire:model="importmodal" title="Import Bank Transaction">
        <x-form wire:submit="importrecords">
            <div class="grid  gap-2">
                <x-input label="File" wire:model="file" type="file" />
            </div>
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.importmodal = false" />
                <x-button label="Import" type="submit" class="btn-primary" spinner="import" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
