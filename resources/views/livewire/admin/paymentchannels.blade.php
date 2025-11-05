<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />

    <x-card title="Payment Channels" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            <x-button label="New" responsive icon="o-plus" class="btn-outline" @click="$wire.modal = true" />
        </x-slot:menu>
        <x-table :headers="$headers" :rows="$paymentchannels">
            @scope('cell_logo', $paymentchannel)
            @if ($paymentchannel->logo)
            <img src="{{ asset('storage/' . $paymentchannel->logo) }}" alt="Logo" class="w-16 h-16 object-cover" />
            @else
            <img src="{{ asset('imgs/noimage.jpg') }}" alt="Logo" class="w-16 h-16 object-cover" />
            @endif
            @endscope
            @scope('cell_showpublic', $paymentchannel)
            <x-badge class="{{ $paymentchannel->showpublic == 'Y' ? 'badge-success' : 'badge-error' }}" value="{{ $paymentchannel->showpublic == 'Y' ? 'Yes' : 'No' }}" />
            @endscope
            @scope('actions', $paymentchannel)
            <div class="flex items-center space-x-2">
                @can('paymentchannels.modify')
                <x-button icon="o-pencil" class="btn-sm btn-info btn-outline" 
                    wire:click="edit({{ $paymentchannel->id }})" spinner />
                <x-button icon="o-trash" class="btn-sm btn-outline btn-error" 
                    wire:click="delete({{ $paymentchannel->id }})" confirm="Are you sure?" spinner />
                    <x-button icon="o-cog" class="btn-sm btn-outline btn-info" 
                    wire:click="getparameters({{ $paymentchannel->id }})" spinner />
                @endcan
            </div>
            @endscope
            <x-slot:empty>
                <x-alert class="alert-error" title="No payment channels found." />
            </x-slot:empty>
        </x-table>
    </x-card>
    <x-modal title="{{ $id ? 'Edit' : 'New' }}Payment Channel" wire:model="modal">
        <x-form wire:submit.prevent="save">
            <div class="grid  gap-2">
            <x-input label="Name" wire:model="name" />
            <x-select label="Show public" wire:model="status" :options="[['id'=>'Y', 'name'=>'Yes'], ['id'=>'N', 'name'=>'No']]" option-label="name" option-value="id" placeholder="Select Public Status" />
            <x-input label="File" wire:model="file" type="file" />
            </div>      
        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.modal = false" />
            <x-button label="{{ $id ? 'Update' : 'Save' }}" type="submit" class="btn-primary" spinner="save" />
        </x-slot:actions>
    </x-form>
    </x-modal>
    <x-modal wire:model="parametermodal" title="{{ $paymentchannel->name ?? 'Parameters' }}" box-class="max-w-6xl">

        <x-form wire:submit.prevent="saveparameter">
        <div class="grid grid-cols-4 gap-2">
        <x-select wire:model="currency_id" :options="$currencies" option-label="name" option-value="id" placeholder="Select Currency" />
        <x-input placeholder="Key" wire:model="key" />
        <x-input placeholder="Value" wire:model="value" />
        <x-button label="{{ $parameter_id ? 'Update' : 'Add' }} Parameter" type="submit" class="btn-primary" spinner="saveparameter" />
        </div>
        </x-form>
        <table class="table table-zebra mt-5">
            <thead>
                <tr>
                    <th>Currency</th>
                    <th>Key</th>
                    <th>Value</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @if ($paymentchannel)
                @forelse ($paymentchannel->parameters as $parameter)
                <tr>
                    <td>{{ $parameter->currency->name }}</td>
                    <td>{{ $parameter->key }}</td>
                    <td>{{ $parameter->value }}</td>
                    <td>
                        <x-button icon="o-pencil" class="btn-sm btn-info btn-outline" 
                            wire:click="editparameter({{ $parameter->id }})" spinner />
                        <x-button icon="o-trash" class="btn-sm btn-outline btn-error" 
                            wire:click="deleteparameter({{ $parameter->id }})" confirm="Are you sure?" wire:confirm="Are you sure?" spinner />
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-red-500 p-3 bg-red-50">No parameters found.</td>
                </tr>
                @endforelse
                @else
                <tr>
                    <td colspan="4" class="text-center text-red-500 p-3 bg-red-50">No parameters found.</td>
                </tr>
                @endif
            </tbody>
        </table>

    </x-modal>
</div>
