<div>
    <x-breadcrumbs :items="$breadcrumbs"    class="bg-base-300 p-3 rounded-box mt-2" />

    <x-header title="{{ $data['customerprofession']->customer->name }} {{ $data['customerprofession']->customer->surname }}" subtitle="{{ $data['customerprofession']->profession->name }} || {{ $data['customerprofession']->registertype->name }} || {{ $data['customerprofession']->customertype->name }}" class="mt-5 border-2 border-gray-200 rounded-lg p-5">
       
        <x-slot:actions>
            <livewire:admin.components.walletbalances :customer="$data['customerprofession']->customer" />
        </x-slot:actions>
    </x-header>
    <div class="text-2xl font-bold mt-5">1. My Qualification</div>

    <x-card title="Qualifications" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            <x-button icon="o-plus" label="Add Qualification" responsive class="btn btn-primary" wire:click="$set('qualificationmodal', true)" spinner />
        </x-slot:menu>
        <table class="table table-zebra">
            <thead>
                <tr>
                    <th>Institution</th>
                    <th>Qualification</th>
                    <th>Level</th>
                    <th>Start Year</th>
                    <th>End Year</th>
                    <th>Grade</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @if ($data['customerprofession']->studentqualifications)
                <tr>
                    <td>{{ $data['customerprofession']->studentqualifications->institution }}</td>
                    <td>{{ $data['customerprofession']->studentqualifications->qualification }}</td>
                    <td>{{ $data['customerprofession']->studentqualifications->qualificationlevel->name }}</td>
                    <td>{{ $data['customerprofession']->studentqualifications->startyear }}</td>
                    <td>{{ $data['customerprofession']->studentqualifications->endyear }}</td>
                    <td>{{ $data['customerprofession']->studentqualifications->grade }}</td>
                    <td>
                        <div class="flex items-center justify-end space-x-2">
                            <x-button icon="o-pencil" class="btn-sm btn-info btn-outline" 
                            wire:click="editqualification({{ $data['customerprofession']->studentqualifications->id }})" spinner />
                                      <x-button icon="o-trash"  class="btn-sm btn-error btn-outline" 
                            wire:click="deletequalification({{ $data['customerprofession']->studentqualifications->id }})" wire:confirm="Are you sure?" spinner />
                        </div>
                    </td>
                </tr>
                @else
                <tr>
                    <td colspan="8">
                        <div class="text-center text-red-500 p-5 font-bold bg-red-50">
                        No qualifications found for this customer profession.</div>
                        </td>
                </tr>
                @endif
            </tbody>
        </table>
    
    </x-card>
    <div class="text-2xl font-bold mt-5">2. Required Documents</div>

    @if ($data['customerprofession']->studentqualifications==null)
     <x-alert title="Qualifications" description="Please add qualifications to continue." icon="o-x-mark" class="alert-error mt-2"/>
    @else
    <x-card title="Required Documents" separator class="mt-5 border-2 border-gray-200">
        <table class="table table-zebra">
            <thead>
                <tr>
                    <th>Document</th>
                    <th>Upload</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data['uploaddocuments'] as $uploaddocument)
                <tr>
                    <td>{{ $uploaddocument['document_name'] }}</td>
                    <td>
                        @if ($uploaddocument['upload'])
                        <x-icon name="o-check" class="text-green-500" />
                        @else
                        <x-icon name="o-x-mark" class="text-red-500" />
                        @endif
                    </td>
                    <td class="flex justify-end items-center space-x-2">
                        @if ($uploaddocument['upload'])
                        <x-button wire:click="removeDocument({{ $uploaddocument['document_id'] }})" icon="o-trash" class="btn btn-xs btn-error" spinner wire:confirm="Are you sure?">Remove</x-button>
                   
                           @else
                        <x-button wire:click="openuploadmodal({{ $uploaddocument['document_id'] }})" icon="o-arrow-up-tray" class="btn btn-xs btn-primary">Upload</x-button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center p-2 bg-red-400 text-red">No documents found</td>
                </tr>
                @endforelse
            </tbody>
         </table>
        
    </x-card>
    @endif
     @php 
     $countpendingupload = collect($data['uploaddocuments'])->where('upload', false)->count();
     @endphp
     <div class="text-2xl font-bold mt-5">3. My Placements</div>
     @if ($countpendingupload > 0)
     <x-alert title="Documents" description="You have {{ $countpendingupload }} pending documents to upload. Please upload them to continue." icon="o-x-mark" class="alert-error mt-2"/>
     @else
    <x-card title="Placements" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            <x-button icon="o-plus" label="Add Placement" class="btn btn-primary" wire:click="$set('placementmodal', true)" spinner />
        </x-slot:menu>
        <table class="table table-zebra">
            <thead>
                <tr>
                    <th>Company</th>
                    <th>Position</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data['customerprofession']->placements??[] as $studentplacement)
                <tr>
                    <td>{{ $studentplacement->company }}</td>
                    <td>{{ $studentplacement->position }}</td>
                    <td>{{ $studentplacement->startdate }}</td>
                    <td>{{ $studentplacement->enddate }}</td>
                    <td>
                        <div class="flex items-center justify-end space-x-2">
                            <x-button icon="o-pencil" class="btn-sm btn-info btn-outline" 
                            wire:click="editplacement({{ $studentplacement->id }})" spinner />
                                      <x-button icon="o-trash"  class="btn-sm btn-error btn-outline" 
                            wire:click="deleteplacement({{ $studentplacement->id }})" wire:confirm="Are you sure?" spinner />
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11">
                        <div class="text-center text-red-500 p-5 font-bold bg-red-50">
                        No placements found for this customer profession.</div>
                        </td>
                </tr>
                @endforelse
            </tbody>
         </table> 
    </x-card>
    @endif

    <div class="text-2xl font-bold mt-5">4. My Registration and payments</div>

   @if(count($data['customerprofession']->placements??[])==0)
   <x-alert title="Placements" description="Please add placements to continue." icon="o-x-mark" class="alert-error mt-2"/>
   @else

    <x-card title="Registration" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            @if ($data['customerprofession']->registration==null)
            <x-button icon="o-plus" label="Generate Registration Invoice" class="btn btn-primary" wire:click="generateregistrationinvoice" spinner />
            @endif
        </x-slot:menu>
        <table class="table table-zebra">
            <thead>
                <tr>
                    <th>Year</th>
                    <th>Certificate number</th>
                    <th>Registration date</th>
                    <th>Approval Status</th>
                    <th>Payment Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @if ($data['customerprofession']->registration)
                <tr>
                    <td>{{ $data['customerprofession']->registration?->year }}</td>
                    <td>{{ $data['customerprofession']->registration?->certificatenumber??'N/A' }}</td>
                    <td>{{ $data['customerprofession']->registration?->registrationdate ??'N/A' }}</td>
                    <td><x-badge value="{{ $data['customerprofession']->registration?->status }}" class="{{ $data['customerprofession']->registration?->status!='APPROVED' ? 'badge-warning' : 'badge-success' }}" /></td>
                    <td>
                        @if ($data['invoice'])
                        <x-badge value="{{ $data['invoice']->amount }} {{ $data['invoice']->currency->name }}" class="{{ $data['invoice']->status=='PENDING' ? 'badge-warning' : 'badge-success' }}" />
                        @else
                        <x-badge value="Not Paid" class="badge-error" />
                        @endif
                    </td>
                    <td>
                        <div class="flex items-center justify-end space-x-2">
                            @if($data['customerprofession']->registration->status=='PENDING')
                             <livewire:admin.components.receipts :invoice="$data['invoice']" />
                             @else
                             @if($data['customerprofession']->registration->status=='APPROVED')
                             <x-button icon="o-arrow-down-tray" label="Download" class="btn-sm btn-success" spinner wire:click="downloadregistrationcertificate({{ $data['customerprofession']->registration->id }})" />
                             @endif
                            @endif
                         </div>
                    </td>
                </tr>
                @else
                <tr>
                    <td colspan="5">
                        <div class="text-center text-red-500 p-5 font-bold bg-red-50">
                        No registrations found for this customer profession.</div>
                        </td>
                </tr>
                @endif
            </tbody>
         </table>
    </x-card>
    @endif



    <x-modal wire:model="qualificationmodal" title=" {{ $id ? 'Edit' : 'New' }} Qualification" separator>
        <x-form wire:submit="savequalification">
            <div class="grid grid-cols-2 gap-2">
                <x-input label="Institution" wire:model="institution" />
                <x-input label="Qualification" wire:model="qualification" />
                <x-select label="Level" wire:model="qualificationlevel_id" :options="$qualificationlevels" option-label="name" option-value="id" placeholder="Select" />
                <x-input label="Start Year" wire:model="startyear" />
                <x-input label="End Year" wire:model="endyear" />
                <x-input label="Grade" wire:model="grade" />
            </div>
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.qualificationmodal = false" />
                <x-button label="Save" type="submit" class="btn-primary" spinner="savequalification" />
            </x-slot:actions>
        </x-form>
    </x-modal>

    <x-modal wire:model="uploadmodal" title="Upload Document" separator>
        <x-form wire:submit="uploadDocument">
            <div class="grid  gap-2">
                <x-input label="File" wire:model="file" type="file" />
                @if(auth()->user()->accounttype_id == 1)
                <x-checkbox label="Document verified" wire:model="verified" />
                @endif
            </div>
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.uploadmodal = false" />
                <x-button label="Upload" type="submit" class="btn-primary" spinner="uploadDocument" />
            </x-slot:actions>
        </x-form>
    </x-modal>
    <x-modal wire:model="placementmodal" title=" {{ $placement_id ? 'Edit' : 'New' }} Placement" separator>
        <x-form wire:submit="saveplacement">
            <div class="grid grid-cols-2 gap-2">
                <x-input label="Company" wire:model="company" />
                <x-input label="Position" wire:model="position" />
                <x-input  type="date" label="Start Date" wire:model="startdate" />
                <x-input type="date" label="End Date" wire:model="enddate" />
                <x-input label="Supervisor Name" wire:model="supervisorname" />
                <x-input label="Supervisor Phone" wire:model="supervisorphone" />
                <x-input label="Supervisor Email" wire:model="supervisoremail" />
                <x-input label="Supervisor Position" wire:model="supervisorposition" />
                <x-select label="Is Supervisor Registered" wire:model="is_supervisor_registered" :options="[['id'=>'YES','label'=>'Yes'], ['id'=>'NO','label'=>'No']]" option-label="label" option-value="id" placeholder="Select" />
                <x-input label="Reg Number" wire:model="regnumber" />
            </div>
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.placementmodal = false" />
                <x-button label="Save" type="submit" class="btn-primary" spinner="saveplacement" />
            </x-slot:actions>
        </x-form>
    </x-modal>
   
</div>
