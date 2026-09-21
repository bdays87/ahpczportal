<div>
    <x-breadcrumbs :items="$breadcrumbs"    class="bg-base-300 p-3 rounded-box mt-2" />

    <x-stepnav :customer="$customerprofession->customer"
        :previous="route('newapplications.practitioners.assessmentinvoicing', $uuid)" />

    <x-card  separator class="mt-5 border-2 border-gray-200">
        <x-steps wire:model="step" stepper-classes="w-full p-5 bg-base-200">
            <x-step step="1" text="Required documents" />
            <x-step step="2" text="Qualifications"/>
            <x-step step="3" text="Assessment invoice"/>
            <x-step step="4" text="Registration Review">
                <x-card class="border-2 mt-2 border-gray-200">
                   
                    @if($invoice)
                    
                    {{-- Show info that registration is under review (no payment needed) --}}
                    <x-alert title="Application Under Review" 
                             description="Your registration application is under review. No payment is required. Please wait for administrator approval." 
                             icon="o-document-check" 
                             class="alert-info mb-4"/>
                    
                    {{-- Show invoice details for reference only (without payment buttons) --}}
                    <div class="text-sm font-semibold mb-2 text-gray-600">Invoice Details (For Reference Only)</div>
                     <table class="table table-compact">
                        <thead>
                            <tr>
                                <th>Invoice details</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tr>
                            <td>
                                <div><b>Invoice Number :</b><span class="text-gray-500">{{ $invoice['invoice_number'] }}</span></div>
                                <div><b>Date :</b><span class="text-gray-500">{{ $invoice['created_at'] }}</span></div>
                                <div><b>Description :</b><span class="text-gray-500">{{ $invoice['description'] }}</span></div>
                                <div><b>Status :</b><span class="text-info">UNDER REVIEW</span></div>
                            </td>
                            <td class="text-right">
                                <div class="text-gray-400">{{ $invoice['currency'] }} {{ $invoice['amount'] }}</div>
                            </td>
                        </tr>
                     </table>
                     
                     {{-- Show registration status if available --}}
                     @if($customerprofession->registration)
                     <div class="mt-4 p-4 bg-base-200 rounded-lg">
                        <h3 class="font-semibold mb-2">Registration Status</h3>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div><strong>Status:</strong></div>
                            <div>
                                @php
                                    $status = $customerprofession->registration->status;
                                    $badgeClass = match($status) {
                                        'APPROVED' => 'badge-success',
                                        'AWAITING' => 'badge-warning',
                                        'REJECTED' => 'badge-error',
                                        default => 'badge-ghost'
                                    };
                                @endphp
                                <x-badge :value="$status" :class="$badgeClass" />
                            </div>
                            <div><strong>Submitted:</strong></div>
                            <div>{{ $customerprofession->registration->created_at->format('d M Y') }}</div>
                            @if($status == 'AWAITING')
                            <div class="col-span-2 mt-2 text-info flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                <span>Your registration is currently being reviewed by administrators. You will receive a notification once it has been processed.</span>
                            </div>
                            @elseif($status == 'APPROVED')
                            <div class="col-span-2 mt-2 text-success flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                <span>Your registration has been approved! You can now proceed to the application step.</span>
                            </div>
                            <div class="col-span-2 mt-3">
                                <x-button label="Proceed to Application" 
                                         icon="o-arrow-right" 
                                         link="{{ route('newapplications.practitioners.applicationinvoicing', $uuid) }}"
                                         class="btn-primary w-full"/>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                    
                    @else
                    {{-- No invoice - Registration submitted for review --}}
                    <x-alert title="Registration Submitted for Review" 
                             description="Your application has been submitted for document and qualification review. You do not need to pay for registration. Please wait for administrator approval to proceed to the next step." 
                             icon="o-check-circle" 
                             class="alert-info">
                        <x-slot:actions>
                            @if($customerprofession->status == 'APPROVED' || $customerprofession->registration?->status == 'APPROVED')
                                <x-button label="Proceed to Application" 
                                         icon="o-arrow-right" 
                                         link="{{ route('newapplications.practitioners.applicationinvoicing', $uuid) }}" 
                                         class="btn-primary"/>
                            @else
                                <x-badge value="Awaiting Approval" class="badge-warning badge-lg"/>
                            @endif
                        </x-slot:actions>
                    </x-alert>
                    
                    {{-- Show registration status if available --}}
                    @if($customerprofession->registration)
                    <div class="mt-4 p-4 bg-base-200 rounded-lg">
                        <h3 class="font-semibold mb-2">Registration Status</h3>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div><strong>Status:</strong></div>
                            <div>
                                @php
                                    $status = $customerprofession->registration->status;
                                    $badgeClass = match($status) {
                                        'APPROVED' => 'badge-success',
                                        'AWAITING' => 'badge-warning',
                                        'REJECTED' => 'badge-error',
                                        default => 'badge-ghost'
                                    };
                                @endphp
                                <x-badge :value="$status" :class="$badgeClass" />
                            </div>
                            <div><strong>Submitted:</strong></div>
                            <div>{{ $customerprofession->registration->created_at->format('d M Y') }}</div>
                            @if($status == 'AWAITING')
                            <div class="col-span-2 mt-2 text-info flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                <span>Your registration is currently being reviewed by administrators. You will receive a notification once it has been processed.</span>
                            </div>
                            @elseif($status == 'APPROVED')
                            <div class="col-span-2 mt-2 text-success flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                <span>Your registration has been approved! You can now proceed to the application step.</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                    @endif
                 
                </x-card>
            </x-step>
            <x-step step="5" text="Practitioner certificate invoice" />

        </x-steps>
    </x-card>
</div>
