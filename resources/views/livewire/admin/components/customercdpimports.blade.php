<div>
    <x-card title="Customer CDP Imports" separator class="mt-5 border-2 border-gray-200">
     <x-slot:menu>
         <x-input placeholder="Search" wire:model.live="search" />
         <x-button label="Import" responsive icon="o-plus" class="btn-outline" @click="$wire.importmodal = true" />
         <x-button label="New" responsive icon="o-plus" class="btn-outline" @click="$wire.editmodal = true" />
     </x-slot:menu>
     <x-table :headers="$headers" :rows="$customercdps" with-pagination>
         @scope('actions', $customercdp)
         <div class="flex items-center space-x-2">
             <x-button icon="o-pencil" class="btn-sm btn-info btn-outline" 
                 wire:click="edit({{ $customercdp->id }})" spinner />
             <x-button icon="o-trash" class="btn-sm btn-error btn-outline" 
                 wire:click="delete({{ $customercdp->id }})" wire:confirm="Are you sure?" spinner />
         </div>
         @endscope
         <x-slot:empty>
             <x-alert class="alert-error" title="No customer cdp imports found." />
         </x-slot:empty>
     </x-table>
    </x-card>
    <x-modal title="Import Customer CDP" wire:model="importmodal">
     <x-form wire:submit.prevent="saveimport">
         <x-input label="File" wire:model="file" type="file" />
         <x-slot:actions>
             <x-button label="Cancel" @click="$wire.importmodal = false" />
             <x-button label="Import" type="submit" class="btn-primary" spinner="save" />
         </x-slot:actions>
     </x-form>
    </x-modal>
    <x-modal title="New Customer CDP" wire:model="editmodal">
     <x-form wire:submit.prevent="savecustomercdp">
         <x-input label="RegNumber" wire:model="regnumber" />
         <x-input label="Points" wire:model="points" />
         <x-input label="Year" wire:model="year" />
         <x-slot:actions>
             <x-button label="Cancel" @click="$wire.editmodal = false" />
             <x-button label="Save" type="submit" class="btn-primary" spinner="save" />
         </x-slot:actions>
     </x-form>
    </x-modal>
 </div>
 