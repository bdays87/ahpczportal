<div>
    <x-header title="Professional Qualifications" subtitle="Manage practitioner professions and certifications" class="mt-5" separator>
        <x-slot:actions>
            <x-button icon="o-plus" label="Add Profession" class="btn-primary" wire:click="$set('addmodal', true)" spinner />
        </x-slot:actions>
    </x-header>

    @if ($customer && $customer->customerprofessions && $customer->customerprofessions->count() > 0)
        <div class="mt-6 space-y-4">
            @forelse ($customer->customerprofessions as $customerprofession)
                <div class="border border-gray-200 rounded-lg p-6 bg-white">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ $customerprofession?->profession?->name }}
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">
                                <span class="font-medium">Last Application:</span> 
                                {{ $customerprofession?->applications?->last()?->year ?? 'N/A' }}
                            </p>
                        </div>
                        <div>
                            @if (
                                $customerprofession?->applications?->count() > 0 &&
                                    $customerprofession?->applications?->last()?->status == 'APPROVED')
                                @if ($customerprofession->isCompliant())
                                    <x-badge value="Compliant" class="badge-success" />
                                @else
                                    <x-badge value="Non-Compliant" class="badge-error" />
                                @endif
                            @else
                                <x-badge value="{{ $customerprofession?->applications?->last()?->status ?? 'N/A' }}" class="badge-neutral" />
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                        <div class="border border-gray-200 rounded p-3 bg-gray-50">
                            <p class="text-xs text-gray-600 uppercase font-medium mb-1">Register Type</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $customerprofession->applications?->last()->registertype?->name ?? 'N/A' }}</p>
                        </div>

                        <div class="border border-gray-200 rounded p-3 bg-gray-50">
                            <p class="text-xs text-gray-600 uppercase font-medium mb-1">Customer Type</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $customerprofession?->customertype?->name ?? 'N/A' }}</p>
                        </div>

                        <div class="border border-gray-200 rounded p-3 bg-gray-50">
                            <p class="text-xs text-gray-600 uppercase font-medium mb-1">Application Type</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $customerprofession?->applications?->last()?->applicationtype?->name ?? 'N/A' }}</p>
                        </div>

                        <div class="border border-gray-200 rounded p-3 bg-gray-50">
                            <p class="text-xs text-gray-600 uppercase font-medium mb-1">Application Status</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $customerprofession?->applications?->last()?->status ?? 'N/A' }}</p>
                        </div>
                    </div>

                    @if($customerprofession->applications?->last()?->status == 'APPROVED')
                        <div class="border border-gray-200 rounded p-3 bg-gray-50 mb-4">
                            <p class="text-xs text-gray-600 uppercase font-medium mb-2">CDP Status</p>
                            <div class="flex flex-wrap items-center gap-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-gray-700 font-medium">Current CDP Points:</span>
                                    <x-badge value="{{ $customerprofession?->totalcdpoints($customerprofession?->applications?->last()?->year) }}" class="{{ $customerprofession?->totalcdpoints($customerprofession?->applications?->last()?->year) >= $customerprofession?->profession?->tires?->where('tire_id',$customerprofession?->tire_id)?->first()?->minimum_cdp ?? 0 ? 'badge-success' : 'badge-error' }}" />
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-gray-700 font-medium">Minimum Required:</span>
                                    <x-badge value="{{ $customerprofession?->profession?->tires?->where('tire_id',$customerprofession?->tire_id)?->first()?->minimum_cdp ?? 0 }}" class="badge-success" />
                                </div>
                                <livewire:admin.components.mycdps :customerprofession="$customerprofession" />
                                <x-button icon="o-arrow-right-circle" label="View online activities" link="{{ route('customer.activities') }}" class="btn-sm btn-outline"/>
                            </div>
                        </div>
                    @endif

                    <div class="flex flex-wrap items-center gap-2 pt-4 border-t border-gray-200">
                        @if (strtolower($customerprofession?->applications?->last()?->status) == 'pending' || $customerprofession->status != 'APPROVED')
                            @if ($customerprofession?->customertype?->name == 'Student')
                                @if($customerprofession?->uuid)
                                    <x-button icon="o-eye" label="View Details" class="btn-sm btn-outline"
                                        link="{{ route('customer.student.show', $customerprofession->uuid) }}"
                                        spinner />
                                @else
                                    <x-button icon="o-eye" label="View Details" class="btn-sm btn-outline" disabled />
                                @endif
                            @else
                                @if($customerprofession?->uuid)
                                    <x-button icon="o-arrow-right-circle" label="Continue Setup" class="btn-sm btn-outline"
                                        link="{{ route('customer.profession.show', $customerprofession->uuid) }}"
                                        spinner />
                                @else
                                    <x-button icon="o-arrow-right-circle" label="Continue Setup" class="btn-sm btn-outline" disabled />
                                @endif
                            @endif
                            <x-button icon="o-trash" label="Delete" class="btn-sm btn-outline btn-error"
                                wire:click="delete({{ $customerprofession->id }})" wire:confirm="Are you sure you want to delete this profession?"
                                spinner />
                        @elseif (strtolower($customerprofession->applications?->last()?->status) == 'approved' || strtolower($customerprofession->applications?->last()?->status) == 'active')
                            @if ($customerprofession->customertype->name == 'Student')
                                @if($customerprofession->uuid)
                                    <x-button icon="o-eye" label="View Details" class="btn-sm btn-outline"
                                        link="{{ route('customer.student.show', $customerprofession->uuid) }}"
                                        spinner />
                                @else
                                    <x-button icon="o-eye" label="View Details" class="btn-sm btn-outline" disabled />
                                @endif
                            @else
                                @if ($customerprofession->applications->count() > 0)
                                    @if (
                                        $customerprofession->applications->last()->status == 'APPROVED' &&
                                            $customerprofession->applications->last()->isExpired())
                                        @if( $customerprofession?->totalcdpoints() >= $customerprofession?->profession?->tires?->where('tire_id',$customerprofession?->tire_id)?->first()?->minimum_cdp ?? 0)
                                            <x-button icon="o-arrow-path" label="Renew Certificate" class="btn-sm btn-outline"
                                                wire:click="renew({{ $customerprofession->id }})" spinner />
                                        @else
                                            <x-alert title="CDP Points Insufficient" description="You have insufficient CDP points to renew your certificate" icon="o-exclamation-triangle" class="alert-error">
                                                <x-slot:actions>
                                                    <x-button icon="o-arrow-right-circle" label="View online activities" link="{{ route('customer.activities') }}" class="btn-sm btn-outline"/>
                                                    <livewire:admin.components.mycdps :customerprofession="$customerprofession" />
                                                </x-slot:actions>
                                            </x-alert>
                                        @endif
                                    @else
                                        @if (
                                            $customerprofession->applications->last()->status == 'APPROVED' &&
                                                !$customerprofession->applications->last()->status == 'PENDING')
                                            <x-button icon="o-arrow-right-circle" label="Proceed with Renewal"
                                                class="btn-sm btn-outline"
                                                link="{{ route('customers.application.renewal', $customerprofession->applications->last()->uuid) }}"
                                                spinner />
                                        @endif
                                    @endif
                                @endif
                                @if (
                                    $customerprofession->applications->last()->status == 'APPROVED' &&
                                        !$customerprofession->applications->last()->isExpired())
                                    <x-button icon="o-arrow-down-tray" label="Download Certificates" class="btn-sm btn-outline"
                                        wire:click="viewapplication({{ $customerprofession->id }})" spinner />
                                @endif
                            @endif
                        @endif
                    </div>
                </div>
            @empty
                <div class="border border-gray-200 rounded-lg p-12 text-center bg-white">
                    <x-icon name="o-academic-cap" class="w-16 h-16 mx-auto text-gray-400 mb-4" />
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No Professions Found</h3>
                    <p class="text-gray-600 mb-6">This practitioner hasn't added any professional qualifications yet.</p>
                    <x-button 
                        icon="o-plus" 
                        label="Add First Profession" 
                        class="btn-outline" 
                        wire:click="$set('addmodal', true)"
                        spinner 
                    />
                </div>
            @endforelse
        </div>
    @elseif(!$customer)
        <div class="mt-6 border border-gray-200 rounded-lg p-12 text-center bg-white">
            <x-icon name="o-exclamation-triangle" class="w-16 h-16 mx-auto text-gray-400 mb-4" />
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Customer Not Found</h3>
            <p class="text-gray-600">Unable to load customer information. Please try again later.</p>
        </div>
    @else
        <div class="mt-6 border border-gray-200 rounded-lg p-12 text-center bg-white">
            <x-icon name="o-academic-cap" class="w-16 h-16 mx-auto text-gray-400 mb-4" />
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No Professions Found</h3>
            <p class="text-gray-600 mb-6">Get started by adding your first professional qualification.</p>
            <x-button 
                icon="o-plus" 
                label="Add First Profession" 
                class="btn-outline" 
                wire:click="$set('addmodal', true)"
                spinner 
            />
        </div>
    @endif


    {{-- Add Profession Modal --}}
    <x-modal wire:model="addmodal" box-class="max-w-4xl" persistent title="Add New Profession">
        @if ($errormessage)
            <x-alert class="alert-error mb-4" icon="o-exclamation-triangle" title="Error" :description="$errormessage" />
        @endif

        <x-form wire:submit.prevent="addprofession">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <x-select 
                    label="Profession" 
                    wire:model="profession_id"
                    placeholder="Select Profession" 
                    :options="$professions" 
                    option-label="name" 
                    option-value="id"
                    icon="o-academic-cap"
                />
                
                <x-select 
                    label="Customer Type" 
                    wire:model.live="customertype_id"
                    placeholder="Select Customer Type" 
                    :options="$customertypes" 
                    option-label="name" 
                    option-value="id"
                    icon="o-user-circle"
                />
                
                <x-select 
                    label="Employment Status" 
                    wire:model.live="employmentstatus_id"
                    placeholder="Select Employment Status" 
                    :options="$employmentstatuses" 
                    option-label="name" 
                    option-value="id"
                    icon="o-briefcase"
                />
                
                @if($employmentstatus_id==1)
                    <x-select 
                        label="Employment Location" 
                        wire:model="employmentlocation_id" 
                        placeholder="Select Employment Location" 
                        :options="$employmentlocations"
                        option-label="name" 
                        option-value="id"
                        icon="o-map-pin"
                    />
                    <x-select 
                        label="Employment Sector" 
                        wire:model="employmentsector_id" 
                        placeholder="Select Employment Sector" 
                        :options="$employmentssectors"
                        option-label="name" 
                        option-value="id"
                        icon="o-map-pin"
                    />
                @endif
            </div>

            <x-slot:actions>
                <x-button 
                    label="Cancel" 
                    class="btn-outline" 
                    wire:click="$set('addmodal', false)" 
                />
                <x-button 
                    label="Save Profession" 
                    icon="o-check" 
                    class="btn-primary" 
                    type="submit" 
                    spinner="addprofession" 
                />
            </x-slot:actions>
        </x-form>
    </x-modal>

    {{-- Certificates Modal --}}
    <x-modal wire:model="openmodal" box-class="max-w-4xl" persistent title="Professional Certificates">
        {{-- Registration Certificate --}}
        <div class="border border-gray-200 rounded-lg p-6 mb-5 bg-gray-50">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">Registration Certificate</h4>
            <div class="bg-white rounded-lg p-4 flex items-center justify-between border border-gray-200">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Certificate Number</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $customerprofession?->registration?->certificatenumber ?? 'N/A' }}</p>
                    <p class="text-sm text-gray-600 mt-2">
                        <span class="font-medium">Registration Date:</span> 
                        {{ $customerprofession?->registration?->registrationdate ?? 'N/A' }}
                    </p>
                </div>
                <x-button 
                    icon="o-arrow-down-tray" 
                    label="Download"
                    class="btn-outline" 
                    spinner
                    wire:click="downloadregistrationcertificate({{ $customerprofession?->registration?->id }})" 
                />
            </div>
        </div>

        {{-- Application Certificates --}}
        <div class="border border-gray-200 rounded-lg p-6 bg-gray-50">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">Application Certificates</h4>
            <div class="space-y-3">
                @forelse (($customerprofession->applications ?? collect())->filter(fn($app) => !$app->isExpired()) as $application)
                    <div class="bg-white rounded-lg p-4 border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <p class="text-sm text-gray-600">
                                        <span class="font-medium">Year:</span> {{ $application->year }}
                                    </p>
                                    @if ($application->status == 'APPROVED')
                                        @if ($application->isExpired())
                                            <x-badge value="EXPIRED" class="badge-error" />
                                        @else
                                            <x-badge value="VALID" class="badge-success" />
                                        @endif
                                    @else
                                        <x-badge value="{{ $application->status }}" class="badge-neutral" />
                                    @endif
                                </div>
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Certificate #:</span> {{ $application->certificate_number ?? 'N/A' }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Expiry Date:</span> {{ $application->certificate_expiry_date ?? 'N/A' }}
                                </p>
                            </div>
                            @if (!$application->isExpired() && $application->status == 'APPROVED')
                                <x-button 
                                    icon="o-arrow-down-tray" 
                                    label="Download" 
                                    class="btn-sm btn-outline" 
                                    spinner
                                    wire:click="downloadpractisingcertificate({{ $application->id }})" 
                                />
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-lg p-8 text-center border-2 border-dashed border-gray-300">
                        <x-icon name="o-document" class="w-12 h-12 mx-auto text-gray-400 mb-2" />
                        <p class="text-gray-600 font-medium">No application certificates found</p>
                    </div>
                @endforelse
            </div>
        </div>

        <x-slot:actions>
            <x-button 
                label="Close" 
                class="btn-outline" 
                wire:click="$set('openmodal', false)" 
            />
        </x-slot:actions>
    </x-modal>
    {{-- Renewal Modal --}}
    <x-modal wire:model="renewmodal" box-class="max-w-3xl" persistent title="Certificate Renewal">
        @if ($message)
            <x-alert class="alert-error mb-4" icon="o-exclamation-triangle" title="Error" :description="$message" />
        @endif

        <x-form wire:submit.prevent="proceedwithrenewal">
            <div class="space-y-5">
                <x-select 
                    label="Select Renewal Period" 
                    wire:model="session_id" 
                    placeholder="Choose renewal period"
                    :options="$sessions" 
                    option-label="year" 
                    option-value="year"
                    icon="o-calendar"
                />

                <div>
                    <h4 class="text-sm font-semibold text-gray-900 mb-4">Select Renewal Type</h4>
                    <div class="space-y-3">
                        @foreach ($applicationtypes->where('name', '!=', 'NEW') as $type)
                            <div class="border-2 rounded-lg p-4 cursor-pointer transition-all
                                {{ $renewaltype == $type->id ? 'border-gray-900 bg-gray-50' : 'border-gray-200 hover:border-gray-300' }}"
                                wire:click="selectrenewaltype('{{ $type->id }}')">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h5 class="font-semibold text-gray-900 text-base mb-1">{{ $type->name }}</h5>
                                        <p class="text-sm text-gray-600">{{ $type->description }}</p>
                                    </div>
                                    <div>
                                        @if ($renewaltype == $type->id)
                                            <x-icon name="o-check-circle" class="w-6 h-6 text-gray-900" />
                                        @else
                                            <div class="w-6 h-6 rounded-full border-2 border-gray-400"></div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            @if ($renewaltype)
                <x-slot:actions>
                    <x-button 
                        label="Cancel" 
                        class="btn-outline" 
                        @click="$wire.renewmodal = false" 
                    />
                    <x-button 
                        label="Proceed with Renewal" 
                        icon="o-arrow-right" 
                        class="btn-primary" 
                        type="submit" 
                        spinner="proceedwithrenewal" 
                    />
                </x-slot:actions>
            @endif
        </x-form>
    </x-modal>
</div>
