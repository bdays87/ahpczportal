<div>

    <x-header title="Professional Qualifications" subtitle="Manage practitioner professions and certifications" class="mt-5 bg-gradient-to-r from-green-200 to-green-600 rounded-sm shadow-lg p-6" separator>
       
        <x-slot:actions>
            <x-button icon="o-plus" label="Add Profession" class="btn-primary" wire:click="$set('addmodal', true)" spinner />
        </x-slot:actions>
    </x-header> 
  

        @if ($customer->customerprofessions->count() > 0)  
            <div class="grid gap-5 mt-6">
                @forelse ($customer->customerprofessions as $customerprofession)
                    <div class="bg-white rounded-xl  border border-gray-200 overflow-hidden hover:shadow-2xl transition-shadow duration-300">
                        {{-- Card Header --}}
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-indigo-600">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                                        </svg>
                                        {{ $customerprofession?->profession?->name }}
                                    </h3>
                                    <p class="text-sm text-gray-600 mt-1">
                                        <span class="font-semibold">Last Application:</span> 
                                        {{ $customerprofession?->applications?->last()?->year ?? 'N/A' }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if (
                                        $customerprofession?->applications?->count() > 0 &&
                                            $customerprofession?->applications?->last()?->status == 'APPROVED')
                                        @if ($customerprofession->isCompliant())
                                            <div class="px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-bold flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Compliant
                                            </div>
                                        @else
                                            <div class="px-4 py-2 bg-red-100 text-red-800 rounded-full text-sm font-bold flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                                </svg>
                                                Non-Compliant
                                            </div>
                                        @endif
                                    @else
                                        <x-badge value="{{ $customerprofession?->applications?->last()?->status }}"
                                            class="badge-neutral" />
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Card Body --}}
                        <div class="p-6">
                            <div class="grid lg:grid-cols-4 gap-4">
                                {{-- Register Type --}}
                                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                                    <div class="flex items-center gap-2 text-blue-600 mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                                        </svg>
                                        <span class="text-xs font-semibold uppercase">Register Type</span>
                                    </div>
                                    <p class="text-gray-800 font-bold text-lg">{{ $customerprofession->applications?->last()->registertype?->name ?? 'N/A' }}</p>
                                </div>

                                {{-- Customer Type --}}
                                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4 border border-purple-200">
                                    <div class="flex items-center gap-2 text-purple-600 mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                        </svg>
                                        <span class="text-xs font-semibold uppercase">Customer Type</span>
                                    </div>
                                    <p class="text-gray-800 font-bold text-lg">{{ $customerprofession?->customertype?->name ?? 'N/A' }}</p>
                                </div>

                                <div class="flex items-center gap-3 p-3 bg-orange-50 rounded-lg">
                                    <div class="bg-indigo-100 p-2 rounded-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-indigo-600">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase font-semibold">Application Type</p>
                                        <p class="text-gray-800 font-bold">{{ $customerprofession?->applications?->last()?->applicationtype?->name ?? 'N/A' }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg">
                                    <div class="bg-green-100 p-2 rounded-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-green-600">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0118 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3l1.5 1.5 3-3.75" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase font-semibold">Application Status</p>
                                        <p class="text-gray-800 font-bold">{{ $customerprofession?->applications?->last()?->status ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Application Details --}}
                            <div class="mt-4 grid  gap-4">
                                
                                
                                @if($customerprofession->applications?->last()?->status == 'APPROVED')
                                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                    <div class="bg-green-100 p-2 rounded-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-green-600">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0118 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3l1.5 1.5 3-3.75" />
                                        </svg>
                                    </div>

                                    <div>
                                        <p class="text-xs text-gray-500 uppercase font-semibold">My CDP Status</p>
                                        <div class="flex items-center gap-2">
                                        <div class="flex items-center gap-2"><div class="text-gray-800 font-bold "> Current CDP Points:</div><x-badge value="{{ $customerprofession?->totalcdpoints() }}" class="{{ $customerprofession?->totalcdpoints() >= $customerprofession?->profession?->tires?->where('tire_id',$customerprofession?->tire_id)?->first()?->minimum_cdp ?? 0 ? 'badge-success' : 'badge-error' }} badge-lg" /> </div>
                                        <div class="flex items-center gap-2"><div class="text-gray-800 font-bold "> Minimum Required CDP Points:</div><x-badge value="{{ $customerprofession?->profession?->tires?->where('tire_id',$customerprofession?->tire_id)?->first()?->minimum_cdp ?? 0 }}" class="badge-lg badge-success" /> </div>
                                            <livewire:admin.components.mycdps :customerprofession="$customerprofession" />
                                        </div>
                                    </div>
                                    
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                            <div class="flex flex-wrap items-center gap-3">
                               
                                @if (strtolower($customerprofession?->applications?->last()?->status) == 'pending' ||$customerprofession->status != 'APPROVED')
                                    @if ($customerprofession?->customertype?->name == 'Student')
                                        @if($customerprofession?->uuid)
                                            <x-button icon="o-eye" label="View Details" class="btn-sm bg-blue-600 text-white hover:bg-blue-700 border-0"
                                                link="{{ route('customer.student.show', $customerprofession->uuid) }}"
                                                spinner />
                                        @else
                                            <x-button icon="o-eye" label="View Details" class="btn-sm bg-gray-400 text-white border-0" disabled />
                                        @endif
                                    @else
                                        @if($customerprofession?->uuid)
                                            <x-button icon="o-arrow-right-circle" label="Continue Setup" class="btn-sm bg-blue-600 text-white hover:bg-blue-700 border-0"
                                                link="{{ route('customer.profession.show', $customerprofession->uuid) }}"
                                                spinner />
                                        @else
                                            <x-button icon="o-arrow-right-circle" label="Continue Setup" class="btn-sm bg-gray-400 text-white border-0" disabled />
                                        @endif
                                    @endif
                                    <x-button icon="o-trash" label="Delete" class="btn-sm bg-red-100 text-red-700 hover:bg-red-200 border border-red-300"
                                        wire:click="delete({{ $customerprofession->id }})" wire:confirm="Are you sure you want to delete this profession?"
                                        spinner />
                                @elseif (strtolower($customerprofession->applications?->last()?->status) == 'approved' || strtolower($customerprofession->applications?->last()?->status) == 'active')
                                    @if ($customerprofession->customertype->name == 'Student')
                                        @if($customerprofession->uuid)
                                            <x-button icon="o-eye" label="View Details" class="btn-sm bg-blue-600 text-white hover:bg-blue-700 border-0"
                                                link="{{ route('customer.student.show', $customerprofession->uuid) }}"
                                                spinner />
                                        @else
                                            <x-button icon="o-eye" label="View Details" class="btn-sm bg-gray-400 text-white border-0" disabled />
                                        @endif
                                    @else
                                        @if ($customerprofession->applications->count() > 0)
                                        
                                            @if (
                                                $customerprofession->applications->last()->status == 'APPROVED' &&
                                                    $customerprofession->applications->last()->isExpired())
                                                    @if( $customerprofession?->totalcdpoints() >= $customerprofession?->profession?->tires?->where('tire_id',$customerprofession?->tire_id)?->first()?->minimum_cdp ?? 0)
                                                <x-button icon="o-arrow-path" label="Renew Certificate" class="btn-sm bg-amber-500 text-white hover:bg-amber-600 border-0"
                                                    wire:click="renew({{ $customerprofession->id }})" spinner />
                                                    @else
                                                    <x-alert title="CDP Points Insufficient" description="You have insufficient CDP points to renew your certificate" icon="o-exclamation-triangle" class="alert-error">
                                                        <x-slot:actions>
                                                            <x-button icon="o-arrow-right-circle" label="View online activities" link="{{ route('customer.activities') }}" class="btn-sm btn-primary btn-outline"/>

                                                <livewire:admin.components.mycdps :customerprofession="$customerprofession" />
                                                        </x-slot:actions>
                                                    </x-alert>
                                                    @endif
                                            @else
                                                @if (
                                                    $customerprofession->applications->last()->status == 'APPROVED' &&
                                                        !$customerprofession->applications->last()->status == 'PENDING')
                                                    <x-button icon="o-arrow-right-circle" label="Proceed with Renewal"
                                                        class="btn-sm bg-indigo-600 text-white hover:bg-indigo-700 border-0"
                                                        link="{{ route('customers.application.renewal', $customerprofession->applications->last()->uuid) }}"
                                                        spinner />
                                                @endif
                                            @endif
                                        @endif
                                        @if (
                                            $customerprofession->applications->last()->status == 'APPROVED' &&
                                                !$customerprofession->applications->last()->isExpired())
                                            <x-button icon="o-arrow-down-tray" label="Download Certificates" class="btn-sm bg-green-600 text-white hover:bg-green-700 border-0"
                                                wire:click="viewapplication({{ $customerprofession->id }})" spinner />

                                         
                                        @endif
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div> 
                @empty
                    <div class="bg-white rounded-xl shadow-lg border-2 border-dashed border-gray-300 p-12 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-20 h-20 mx-auto text-gray-400 mb-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z" />
                        </svg>
                        <h3 class="text-xl font-bold text-gray-700 mb-2">No Professions Found</h3>
                        <p class="text-gray-500 mb-6">This practitioner hasn't added any professional qualifications yet.</p>
                        <x-button 
                            icon="o-plus" 
                            label="Add First Profession" 
                            class="btn-lg bg-indigo-600 text-white hover:bg-indigo-700 border-0" 
                            wire:click="$set('addmodal', true)"
                            spinner 
                        />
                    </div>

                @endforelse
            </div>
        @else
            <div class="mt-6 bg-white rounded-xl shadow-lg border-2 border-dashed border-gray-300 p-12 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-20 h-20 mx-auto text-gray-400 mb-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z" />
                </svg>
                <h3 class="text-xl font-bold text-gray-700 mb-2">No Professions Found</h3>
                <p class="text-gray-500 mb-6">Get started by adding your first professional qualification.</p>
                <x-button 
                    icon="o-plus" 
                    label="Add First Profession" 
                    class="btn-lg bg-indigo-600 text-white hover:bg-indigo-700 border-0" 
                    wire:click="$set('addmodal', true)"
                    spinner 
                />
            </div>
        @endif


   
    {{-- Add Profession Modal --}}
    <x-modal wire:model="addmodal" box-class="max-w-4xl" persistent>
        <div class="bg-gradient-to-r from-blue-600 to-blue-600  p-6 -m-6 mb-6">
            <h3 class="text-2xl font-bold text-white flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Add New Profession
            </h3>
            <p class="text-indigo-100 text-sm mt-1">Enter the professional qualification details below</p>
        </div>

        @if ($errormessage)
            <x-alert class="alert-error mb-4" icon="o-exclamation-triangle" title="Error" :description="$errormessage" />
        @endif

        <x-form wire:submit.prevent="addprofession">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <x-select 
                        label="Profession" 
                        wire:model="profession_id"
                        placeholder="Select Profession" 
                        :options="$professions" 
                        option-label="name" 
                        option-value="id"
                        icon="o-academic-cap"
                    />
                </div>
                
                <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                    <x-select 
                        label="Customer Type" 
                        wire:model.live="customertype_id"
                        placeholder="Select Customer Type" 
                        :options="$customertypes" 
                        option-label="name" 
                        option-value="id"
                        icon="o-user-circle"
                    />
                </div>
                
              
                
                <div class="bg-amber-50 p-4 rounded-lg border border-amber-200">
                    <x-select 
                        label="Employment Status" 
                        wire:model.live="employmentstatus_id"
                        placeholder="Select Employment Status" 
                        :options="$employmentstatuses" 
                        option-label="name" 
                        option-value="id"
                        icon="o-briefcase"
                    />
                </div>
                @if($employmentstatus_id==1)
                <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-200 ">
                    <x-select 
                        label="Employment Location" 
                        wire:model="employmentlocation_id" 
                        placeholder="Select Employment Location" 
                        :options="$employmentlocations"
                        option-label="name" 
                        option-value="id"
                        icon="o-map-pin"
                    />
                </div>
                <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-200 ">
                    <x-select 
                        label="Employment Sector" 
                        wire:model="employmentsector_id" 
                        placeholder="Select Employment Sector" 
                        :options="$employmentssectors"
                        option-label="name" 
                        option-value="id"
                        icon="o-map-pin"
                    />
                </div>
                @endif
            </div>

            <x-slot:actions>
                <x-button 
                    label="Cancel" 
                    class="btn-md bg-gray-200 text-gray-700 hover:bg-gray-300 border-0" 
                    wire:click="$set('addmodal', false)" 
                />
                <x-button 
                    label="Save Profession" 
                    icon="o-check" 
                    class="btn-md bg-indigo-600 text-white hover:bg-indigo-700 border-0" 
                    type="submit" 
                    spinner="addprofession" 
                />
            </x-slot:actions>
        </x-form>
    </x-modal>

    {{-- Certificates Modal --}}
    <x-modal wire:model="openmodal" box-class="max-w-4xl" persistent>
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 rounded-t-xl p-6 -m-6 mb-6">
            <h3 class="text-2xl font-bold text-white flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
                Professional Certificates
            </h3>
            <p class="text-emerald-100 text-sm mt-1">View and download your certificates</p>
        </div>

        {{-- Registration Certificate --}}
        <div class="bg-blue-50 rounded-xl p-6 mb-5 border border-blue-200">
            <h4 class="text-lg font-bold text-blue-900 mb-4 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                </svg>
                Registration Certificate
            </h4>
            <div class="bg-white rounded-lg p-4 flex items-center justify-between shadow-sm">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Certificate Number</p>
                    <p class="text-lg font-bold text-gray-800">{{ $customerprofession?->registration?->certificatenumber ?? 'N/A' }}</p>
                    <p class="text-sm text-gray-600 mt-2">
                        <span class="font-semibold">Registration Date:</span> 
                        {{ $customerprofession?->registration?->registrationdate ?? 'N/A' }}
                    </p>
                </div>
                <x-button 
                    icon="o-arrow-down-tray" 
                    label="Download"
                    class="btn-md bg-blue-600 text-white hover:bg-blue-700 border-0" 
                    spinner
                    wire:click="downloadregistrationcertificate({{ $customerprofession?->registration?->id }})" 
                />
            </div>
        </div>

        {{-- Application Certificates --}}
        <div class="bg-purple-50 rounded-xl p-6 border border-purple-200">
            <h4 class="text-lg font-bold text-purple-900 mb-4 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0118 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3l1.5 1.5 3-3.75" />
                </svg>
                Application Certificates
            </h4>
            <div class="space-y-3">
                @forelse ($customerprofession->applications??[] as $application)
                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <p class="text-sm text-gray-500">
                                        <span class="font-semibold text-gray-700">Year:</span> {{ $application->year }}
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
                                    <span class="font-semibold">Certificate #:</span> {{ $application->certificate_number ?? 'N/A' }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    <span class="font-semibold">Expiry Date:</span> {{ $application->certificate_expiry_date ?? 'N/A' }}
                                </p>
                            </div>
                            @if (!$application->isExpired() && $application->status == 'APPROVED')
                                <x-button 
                                    icon="o-arrow-down-tray" 
                                    label="Download" 
                                    class="btn-sm bg-purple-600 text-white hover:bg-purple-700 border-0" 
                                    spinner
                                    wire:click="downloadpractisingcertificate({{ $application->id }})" 
                                />
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-lg p-8 text-center border-2 border-dashed border-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 mx-auto text-gray-400 mb-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                        <p class="text-gray-500 font-medium">No application certificates found</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <x-button 
                label="Close" 
                class="btn-md bg-gray-200 text-gray-700 hover:bg-gray-300 border-0" 
                wire:click="$set('openmodal', false)" 
            />
        </div>
    </x-modal>
    {{-- Renewal Modal --}}
    <x-modal wire:model="renewmodal" box-class="max-w-3xl" persistent class="backdrop-blur-sm">
        <div class="bg-gradient-to-r from-amber-600 to-orange-600 rounded-t-xl p-6 -m-6 mb-6">
            <h3 class="text-2xl font-bold text-white flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                Certificate Renewal
            </h3>
            <p class="text-amber-100 text-sm mt-1">Select renewal period and type to continue</p>
        </div>

        @if ($message)
            <x-alert class="alert-error mb-4" icon="o-exclamation-triangle" title="Error" :description="$message" />
        @endif

        <x-form wire:submit.prevent="proceedwithrenewal">
            <div class="space-y-5">
                {{-- Period Selection --}}
                <div class="bg-blue-50 rounded-xl p-5 border border-blue-200">
                    <label class="text-sm font-bold text-blue-900 mb-3 block flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                        Select Renewal Period
                    </label>
                    <x-select 
                        wire:model="session_id" 
                        placeholder="Choose renewal period"
                        :options="$sessions" 
                        option-label="year" 
                        option-value="year"
                        icon="o-calendar"
                    />
                </div>

                {{-- Renewal Type Selection --}}
                <div class="bg-purple-50 rounded-xl p-5 border border-purple-200">
                    <h4 class="text-sm font-bold text-purple-900 mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                        </svg>
                        Select Renewal Type
                    </h4>
                    <div class="space-y-3">
                        @foreach ($applicationtypes->where('name', '!=', 'NEW') as $type)
                            <div class="bg-white rounded-lg p-4 border-2 transition-all duration-200 cursor-pointer hover:shadow-md
                                {{ $renewaltype == $type->id ? 'border-purple-500 bg-purple-50 shadow-md' : 'border-gray-200 hover:border-purple-300' }}"
                                wire:click="selectrenewaltype('{{ $type->id }}')">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h5 class="font-bold text-gray-800 text-lg mb-1">{{ $type->name }}</h5>
                                        <p class="text-sm text-gray-600">{{ $type->description }}</p>
                                    </div>
                                    <div>
                                        @if ($renewaltype == $type->id)
                                            <div class="bg-purple-600 text-white rounded-full p-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                                </svg>
                                            </div>
                                        @else
                                            <div class="bg-gray-200 text-gray-400 rounded-full p-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                                </svg>
                                            </div>
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
                        class="btn-md bg-gray-200 text-gray-700 hover:bg-gray-300 border-0" 
                        @click="$wire.renewmodal = false" 
                    />
                    <x-button 
                        label="Proceed with Renewal" 
                        icon="o-arrow-right" 
                        class="btn-md bg-amber-600 text-white hover:bg-amber-700 border-0" 
                        type="submit" 
                        spinner="proceedwithrenewal" 
                    />
                </x-slot:actions>
            @endif
        </x-form>
    </x-modal>
</div>
