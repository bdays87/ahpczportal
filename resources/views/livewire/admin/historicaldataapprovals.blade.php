<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />
    
    <x-card title="Historical Data Approvals ({{ $historicalDataList->count() }})" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            <x-input type="text" placeholder="Search by name, surname, or ID" wire:model.live="search" />
            <x-select wire:model.live="status" :options="[['id'=>'PENDING','label'=>'Pending'], ['id'=>'APPROVED','label'=>'Approved'], ['id'=>'REJECTED','label'=>'Rejected']]" option-label="label" option-value="id" placeholder="Filter by Status" />
                <x-button icon="o-plus" label="New" class="btn-outline"  link="{{ route('admin.create-historical-data') }}" />
        </x-slot:menu>
      
        <table class="table table-zebra">
            <thead>
                <tr>
                    <th>Name</th>   
                    <th>Surname</th>
                    <th>National ID</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($historicalDataList as $data)
                <tr>
                    <td>{{ $data->name }}</td>
                    <td>{{ $data->surname }}</td>
                    <td>{{ $data->identificationnumber }}</td>
                    <td>
                        <x-badge value="{{ $data->status }}" class="{{ $data->status=='PENDING' ? 'badge-warning' : ($data->status=='APPROVED' ? 'badge-success' : 'badge-error') }}" />
                    </td>
                    <td>
                        {{ $data->created_at->diffForHumans() }}
                    </td>
                    <td>
                        <div class="flex items-center justify-end space-x-2">
                            @if($data->status == 'PENDING')
                                <x-button icon="o-eye" label="View" class="btn-sm btn-info btn-outline" wire:click="viewHistoricalData({{ $data->id }})" />
                            @else
                                <x-button icon="o-eye" label="View" class="btn-sm btn-info btn-outline" wire:click="viewHistoricalData({{ $data->id }})" />
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="text-center text-red-500 p-5 font-bold bg-red-50">
                            No historical data submissions found.
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </x-card>

    {{-- View Modal --}}
    <x-modal title="Historical Data Details" wire:model="viewmodal" box-class="max-w-full w-full h-[90vh]" persistent separator>
        @if($historicalData)
            <div class="space-y-4 overflow-y-auto max-h-[calc(90vh-150px)]">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="font-semibold">Name:</p>
                        <p>{{ $historicalData->name }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Surname:</p>
                        <p>{{ $historicalData->surname }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Gender:</p>
                        <p>{{ $historicalData->gender }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">National ID:</p>
                        <p>{{ $historicalData->identificationnumber }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Date of Birth:</p>
                        <p>{{ $historicalData->dob }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Identity Type:</p>
                        <p>{{ $historicalData->identificationtype }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Nationality:</p>
                        <p>{{ $historicalData->nationality->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Address:</p>
                        <p>{{ $historicalData->address }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Place of Birth:</p>
                        <p>{{ $historicalData->placeofbirth }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Phone:</p>
                        <p>{{ $historicalData->phone }}</p>
                    </div>
                </div>

                <div class="border-t pt-4">
                    <p class="font-semibold mb-4">Professions:</p>
                    @foreach($historicalData->professions as $profession)
                        <div class="border p-4 rounded-lg mb-4 space-y-2">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="font-semibold">Profession:</p>
                                    <p>{{ $profession->profession->name ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="font-semibold">Registration Number:</p>
                                    <p>{{ $profession->registrationnumber }}</p>
                                </div>
                                <div>
                                    <p class="font-semibold">Registration Year:</p>
                                    <p>{{ $profession->registrationyear }}</p>
                                </div>
                                <div>
                                    <p class="font-semibold">Practising Certificate Number:</p>
                                    <p>{{ $profession->practisingcertificatenumber }}</p>
                                </div>
                                <div>
                                    <p class="font-semibold">Register Type:</p>
                                    <p>{{ $profession->registertype->name ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="font-semibold">Tier:</p>
                                    <p>{{ $profession->tire->name ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="font-semibold">Last Renewal Year:</p>
                                    <p>{{ $profession->last_renewal_year ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="font-semibold">Last Renewal Expire Date:</p>
                                    <p>{{ $profession->last_renewal_expire_date ? $profession->last_renewal_expire_date->format('Y-m-d') : 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="font-semibold">Total CDP Points on Last Renewal Year:</p>
                                    <p>{{ $profession->last_renewal_year_cdp_points ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="border-t pt-2 mt-2">
                                <p class="font-semibold mb-2">Attached Certificates:</p>
                                <div class="space-y-2">
                                    @foreach($profession->documents as $doc)
                                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                            <span>{{ $doc->description ?? 'Certificate' }}</span>
                                            <x-button icon="o-eye" label="View Document" class="btn-sm btn-info btn-outline" wire:click="viewAttachment('{{ addslashes($doc->file) }}')" />
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($historicalData->status == 'REJECTED' && $historicalData->rejection_reason)
                    <div class="border-t pt-4">
                        <p class="font-semibold text-red-600">Rejection Reason:</p>
                        <p class="text-red-600">{{ $historicalData->rejection_reason }}</p>
                    </div>
                @endif

                <x-slot:actions>
                    @if($historicalData->status == 'PENDING')
                        <x-button label="Approve" wire:click="approveHistoricalData({{ $historicalData->id }})" class="btn-success" spinner="approveHistoricalData" />
                        <x-button label="Reject" wire:click="openRejectModal({{ $historicalData->id }})" class="btn-error" />
                    @endif
                    <x-button label="Close" wire:click="$set('viewmodal', false)" class="btn-secondary" />
                </x-slot:actions>
            </div>
        @endif
    </x-modal>

    {{-- Reject Modal --}}
    <x-modal title="Reject Historical Data Submission" wire:model="rejectmodal" box-class="max-w-2xl" persistent separator>
        <x-form wire:submit="rejectHistoricalData">
            <div class="space-y-4">
                <p class="text-gray-600">Please provide a reason for rejecting this submission:</p>
                <x-textarea wire:model="rejectionReason" label="Rejection Reason" placeholder="Enter reason for rejection..." rows="5" required />
                <x-slot:actions>
                    <x-button label="Submit Rejection" type="submit" class="btn-error" spinner="rejectHistoricalData" />
                    <x-button label="Cancel" wire:click="$set('rejectmodal', false)" class="btn-secondary" />
                </x-slot:actions>
            </div>
        </x-form>
    </x-modal>
    <x-modal title="View Document" wire:model="viewattachmentmodal" box-class="max-w-full w-full h-[95vh]" persistent separator>
        @if($attachment)
            <div class="w-full h-[calc(95vh-150px)] flex items-center justify-center">
                <iframe src="{{ $attachment }}" width="100%" height="100%" class="border-0 rounded-lg" frameborder="0"></iframe>
            </div>
        @else
            <div class="text-center p-8">
                <p class="text-gray-500">No document URL available</p>
            </div>
        @endif
        <x-slot:actions>
            <x-button label="Close" wire:click="closeAttachmentModal" class="btn-secondary" />
        </x-slot:actions>
    </x-modal>
</div>
