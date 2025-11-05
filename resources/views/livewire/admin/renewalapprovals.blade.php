<div>
    <x-breadcrumbs :items="$breadcrumbs"    class="bg-base-300 p-3 rounded-box mt-2" />
    @can('applications.access')
    <x-card title="Renewal Approvals ({{ $customerapplications->count() }})" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            <x-input type="text" placeholder="Search" wire:model.live="search" />
            <x-select wire:model.live="applicationtype_id" placeholder="Select Application Type" :options="$applicationtypes" option-label="name" option-value="id" />
            <x-select wire:model.live="year" placeholder="Select Year" :options="$applicationsessions" option-label="year" option-value="year" />
            <x-select wire:model.live="status" placeholder="Select Status" :options="[['id'=>'AWAITING','name'=>'AWAITING'],['id'=>'APPROVED','name'=>'APPROVED'],['id'=>'REJECTED','name'=>'REJECTED']]" option-label="name" option-value="id" />
        
        </x-slot:menu>
        <x-hr/>
      
        <table class="table table-zebra">
            <thead>
                <tr>
                    <th>Practitioner</th>
                    <th>Profession</th>
                    <th>Reg Type</th>
                    <th>Customer type</th>
                    <th>Application type</th>
                    <th>Status</th>
                    <th>Time decay</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customerapplications as $customerapplication)
                <tr>
                    <td>{{ $customerapplication->customerprofession->customer->name }} {{ $customerapplication->customerprofession->customer->surname }}</td>
                    <td>{{ $customerapplication->customerprofession->profession->name }}</td>
                    <td>{{ $customerapplication->customerprofession->registertype->name }}</td>
                    <td>{{ $customerapplication->customerprofession->customertype->name }}</td>
                    <td>{{ $customerapplication->applicationtype->name }}</td>
                    <td>
                        <x-badge value="{{ $customerapplication->status }}" class="{{ $customerapplication->status=='PENDING' ? 'badge-warning' : 'badge-success' }}" />
                    </td>
                    <td>
                        {{ $customerapplication->updated_at->diffForHumans() }}
                    </td>
                    <td>
                        <div class="flex items-center justify-end space-x-2">
                          <x-button icon="o-eye" label="View" class="btn-sm btn-info btn-outline" wire:click="getapplication('{{ $customerapplication->uuid }}')"  spinner />
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="text-center text-red-500 p-5 font-bold bg-red-50">
                       No profession found.</div>
                        </td>
                </tr>
                @endforelse
            </tbody>
        </table>  
    </x-card>
    @else
    <x-alert class="alert-error mt-3" title="You do not have permission to view renewal approvals." />
    @endcan
    <x-modal wire:model="viewmodal" title="View Application" box-class="max-w-6xl h-screen" separator class="backdrop-blur">
        @if($application)
       <table class="table table-zebra">
        <tbody>
            <tr>
                <td>Practitioner</td>
                <td>{{ $application['customerapplication']->customerprofession->customer->name }} {{ $application['customerapplication']->customerprofession->customer->surname }}</td>
            </tr>
            <tr>
                <td>Profession</td>
                <td>{{ $application['customerapplication']->customerprofession->profession->name }}</td>
            </tr>
            <tr>
                <td>Reg Type</td>
                <td>{{ $application['customerapplication']->customerprofession->registertype->name }}</td>
            </tr>
            <tr>
                <td>Customer type</td>
                <td>{{ $application['customerapplication']->customerprofession->customertype->name }}</td>
            </tr>
            <tr>
                <td>Application type</td>
                <td>{{ $application['customerapplication']->applicationtype->name }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td>{{ $application['customerapplication']->status }}</td>
            </tr>
            <tr>
                <td>Time decay</td>
                <td>{{ $application['customerapplication']->updated_at->diffForHumans() }}</td>
            </tr>
        </tbody>
       </table>
       <x-card class="mt-5 border-2 border-gray-200">
       <x-tabs wire:model="selectedTab"     active-class="bg-primary rounded !text-white"
       label-class="font-semibold"
       label-div-class="bg-primary/5 rounded w-full p-2">
        <x-tab label="CDP points" name="cdppoints">
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
                    @forelse ($application['mycdps']??[] as $cdp)
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
        </x-tab>
        <x-tab label="Documents" name="documents" >

          <table class="table table-zebra">
            <thead>
                <tr>
                    <th>Document</th>
                    <th>Status</th>
                    <th></th>
                </tr>
          </thead>
          <tbody>
            @forelse ($application['customerapplication']->documents as $document)
            <tr>
                <td>{{ $document->document->name }}</td>
                <td>{{ $document->status }}</td>
                <td>
                    <div class="flex items-center justify-end space-x-2">
                        <x-button icon="o-eye"  class="btn-sm btn-info btn-outline" label="View" wire:click="viewdocument('{{ $document->file }}')" spinner />
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="2">
                    <div class="text-center text-red-500 p-5 font-bold bg-red-50">
                   No documents found.</div>
                    </td>
            </tr>
            @endforelse
          </tbody>
        </table>
        </x-tab>
        <x-tab label="Make decision" name="makedecision" >
         <x-form wire:submit="savedecision">
            <div class="grid  gap-2">
                <x-select placeholder="Status" wire:model="decisionstatus" :options="[['id'=>'APPROVED','name'=>'Approved'],['id'=>'REJECTED','name'=>'Rejected']]" />
                <x-textarea placeholder="Comment" wire:model="comment" />
                <x-button icon="o-plus" label="Save" class="btn-primary" spinner type="submit" />
            </div>
         </x-form>
        </x-tab>
       </x-tabs>
       </x-card>
       @endif
    </x-modal>
    <x-modal wire:model="documentview" title="View Document" box-class="max-w-4xl h-screen" separator class="backdrop-blur">
        <iframe src="{{$documenturl}}" class="w-full h-screen"></iframe>
    </x-modal>
</div>
