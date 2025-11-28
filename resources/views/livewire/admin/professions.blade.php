<div>
    <x-breadcrumbs :items="$breadcrumbs"    class="bg-base-300 p-3 rounded-box mt-2" />
    @can('professions.access')
    <x-card title="Professions" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            @can('professions.modify')
            <x-input wire:model.live="search" placeholder="Search..." />
            <x-select wire:model.live="filtertire_id" placeholder="Select Tire..." :options="$tires" option-label="name" option-value="id" />
            <x-button wire:click="$set('modal', true)" class="btn btn-primary">Add Profession</x-button>
            @endcan
        </x-slot:menu>

        <x-table :headers="$headers" :rows="$professions">
            @can('professions.modify')
            @scope('actions', $profession)
            <div class="flex items-center space-x-2">
                <x-button icon="o-pencil" class="btn-sm btn-info btn-outline" 
                    wire:click="edit({{ $profession->id }})" spinner />
                <x-button icon="o-trash" class="btn-sm btn-outline btn-error" 
                    wire:click="delete({{ $profession->id }})" wire:confirm="Are you sure?" spinner />
                <x-button icon="o-document" class="btn-sm btn-outline btn-info" 
                    wire:click="getassignedDocuments({{ $profession->id }})" spinner />
                    <x-button icon="o-adjustments-horizontal" class="btn-sm btn-outline btn-success" 
                    wire:click="openconditionmodal({{ $profession->id }})" spinner />
                    <livewire:components.qualifications :profession_id="$profession->id" />
            </div>
            @endscope
            @endcan
            <x-slot:empty>
                <x-alert class="alert-error" title="No professions found." />
            </x-slot:empty>
        </x-table>
    </x-card>
    @else
    <x-alert class="alert-error mt-3" title="You do not have permission to view professions." />
    @endcan  
    
    <x-modal title="{{ $id ? 'Edit' : 'Add' }} Profession" wire:model="modal" box-class="max-w-3xl">
        <x-form wire:submit="save">
        <div class="grid grid-cols-2 gap-2">
        <x-input label="Name" wire:model="name" />
        <x-select label="Status" wire:model="status" placeholder="Select Status" :options="[['id'=>'active', 'name'=>'Active'], ['id'=>'inactive', 'name'=>'Inactive']]" option-label="name" option-value="id" />
        <x-input label="Required CDP" wire:model="requiredcdp" />
        <x-input label="Minimum CDP" wire:model="minimumcpd" />
        <x-input label="Prefix" wire:model="prefix" />
        <x-select wire:model="tire_id" label="Tire" placeholder="Select Tire..." :options="$tires" option-label="name" option-value="id" />
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
            <x-select  :options="$customertypes" option-label="name" placeholder="Select Customer Type..." option-value="id" wire:model="customertype_id" single />
            <x-select  :options="$documents" option-label="name" placeholder="Select Document..." option-value="id" wire:model="document_id" single/>
           
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
    <x-modal title="{{ $profession?->name }} Conditions" wire:model="conditionmodal" box-class="max-w-3xl">
        <x-form wire:submit="savecondition">
            <div class="grid grid-cols-3 gap-2">
            <x-select  :options="$customertypes" option-label="name" placeholder="Select Customer Type..." option-value="id" wire:model="customertype_id" single />
            <x-input placeholder="Condition" wire:model="condition" />         
            <x-button label="{{ $condition_id ? 'Update' : 'Add' }} Condition" icon="o-plus" type="submit" class=" btn-primary" spinner="savecondition" />

            </div>
        </x-form>

        @if (count($profession?->conditions??[]) > 0)
        <table class="table table-compact mt-2">
            <thead>
                <tr>
                    <th>Customer Type</th>
                    <th>Condition</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($profession?->conditions??[] as $condition)
                <tr>
                    <td>{{ $condition?->customertype?->name }}</td>
                    <td>{{ $condition?->condition }}</td>
                    <td class="flex justify-end items-center space-x-2">
                        <x-button icon="o-pencil" class="btn-sm btn-info btn-outline" 
                        wire:click="editcondition({{ $condition?->id }})" spinner />
                    <x-button icon="o-trash" class="btn-sm btn-outline btn-error" 
                        wire:click="deletecondition({{ $condition?->id }})" wire:confirm="Are you sure?" spinner />
            
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center p-2 bg-red-400 text-red">No conditions assigned</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @else
        <x-alert class="alert-error mt-2" title="No conditions assigned." />
        @endIf
    </x-modal>
</div>
