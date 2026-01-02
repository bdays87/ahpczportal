<div>
    <x-breadcrumbs :items="$breadcrumbs"    class="bg-base-300 p-3 rounded-box mt-2" />
    <x-card  separator class="mt-5 border-2 border-gray-200">
        <x-steps wire:model="step" stepper-classes="w-full p-5 bg-base-200">
            <x-step step="1" text="Required documents" />
            @php
                $applicationtype_id = null;
                if($customerprofession->applications->count() > 0){
                    $applicationtype_id = $customerprofession->applications->last()->applicationtype_id;
                }else{
                    $applicationtype_id = 1;
                }
            @endphp
            @if($applicationtype_id == 1)
            <x-step step="2" text="Qualifications"/>
            
            <x-step step="3" text="Assessment invoice"/>
            <x-step step="4" text="Registration invoice"/>
            @endif
            <x-step step="5" text="Application invoice">
                <x-card class="border-2 mt-2 border-gray-200">
                  
                    @if($invoice)
                    @if($invoice['button'] == "enabled")
                   
                    <livewire:admin.components.walletbalances :customer="$customerprofession->customer" />
                     @endif
                     @if($invoice['status'] == "PAID")
                     <x-alert title="Invoice paid" description="The invoice has been paid successfully. Your application is being reviewed. Once approved, you will be notified." icon="o-check" class="alert-success">
                        <x-slot:actions>
                            <x-button label="Goto dashoard" icon="o-arrow-right" link="{{ route('dashboard') }}"/>
                        </x-slot:actions>
                    </x-alert>
                     @else
                     @if($invoice['button'] == "disabled")
                     
                     <x-alert title="Awaiting approval" description="{{ $invoice['comment'] }}" icon="o-exclamation-triangle" class="alert-error"/>
                     @else
                     <x-alert title="Invoice pending" description="Please settle the invoice to continue." icon="o-exclamation-triangle" class="alert-warning"/>
                    @endif
            
                     @endif
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
                                <div><b>Status :</b><span class="{{ $invoice['status'] == "PAID" ? "text-green-500" : "text-red-500" }}">{{ $invoice['status'] }}</span></div>
                            </td>
                            <td class="text-right">
                                <div>{{ $invoice['currency'] }} {{ $invoice['amount'] }}</div>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                @if($invoice['status'] == "PENDING" && $invoice['button'] == "enabled")
                                <div class="grid gap-2">
                               <livewire:admin.components.receipts :invoice="$invoice['data']" />
                               <livewire:admin.components.attachpop :invoice="$invoice['data']" />

                                      </div>
                            @endif
                            </td>
                        </tr>
                        
                     </table>
                    @else
                    <div class="p-5 bg-red-100 text-red-500 rounded-2xl">
                        No application invoice available your registration is awaiting approval.
                        <x-slot:actions>
                            <x-button label="Goto registration" icon="o-arrow-right" link="{{ route('newapplications.practitioners.registrationinvoicing', $uuid) }}"/>
                        </x-slot:actions>
                    </div>
                    @endif
                 
                </x-card>
            </x-step>
        

        </x-steps>
    </x-card>
</div>
