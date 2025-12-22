<div>
    <x-card title="Customer Registrations Imports" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            <x-input placeholder="Search" wire:model.live="search" />
            <x-button label="Import" responsive icon="o-plus" class="btn-outline" @click="$wire.importmodal = true" />
            <x-button label="New" responsive icon="o-plus" class="btn-outline" @click="$wire.editmodal = true" />
        </x-slot:menu>
        <x-table :headers="$headers" :rows="$customerregistrationimports" with-pagination>
            @scope('actions', $customerregistrationimport)
            <div class="flex items-center space-x-2">
                <x-button icon="o-pencil" class="btn-sm btn-info btn-outline" 
                    wire:click="edit({{ $customerregistrationimport->id }})" spinner />
                <x-button icon="o-trash" class="btn-sm btn-error btn-outline" 
                    wire:click="delete({{ $customerregistrationimport->id }})" wire:confirm="Are you sure?" spinner />
            </div>
            @endscope
            @scope('cell_status', $customerregistrationimport)
            <x-badge class="{{ $customerregistrationimport->status == 'APPROVED' ? 'badge-success' : 'badge-error' }}" value="{{ $customerregistrationimport->status == 'APPROVED' ? 'Approved' : 'Pending' }}" />
            @endscope
            @scope('cell_proceeded', $customerregistrationimport)
            <x-badge class="{{ $customerregistrationimport->proceeded == 'Y' ? 'badge-success' : 'badge-error' }}" value="{{ $customerregistrationimport->proceeded == 'Y' ? 'Yes' : 'No' }}" />
            @endscope
            <x-slot:empty>
                <x-alert class="alert-error" title="No customer registrations imports found." />
            </x-slot:empty>
        </x-table>
    </x-card>
    <x-modal title="{{ $id ? 'Edit' : 'Add' }} Customer Registration" wire:model="editmodal">
        <x-form wire:submit.prevent="savecustomerregistration">
            <x-input label="RegNumber" wire:model="regnumber" />
            <x-input label="Prefix" wire:model="prefix" />
            <x-input label="CertificateNumber" wire:model="certificatenumber" />
            <x-input label="RegistrationDate" wire:model="registrationdate" />
            <x-input label="Status" wire:model="status" />
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.editmodal = false" />
                <x-button label="{{ $id ? 'Update' : 'Save' }}" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>
    <x-modal title="Import Customer Registrations" wire:model="importmodal">
        <x-form wire:submit.prevent="saveimport">
            <x-input label="File" wire:model="file" type="file" />
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.importmodal = false" />
                <x-button label="Import" type="submit" class="btn-primary" spinner="import" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
