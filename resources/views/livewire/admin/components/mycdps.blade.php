<div>
  
    <x-card  title="My CDPs" separator class="border-2 border-gray-200">
        <x-slot:menu>
            <x-input wire:model.live="year" placeholder="Select Year" />
            <x-select wire:model.live="customerprofession_id" placeholder="Select Profession" :options="$customerprofessions" option-label="name" option-value="id" />
           @if($customerprofession_id)
            <x-button icon="o-plus" label="Add CDP" class="btn-sm btn-primary" wire:click="$set('addmodal', true)" />
            @endif
        </x-slot:menu>
   
 <x-hr/>
        <table class="table table-compact">
            <thead>
                <tr>
                    <th>Year</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Duration</th>
                    <th>Points</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody> 
                @forelse ($cdps??[] as $cdp)
                <tr>
                    <td>{{ $cdp->year }}</td>
                    <td>{{ $cdp->title }}</td>
                    <td>{{ $cdp->type }}</td>
                    <td>{{ $cdp->duration }} {{ $cdp->durationunit }}</td>
                    <td>{{ $cdp->points }}</td>
                    <td>{{ $cdp->status }}</td>
                    <td>
                      
                    </td>
                    <td>
                        <div class="flex items-center justify-end space-x-2">
                            <x-button icon="o-eye"  class="btn-sm btn-info btn-outline" 
                            wire:click="openattachmentmodal({{ $cdp->id }})" />
                            @if($cdp->status == "PENDING")
                            <x-button icon="o-pencil"  class="btn-sm btn-info btn-outline" 
                            wire:click="edit({{ $cdp->id }})" />
                            <x-button icon="o-trash"  class="btn-sm btn-error btn-outline" 
                            wire:click="delete({{ $cdp->id }})" wire:confirm="Are you sure?" spinner />
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="text-center text-red-500 p-5 font-bold bg-red-50">
                       No CDP found.</div>
                        </td>
                </tr>
                @endforelse
            </tbody>
        </table>
       
    </x-modal>
    <x-modal wire:model="addmodal" title="{{ $id ? 'Edit' : 'Add' }} CDP" box-class="max-w-4xl">
        <x-form wire:submit.prevent="save">
            <div class="grid grid-cols-2 gap-2">
                <x-input placeholder="Title" label="Title" wire:model="title" />
                
                <x-select placeholder="Type" label="Type" wire:model="type" :options="[['id'=>'PHYSICAL','label'=>'Physical'], ['id'=>'VIRTUAL','label'=>'Virtual']]" option-label="label" option-value="id" />
                <x-input placeholder="Duration" label="Duration" wire:model="duration" />
                <x-select placeholder="Duration Unit" label="Duration Unit" wire:model="durationunit" :options="[['id'=>'HOURS','label'=>'Hours'], ['id'=>'DAYS','label'=>'Days'], ['id'=>'WEEKS','label'=>'Weeks'], ['id'=>'MONTHS','label'=>'Months'], ['id'=>'YEARS','label'=>'Years']]" option-label="label" option-value="id" />
            </div>
            <div class="grid gap-2">
                <x-textarea placeholder="Description" label="Description" wire:model="description" />
            </div>
            <x-slot:actions>
                <x-button icon="o-x-mark" label="Cancel" class="btn-sm btn-outline btn-error" wire:click="$set('addmodal', false)" />
         
            <x-button type="submit" icon="o-plus" label="{{ $id ? 'Update' : 'Add' }}" class="btn-sm btn-primary" spinner />
            </x-slot:actions>
        </x-form>
    </x-modal>

    <x-modal wire:model="attachmentmodal" title="Attachments" box-class="max-w-4xl">
        <table class="table table-sm">
            <tbody>
                <tr>
                    <th>Title</th>
                    <td>{{ $mycdp?->title }}</td>
                </tr>
                <tr>
                    <th>Type</th>
                    <td>{{ $mycdp?->type }}</td>
                </tr>
                <tr>
                    <th>Description</th>
                    <td>{{ $mycdp?->description }}</td>
                </tr>
                <tr>
                    <th>Duration</th>
                    <td>{{ $mycdp?->duration }} {{ $mycdp?->durationunit }}</td>
                </tr>
                <tr>
                    <th>Points</th>
                    <td>{{ $mycdp?->points }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td class="flex justify-between">{{ $mycdp?->status }} @if($mycdp?->status == "PENDING") <x-button icon="o-check" label="Submit" class="btn-sm btn-primary" wire:click="submitforassessment({{ $mycdp?->id }})" spinner /> @endif</td>
                </tr>
            </tbody>
        </table>
        <x-card title="Attachments" class="mt-5 border-2 border-gray-200" separator>
            @if($mycdp?->status == "PENDING")
        <x-form wire:submit.prevent="saveattachment">
            <div class="grid grid-cols-3 gap-2">
            <x-select placeholder="Type"  wire:model="type" :options="[['id'=>'PROGRAMME','label'=>'Programme'], ['id'=>'REGISTER','label'=>'Register'],['id'=>'CERTIFICATE','label'=>'Certificate']]" option-label="label" option-value="id" />
           <x-input placeholder="File"  wire:model="file" type="file" />
           <x-button icon="o-plus" label="Add Attachment" class=" btn-primary" spinner type="submit" />
           </div>
        </x-form>
        @endif
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>
                                           </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($mycdp?->attachments??[] as $attachment)
                <tr>
                    <td>{{ $attachment->type }}</td>
                    <td class="flex items-center justify-end space-x-2">    
                        @if($mycdp?->status == "PENDING")
                        <x-button icon="o-trash"  class="btn-sm btn-error btn-outline" 
                        wire:click="deleteattachment({{ $attachment->id }})" spinner wire:confirm="Are you sure?"/>
                        @endif
                        <x-button icon="o-eye"  class="btn-sm btn-success btn-outline" 
                        wire:click="openadocmodal({{ $attachment->id }})" spinner />
           
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="2">
                        <div class="text-center text-red-500 p-5 font-bold bg-red-50">
                       No attachments found.</div>
                        </td>
                </tr>
                @endforelse
            </tbody>
          </table>
        </x-card>
    </x-modal>




</div>
