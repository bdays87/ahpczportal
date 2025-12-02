<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />
    <x-card title="Personal details" separator class="mt-5 border-2 border-gray-200">
        <livewire:admin.components.customerprofile :customer="$customerprofession['customer']" />
    </x-card>

    <x-card title="Professional details" separator class="mt-5 border-2 border-gray-200">
       <table class="table table-zebra">
        <thead>
            <tr>
                <th>Profession</th>
                <th>Tire</th>
                <th>Register type</th>
                <th>Customer type</th>
                <th>Status</th>
                <th>Updated at</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $customerprofession?->profession?->name }}</td>
                <td>{{ $customerprofession?->profession?->tire?->name }}</td>
                <td>{{ $customerprofession?->registertype?->name }}</td>
                <td>{{ $customerprofession?->customertype?->name }}</td>
                <td>{{ $customerprofession?->status }}</td>
                <td>{{ $customerprofession?->updated_at->diffForHumans() }}</td>
                <td class="flex justify-end">
                    @if($customerprofession->status == "AWAITING_REG")
                    @can("registrations.approve")
                    <x-button icon="o-check" label="Capture" class="btn-sm btn-success" wire:click="opencommentmodal({{ $customerprofession->id }})" spinner />
                    @else
                    <x-button icon="o-check" label="Capture" class="btn-sm btn-success" disabled />
                    @endcan
                    @endif
                </td>
              
            </tr>
        </tbody>
       </table>
    </x-card>

    <x-card title="Qualifications" separator class="mt-5 border-2 border-gray-200">
        <table class="table table-zebra">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Level</th>
                    <th>Institution</th>
                    <th>Year</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customerprofession->qualifications as $qualification)
                <tr>
                    <td>{{ $qualification->name }}</td>
                    <td>{{ $qualification->qualificationcategory->name }}</td>
                    <td>{{ $qualification->qualificationlevel->name }}</td>
                    <td>{{ $qualification->institution }}</td>
                    <td>{{ $qualification->year }}</td>
                    <td class="flex justify-end">
                        <x-button icon="o-document-magnifying-glass" label="View" class="btn-sm btn-info btn-outline" wire:click="viewqualification({{ $qualification->id }})" spinner />
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">No qualifications found</td>
                </tr>
            </tbody>
            @endforelse
        </table>
    </x-card>

    <x-card title="Uploaded documents" separator class="mt-5 border-2 border-gray-200">
        <table class="table table-zebra">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customerprofession->documents as $document)
                <tr>
                    <td>{{ $document->document->name }}</td>
                    <td>{{ $document->status }}</td>
                    <td class="flex justify-end">
                        <x-button icon="o-document-magnifying-glass" label="View" class="btn-sm btn-info btn-outline" wire:click="viewdocument({{ $document->id }})" spinner />
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">No documents found</td>
                </tr>
            </tbody>
            @endforelse
        </table>
    </x-card>

    <x-card title="Employment details" separator class="mt-5 border-2 border-gray-200">
        <table class="table table-zebra">
            <thead>
                <tr>
                    <th>Company name</th>
                    <th>Position</th>
                    <th>Start date</th>
                    <th>End date</th>
                    <th>Address</th>
                    <th>Phone</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customerprofession->customer->employmentdetails as $employmentdetail)
                <tr>
                    <td>{{ $employmentdetail->companyname }}</td>
                    <td>{{ $employmentdetail->position }}</td>
                    <td>{{ $employmentdetail->start_date }}</td>
                    <td>{{ $employmentdetail->end_date }}</td>
                    <td>{{ $employmentdetail->address }}</td>
                    <td>{{ $employmentdetail->phone }}</td>
                    <td>{{ $employmentdetail->email }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">No employment details found</td>
                </tr>
            </tbody>
            @endforelse
        </table>
    </x-card>

 <x-modal title="Document view" wire:model="documentview" box-class="max-w-6xl h-screen">
   <iframe src="{{$documenturl}}" class="w-full h-screen"></iframe>
 </x-modal>

<x-modal title="Decision" wire:model="decisionmodal">
    <x-form wire:submit="savecomment">
        <div class="grid ggap-4">          
           @if(!$customerprofession->registertype_id)
  
      <x-select label="Register type" wire:model="registertype_id" placeholder="Select register type" :options="$registertypes" option-label="name" option-value="id" />
           @endif
            <x-select label="Status" wire:model="status" placeholder="Select status" :options="[['id'=>'APPROVED','name'=>'Approved'],['id'=>'REJECTED','name'=>'Rejected']]" />
            <x-textarea label="Comment" wire:model="comment" />
        </div>
       <x-slot:actions>
            <x-button icon="o-x-mark" label="Cancel" type="button" class="btn-sm btn-error" wire:click="closemodal" />
            <x-button icon="o-check" label="Save" type="submit" class="btn-sm btn-success"  spinner />
        </x-slot:actions>
    </x-form>
</x-modal>

</div>