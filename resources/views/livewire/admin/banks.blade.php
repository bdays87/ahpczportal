<div>
    <x-breadcrumbs :items="$breadcrumbs"    class="bg-base-300 p-3 rounded-box mt-2" />
    @can('banks.access')
    <x-card title="Banks" class="mt-5 border-2 border-gray-200" separator>
        <x-slot:menu>
            @can('banks.modify')
            <x-button label="New" responsive icon="o-plus" class="btn-outline" @click="$wire.modal = true" />
            @endcan
        </x-slot:menu>
        <x-table :headers="$headers" :rows="$banks">
            @scope('cell_name', $bank)
            {{ $bank->name }}
            @endscope
            @scope('actions', $bank)
            @can('banks.modify')
            <div class="flex items-center space-x-2">
                <x-button icon="o-pencil" class="btn-sm btn-info btn-outline" 
                    wire:click="edit({{ $bank->id }})" spinner />
                <x-button icon="o-trash" class="btn-sm btn-outline btn-error" 
                    wire:click="delete({{ $bank->id }})" wire:confirm="Are you sure?" spinner />
                <x-button icon="o-building-library" class="btn-sm btn-info btn-outline" 
                    wire:click="getbank({{ $bank->id }})" spinner />
            </div>

            @endcan
            @endscope

            <x-slot:empty>
                <x-alert class="alert-error" title="No banks found." />
            </x-slot:empty>
        </x-table>
    </x-card>
    @else
    <x-alert class="alert-error mt-3" title="You do not have permission to view banks." />
    @endcan

    <x-modal title="{{ $id ? 'Edit Bank' : 'New Bank' }}" wire:model="modal">
        <x-form wire:submit="save">
            <x-input label="Name" wire:model="name" />
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.modal = false" />
                <x-button label="{{ $id ? 'Update' : 'Save' }}" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>

    <x-modal title="{{ $bank?->name }} Account numbers" wire:model="accountmodal" box-class="max-w-3xl">
     <x-form wire:submit="saveaccount">
        <div class="grid grid-cols-3 gap-4">
        <x-input placeholder="Account Number" wire:model="account_number" />
        <x-select placeholder="Currency" wire:model="currency_id" placeholder="Select Currency" :options="$currencies" option-label="name" option-value="id" />
        <x-button label="{{ $account_id ? 'Update' : 'Save' }}" type="submit" class="btn-primary" spinner="saveaccount" />
        </div>        
    </x-form>

    <table class="table table-compact">
        <thead>
            <tr>
                <th>Account Number</th>
                <th>Currency</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($bank->accounts??[] as $account)
            <tr>
                <td>{{ $account->account_number }}</td>
                <td>{{ $account->currency?->name }}</td>
                <td>
                    <div class="flex items-center space-x-2">
                        <x-button icon="o-pencil" class="btn-sm btn-info btn-outline" 
                            wire:click="editaccount({{ $account->id }})" spinner />
                        <x-button icon="o-trash" class="btn-sm btn-outline btn-error" 
                            wire:click="deleteaccount({{ $account->id }})" wire:confirm="Are you sure?" spinner />
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center">No accounts found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </x-modal>
</div>
