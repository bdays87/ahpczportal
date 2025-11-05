<div>
   <x-breadcrumbs :items="$breadcrumbs"    class="bg-base-300 p-3 rounded-box mt-2" />

   @can('otherservices.access')
   <x-card title="Other Services" class="mt-5 border-2 border-gray-200" separator>
       <x-slot:menu>
           @can('otherservices.modify')
           <x-button label="New" responsive icon="o-plus" class="btn-outline" @click="$wire.modal = true" />
           @endcan
       </x-slot:menu>
       <x-table :headers="$headers" :rows="$otherservices">
          
           @scope('actions', $otherservice)
           @can('otherservices.modify')
           <div class="flex items-center space-x-2">
               <x-button icon="o-pencil" class="btn-sm btn-info btn-outline" 
                   wire:click="edit({{ $otherservice->id }})" spinner />
               <x-button icon="o-trash" class="btn-sm btn-outline btn-error" 
                   wire:click="delete({{ $otherservice->id }})" wire:confirm="Are you sure?" spinner />
               <x-button icon="o-document" class="btn-sm btn-outline btn-info" 
                   wire:click="getotherservice({{ $otherservice->id }})" spinner />
           </div>
           @endcan
           @endscope
           <x-slot:empty>
               <x-alert class="alert-error" title="No other services found." />
           </x-slot:empty>
       </x-table>
   </x-card>
   @else
   <x-alert class="alert-error mt-3" title="You do not have permission to view other services." />
   @endcan
   <x-modal title="{{ $id ? 'Edit' : 'New' }} Other Service" wire:model="modal">
       <x-form wire:submit.prevent="save">
           <div class="grid grid-cols-2 gap-2">
               <x-select label="Currency" wire:model="currency_id" :options="$currencies" option-label="name" option-value="id" placeholder="Select" />
               <x-input type="text" label="Name" wire:model="name" />
               <x-input type="number" label="Amount" wire:model="amount" />
               <x-input type="text" label="General ledger" wire:model="generalledger" />
               <x-select  label="Generate certificate" wire:model="generatecertificate" :options="[['id'=>'YES','label'=>'Yes'], ['id'=>'NO','label'=>'No']]" option-label="label" option-value="id" placeholder="Select" />
               <x-select label="Expire type" wire:model="expiretype" :options="[['id'=>'MONTHLY','label'=>'Monthly'], ['id'=>'QUARTERLY','label'=>'Quarterly'], ['id'=>'ANNUAL','label'=>'Annual'], ['id'=>'LIFETIME','label'=>'Lifetime']]" option-label="label" option-value="id" placeholder="Select" />
               <x-select label="Require approval" wire:model="requireapproval" :options="[['id'=>'YES','label'=>'Yes'], ['id'=>'NO','label'=>'No']]" option-label="label" option-value="id" placeholder="Select" />
               
           </div>
           <x-slot:actions>
               <x-button label="Cancel" @click="$wire.modal = false" />
               <x-button label="{{ $id ? 'Update' : 'Save' }}" type="submit" class="btn-primary" spinner="save" />
           </x-slot:actions>
       </x-form>
   </x-modal>

   <x-modal title="Documents" wire:model="documentmodal">
    <x-form wire:submit.prevent="assignDocument">
    <x-select  wire:model.live="document_id" placeholder="Select Document" :options="$documents" option-label="name" option-value="id" single>
        
        <x-slot:append>
            {{-- Add `join-item` to all appended elements --}}
            <x-button label="Assign"  type="submit" icon="o-plus" class="join-item btn-primary" spinner="assignDocument" />
        </x-slot:append>
    </x-select>
    </x-form>
    <table class="table table-xs table-zebra">
        <thead>
            <tr>
                <th>Document</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($otherservice->documents??[] as $document)
            <tr>
                <td>{{ $document->document->name }}</td>
                <td class="flex justify-end items-center space-x-2">
                    <x-button icon="o-trash" class="btn-sm btn-outline btn-error" 
                        wire:click="unassignDocument({{ $document->document_id }})" spinner />
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="2" class="text-center p-2 bg-red-400 text-red">No documents assigned</td>
            </tr>
            @endforelse
        </tbody>
    </table>
   </x-modal>
</div>
