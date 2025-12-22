<div>
    <x-card title="Customer Applications Imports" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
          <x-input placeholder="Search" wire:model.live="search" />
          <x-button label="Import" responsive icon="o-plus" class="btn-outline" @click="$wire.importmodal = true" />
          <x-button label="New" responsive icon="o-plus" class="btn-outline" @click="$wire.editmodal = true" />
        </x-slot:menu>
        <x-table :headers="$headers" :rows="$customerapplicationimports" with-pagination>
          @scope('actions', $customerapplicationimport)
          <div class="flex items-center space-x-2">
            <x-button icon="o-pencil" class="btn-sm btn-info btn-outline" 
              wire:click="edit({{ $customerapplicationimport->id }})" spinner />
            <x-button icon="o-trash" class="btn-sm btn-error btn-outline" 
              wire:click="delete({{ $customerapplicationimport->id }})" wire:confirm="Are you sure?" spinner />
          </div>
          @endscope
          @scope('cell_status', $customerapplicationimport)
          <x-badge class="{{ $customerapplicationimport->status == 'APPROVED' ? 'badge-success' : 'badge-error' }}" value="{{ $customerapplicationimport->status == 'APPROVED' ? 'Approved' : 'Pending' }}" />
          @endscope
          <x-slot:empty>
            <x-alert class="alert-error" title="No customer applications imports found." />
          </x-slot:empty>
        </x-table>
      </x-card>
      <x-modal title="Import Customer Applications" wire:model="importmodal">
        <x-form wire:submit.prevent="saveimport">
          <x-input label="File" wire:model="file" type="file" />
          <x-slot:actions>
            <x-button label="Cancel" @click="$wire.importmodal = false" />
            <x-button label="Import" type="submit" class="btn-primary" spinner="save" />
          </x-slot:actions>
        </x-form>
      </x-modal>
      <x-modal title="{{ $id ? 'Edit' : 'Add' }} Customer Application" wire:model="editmodal">
        <x-form wire:submit.prevent="save">
          <div class="grid grid-cols-2 gap-2">
            <x-input label="Reg Number" wire:model="regnumber" />
            <x-input label="Prefix" wire:model="prefix" />
            <x-input label="Application Type" wire:model="applicationtype" />
            <x-input label="Register Type" wire:model="registertype" />
            <x-input label="Certificate Number" wire:model="certificatenumber" />
            <x-input label="Registration Date" wire:model="registrationdate" />
            <x-input label="Certificate Expiry Date" wire:model="certificateexpirydate" />
            <x-input label="Year" wire:model="year" />
            <x-input label="Status" wire:model="status" />
          </div>
          <x-slot:actions>
            <x-button label="Cancel" @click="$wire.editmodal = false" />
            <x-button label="{{ $id ? 'Update' : 'Save' }}" type="submit" class="btn-primary" spinner="save" />
          </x-slot:actions>
        </x-form>
      </x-modal>
</div>
