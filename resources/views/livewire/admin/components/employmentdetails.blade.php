<div>
   <x-card title="Employment details" class="mt-5 border-2 border-gray-200">
    <x-slot:menu>
        <x-button class="btn-primary btn-circle" icon="o-plus" separator wire:click="$set('modal', true)"/>
    </x-slot:menu>
   <x-table :headers="$headers" :rows="$customer->employmentdetails" class="table table-xs">
   @scope('actions', $employmentdetail)
   <div class="flex items-center space-x-2">
       @can('configurations.modify')
       <x-button icon="o-pencil" class="btn-sm btn-info btn-outline" 
           wire:click="edit({{ $employmentdetail->id }})" spinner />
       <x-button icon="o-trash" class="btn-sm btn-outline btn-error" 
           wire:click="delete({{ $employmentdetail->id }})" wire:confirm="Are you sure?" spinner />
       @endcan
   </div>
   @endscope
   @scope('cell_dates', $employmentdetail)
   {{ $employmentdetail->start_date }} - {{ $employmentdetail->end_date }}
   @endscope
   <x-slot:empty>
       <x-alert class="alert-error" title="No employment details found." />
   </x-slot:empty>
   </x-table>
   </x-card>

   <x-modal wire:model="modal" title="{{ $id ? 'Edit' : 'Add' }} Employment Detail" box-class="max-w-4xl">
    <x-form wire:submit.prevent="save">
        <div class="grid grid-cols-2 gap-2">
    <x-input label="Company Name" wire:model="companyname" />
    <x-input label="Position" wire:model="position" />
  
    </div>
    <div class="grid grid-cols-3 gap-2">
        <div class="pt-6">
        <x-checkbox label="Current Employment" wire:model.live="currentemployment" />
        </div>
    <x-input label="Start Date" wire:model="start_date" type="date" />
    @if(!$currentemployment)
    <x-input label="End Date" wire:model="end_date" type="date" />
    @endif
  
    </div>
    <div class="grid grid-cols-2 gap-2">
    <x-input label="Phone" wire:model="phone" />
    <x-input label="Email" wire:model="email" />
    <x-input label="Address" wire:model="address" />
    <x-input label="Contact Person" wire:model="contactperson" />
    </div>
    <x-slot:actions>
        <x-button label="Cancel" class="btn-sm btn-outline btn-error" wire:click="$set('modal', false)" />
        <x-button label="Save" class="btn-sm btn-info btn-outline" type="submit" spinner="save" />
    </x-slot:actions>
    </x-form>
    </x-modal>
</div>
