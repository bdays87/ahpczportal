<div>
   
    <div class="p-5 rounded-sm border bg-base-200 border-gray-200 hover:bg-blue-100 transition-colors duration-200 cursor-pointer" wire:click="$set('modal', true)">          
        <div class="flex justify-center items-center">
            <x-icon name="o-cog-6-tooth" class="w-12 h-12 text-blue-300 text-center" />
        </div>
        <h2 class="text-lg font-bold text-center">Customer types</h2>        
    </div>
    <x-modal title="Customer types" wire:model="modal" box-class="max-w-3xl">
        <x-table :headers="$headers" :rows="$customertypes">
            @scope('actions', $customertype)
            <div class="flex items-center space-x-2">
                @can('configurations.modify')
                <x-button icon="o-pencil" class="btn-sm btn-info btn-outline" 
                    wire:click="edit({{ $customertype->id }})" spinner />
                <x-button icon="o-trash" class="btn-sm btn-outline btn-error" 
                    wire:click="delete({{ $customertype->id }})" confirm="Are you sure?" spinner />
                <x-button icon="o-book-open" class="btn-sm btn-success btn-outline" 
                    wire:click="showassignmodal({{ $customertype->id }})" spinner />
                @endcan
            </div>
            @endscope

            @scope('cell_registertypes', $customertype)
            <div class=" space-x-2">
                @forelse     ($customertype->registertypes as $registertype)
                <div class="flex justify-between  space-x-2">
                    <span class="badge ">{{ $registertype->registertype->name }}</span>
                    <x-button icon="o-x-mark" class="btn-xs btn-outline btn-error" 
                        wire:click="removeregistertype({{ $customertype->id }},{{ $registertype->registertype->id }})" confirm="Are you sure?" spinner />
                </div>
                @empty
                    <span class="badge badge-error">No register types assigned</span>
                @endforelse
            </div>
            @endscope
            <x-slot:empty>
                <x-alert class="alert-error" title="No customer types found." />
            </x-slot:empty>
        </x-table>

        <x-slot:actions>
            @can('configurations.modify')
            <x-button icon="o-plus" class="btn-sm btn-info btn-outline" 
                wire:click="$set('modifymodal', true)" spinner />
            @endcan
        </x-slot:actions>
      
    </x-modal>
    <x-modal title="{{ $id ? 'Edit customer type' : 'New customer type' }}" wire:model="modifymodal">
        <x-form wire:submit="save">
            <div class="grid gap-2">
            <x-input label="Name" wire:model="name" />
            </div>
         
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.modifymodal = false" />
                <x-button label="{{ $id ? 'Update' : 'Save' }}" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>
    <x-modal title="Assign register type" wire:model="assignmodal">
        <x-form wire:submit="assign">
            <div class="grid gap-2">
            <x-select label="Register type" wire:model="registertype_id" :options="$registertypes" option-label="name" option-value="id" placeholder="Select" />
            </div>
         
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.assignmodal = false" />
                <x-button label="Assign" type="submit" class="btn-primary" spinner="assign" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
