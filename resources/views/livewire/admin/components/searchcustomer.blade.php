<div>
    <x-input placeholder="Search" wire:model.live.debounce.500ms="search" />

    <x-modal title="Customers" wire:model="modal" box-class="max-w-4xl">
        <x-table :headers="$headers" :rows="$customers">
           
            @scope('actions', $customer)
            @can('customers.modify')
            <div class="flex items-center space-x-2">
           
                @can('customers.access')
                <x-button icon="o-eye" class="btn-sm btn-info btn-outline" 
                    link="{{ route('customers.show', $customer->uuid) }}" spinner />
                @endcan
            </div>
            @endcan
            @endscope
            <x-slot:empty>
                <x-alert class="alert-error" title="No customers found." />
            </x-slot:empty>
        </x-table>
    </x-modal>
</div>
