<div>
   
    <div class="p-5 rounded-sm border bg-base-200 border-gray-200 hover:bg-blue-100 transition-colors duration-200 cursor-pointer" wire:click="$set('modal', true)">          
        <div class="flex justify-center items-center">
            <x-icon name="o-cog-6-tooth" class="w-12 h-12 text-blue-300 text-center" />
        </div>
        <h2 class="text-lg font-bold text-center">Applicant types</h2>        
    </div>
    <x-modal title="Applicant types" wire:model="modal" box-class="max-w-3xl">
        <x-table :headers="$headers" :rows="$applicanttypes">
            @scope('actions', $applicanttype)
            <div class="flex items-center space-x-2">
                @can('configurations.modify')
                <x-button icon="o-pencil" class="btn-sm btn-info btn-outline" 
                    wire:click="edit({{ $applicanttype->id }})" spinner />
                <x-button icon="o-trash" class="btn-sm btn-outline btn-error" 
                    wire:click="delete({{ $applicanttype->id }})" confirm="Are you sure?" spinner />
                @endcan
            </div>
            @endscope
            <x-slot:empty>
                <x-alert class="alert-error" title="No applicant types found." />
            </x-slot:empty>
        </x-table>

        <x-slot:actions>
            @can('configurations.modify')
            <x-button icon="o-plus" class="btn-sm btn-info btn-outline" 
                wire:click="$set('modifymodal', true)" spinner />
            @endcan
        </x-slot:actions>
      
    </x-modal>
    <x-modal title="{{ $id ? 'Edit applicant type' : 'New applicant type' }}" wire:model="modifymodal">
        <x-form wire:submit="save">
            <div class="grid gap-2">
            <x-input label="Name" wire:model="name" />
            <x-input label="Description" wire:model="description" />
            </div>
         
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.modifymodal = false" />
                <x-button label="{{ $id ? 'Update' : 'Save' }}" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>
  
</div>
