<div>
    <x-breadcrumbs :items="$breadcrumbs"    class="bg-base-300 p-3 rounded-box mt-2" />

    @can('tire.access')
    <x-card title="Profession tires" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            @can('tire.modify')
            <x-button label="New" responsive icon="o-plus" class="btn-outline" @click="$wire.modal = true" />
            @endcan
        </x-slot:menu>

        <x-table :headers="$headers" :rows="$tires">
       
            @scope('actions', $tire)
            <div class="flex items-center space-x-2">
                @can('tire.modify')
                <x-button icon="o-pencil" class="btn-sm btn-info btn-outline" 
                    wire:click="edit({{ $tire->id }})" spinner />
                <x-button icon="o-trash" class="btn-sm btn-outline btn-error" 
                    wire:click="delete({{ $tire->id }})" confirm="Are you sure?" spinner />
                <x-button icon="o-document" class="btn-sm btn-outline btn-info" 
                    wire:click="getassigneddocuments({{ $tire->id }})" spinner />
                @endcan
            </div>
            @endscope
            <x-slot:empty>
                <x-alert class="alert-error" title="No tires found." />
            </x-slot:empty>
        </x-table>
    </x-card>
    @else
    <x-alert class="alert-error mt-3" title="You do not have permission to access this page." />
    @endcan
    <x-modal wire:model="modal"  title="{{ $id ? 'Edit Tire' : 'New Tire' }}">
        <x-form wire:submit="{{ $id ? 'update' : 'save' }}">
            <div class="grid gap-2">
            <x-input label="Name" wire:model="name" />
            </div>
         

            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.modal = false" />
                <x-button label="{{ $id ? 'Update' : 'Save' }}" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>
    <x-modal title="Assign Document" wire:model="documentmodal" box-class="max-w-3xl">
        <x-form wire:submit="assignDocument">
            <div class="grid grid-cols-3 gap-2">
            <x-select   :options="$customertypes" option-label="name" placeholder="Select Customer Type..." option-value="id" wire:model="customertype_id" single />
            <x-select   :options="$documents" option-label="name" placeholder="Select Document..." option-value="id" wire:model="document_id" single/>
           
            <x-button label="Assign" icon="o-plus" type="submit" class=" btn-primary" spinner="assignDocument" />

            </div>
          
        </x-form>
        @if (count($assignedDocuments) > 0)
        @php 
        $groupbycustomertype = $assignedDocuments->groupBy('customertype_id');
     @endphp
     @forelse ($groupbycustomertype as $customertype => $documents)
     <x-card title="{{ $documents->first()->customertype->name }}" class="border-2 border-gray-200 mt-2" separator>
        <table class="table">
            <thead>
                <tr>
                 
                    <th>Document</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($documents as $document)
                <tr>
                  
                    <td>{{ $document->document->name }}</td>
                    <td class="flex justify-end items-center space-x-2">
                        <x-button icon="o-trash" class="btn-sm btn-outline btn-error" 
                            wire:click="unassignDocument({{ $document->id }})" spinner />
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center p-2 bg-red-400 text-red">No documents assigned</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </x-card>
        @empty
        <x-alert class="alert-error" title="No documents assigned." />
        @endforelse
   
        @else
        <x-alert class="alert-error" title="No documents assigned." />
        @endIf
    </x-modal>
</div>
