<div>
   
    <div class="p-5 rounded-sm border bg-base-200 border-gray-200 hover:bg-blue-100 transition-colors duration-200 cursor-pointer" wire:click="$set('modal', true)">          
        <div class="flex justify-center items-center">
            <x-icon name="o-cog-6-tooth" class="w-12 h-12 text-blue-300 text-center" />
        </div>
        <h2 class="text-lg font-bold text-center">Qualification categories</h2>        
    </div>
    <x-modal title="Qualification categories" wire:model="modal" box-class="max-w-3xl">
        <x-input label="Search" wire:model.live="search" />
        <x-table :headers="$headers" :rows="$qualificationcategories">
            @scope('actions', $qualificationcategory)
            <div class="flex items-center space-x-2">
                @can('configurations.modify')
                <x-button icon="o-pencil" class="btn-sm btn-info btn-outline" 
                    wire:click="edit({{ $qualificationcategory->id }})" spinner />
                <x-button icon="o-trash" class="btn-sm btn-outline btn-error" 
                    wire:click="delete({{ $qualificationcategory->id }})" wire:confirm="Are you sure?" spinner />
                @endcan
            </div>
            @endscope
            <x-slot:empty>
                <x-alert class="alert-error" title="No qualification categories found." />
            </x-slot:empty>
        </x-table>

        <x-slot:actions>
            @can('configurations.modify')
            <x-button icon="o-plus" class="btn-sm btn-info btn-outline" 
                wire:click="$set('modifymodal', true)" spinner />
            @endcan
        </x-slot:actions>
      
    </x-modal>
    <x-modal title="{{ $id ? 'Edit qualification category' : 'New qualification category' }}" wire:model="modifymodal">
        <x-form wire:submit="save">
            <div class="grid gap-2">
            <x-input label="Name" wire:model="name" />
            <x-select label="Require approval" wire:model="requireapproval" :options="[['id'=>'N','value'=>'No'],['id'=>'Y','value'=>'Yes']]" option-label="value" option-value="id" placeholder="Select" />
            </div>
         
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.modifymodal = false" />
                <x-button label="{{ $id ? 'Update' : 'Save' }}" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
