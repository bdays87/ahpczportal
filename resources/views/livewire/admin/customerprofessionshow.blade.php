<div>
  <x-breadcrumbs :items="$breadcrumbs"    class="bg-base-300 p-3 rounded-box mt-2" />
  <x-card title="{{ $customerprofession->customer->name }} {{ $customerprofession->customer->surname }}" subtitle="{{ $customerprofession->profession->name }} || {{ $customerprofession->registertype->name }} || {{ $customerprofession->customertype->name }}" separator class="mt-5 border-2 border-gray-200">
  <x-steps wire:model="step" class="border-y border-base-content/10 my-5 py-5" stepper-classes="w-full p-5 bg-base-200">
    <x-step step="1" text="Required documents">
    <x-card class="border-2 border-gray-200">

        @php  

        $countpendingupload = collect($uploaddocuments)->where('upload', false)->count();
        
        @endphp
        @if ($countpendingupload > 0)
        <div class="text-center text-red-500 rounded-2xl p-5 font-bold bg-red-100">
            You have {{ $countpendingupload }} pending documents to upload. Please upload them to continue.
        </div>
        @else
        <x-alert title="Documents uploaded" description="All documents have been uploaded successfully." icon="o-check" class="alert-success">
            <x-slot:actions>
                <x-button label="Continue" wire:click="nextstep(2)"/>
            </x-slot:actions>
        </x-alert>
        @endif
     <table class="table table-zebra">
        <thead>
            <tr>
                <th>Document</th>
                <th>Upload</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($uploaddocuments as $uploaddocument)
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
                    <x-button wire:click="removeDocument({{ $uploaddocument['document_id'] }})" icon="o-trash" class="btn btn-xs btn-error">Remove</x-button>
               
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
     <x-slot:actions>
           
     @if ($countpendingupload == 0)
            <x-button label="Proceed" icon="o-arrow-right" class="btn btn-primary btn-outline" wire:click="nextstep(2)"/>
            @endif
        </x-slot:actions>
    </x-card>
    </x-step>
    <x-step step="2" text="Qualifications">
    <x-card title="Profession Related Qualifications" separator class="border-2 border-gray-200">
        <x-slot:menu>
            <x-button label="Add Qualification" wire:click="$set('qualificationmodal',true)" class="btn btn-primary"/>
        </x-slot:menu>
        @if ($customerprofession->qualifications->count() > 0)
        <x-alert title="Qualifications added" description=" Qualification have been added successfully." icon="o-check" class="alert-success">
            <x-slot:actions>
                <x-button label="Continue" wire:click="nextstep(3)" spinner="nextstep"/>
            </x-slot:actions>
        </x-alert>

        @else
        <div class="text-center text-red-500 rounded-2xl p-5 font-bold bg-red-100">
            No qualifications found for this customer profession. Please add related qualifications to continue.
        </div>
        @endif
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
                    <td class="flex justify-end items-center space-x-2">
                        @if ($qualification->status == "PENDING")
                        <x-button wire:click="editQualification({{ $qualification->id }})" icon="o-pencil" class="btn btn-info btn-xs">Edit</x-button>
                        <x-button wire:click="removeQualification({{ $qualification->id }})" icon="o-trash" class="btn btn-error btn-xs" wire:confirm="Are you sure you want to delete this qualification?">Delete</x-button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center p-2  text-red">No qualifications found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <x-slot:actions>
            <x-button label="Previous" icon="o-arrow-left" class="btn btn-primary btn-outline" wire:click="prevstep(1)"/>
            @if ($customerprofession->qualifications->count() > 0)
            <x-button label="Proceed" icon="o-arrow-right" class="btn btn-primary btn-outline" wire:click="nextstep(3)" spinner="nextstep"/>
            @endif
        </x-slot:actions>
    </x-card>
    </x-step>
    <x-step step="3" text="Invoice settlement">
        <x-alert icon="o-exclamation-triangle" title="PLEASE NOTE" description="This system will enable you to pay for the invoice item in a pre-defined order" class="alert-warning" />
        <x-card title="Invoice Settlement" subtitle="Application status: {{ $customerprofession->status }}" separator class="border-2 border-gray-200 mt-2">
          <x-slot:menu>
            <livewire:admin.components.wallettopups :currencies="$currencies" :customer="$customerprofession->customer" />
          <livewire:admin.components.walletbalances :customer="$customerprofession->customer" />
          </x-slot:menu>
       <table class="table table-zebra table-compact">
        <thead>
            <tr>
                <th>Details</th>
                <th>Settlement Split</th>
                <th>Status</th>                
                <th>Amount</th>
                <th>Paid</th> 
                <th>Balance</th>
                <th>Comment</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @if($invoices)
            @forelse ($invoices["data"] as $inv)
            <tr>
                <td>
                    <div>Date: {{ $inv['created_at'] }}</div>
                    <div>Invoice Number: {{ $inv['invoice_number'] }}</div>
                    <div>Description: {{ $inv['description'] }}</div>
                </td>
               
                <td>{{ $inv['settlementsplit'] }}</td>
                <td><x-badge value="{{ $inv['status'] }}" class="{{ $inv['status'] == 'PAID' ? 'badge-success' : 'badge-warning' }}" /></td>
                <td>{{ $inv['currency'] }}{{ $inv['amount'] }}</td>
                <td>{{ $inv['currency'] }}{{ $inv['paid'] }}</td>
                <td>{{ $inv['currency'] }}{{ $inv['balance'] }}</td>
                <td>
                    @if($inv['comment'] != "")
                   <div class="text-red-500 text-xs"> {{ $inv['comment'] }}</div>
               
                    @endif
                </td>
                <td>
                    @if($inv['button'] == "enabled" && $inv['status'] == "PENDING")
                    <x-button label="Pay"  class="btn btn-sm btn-success" wire:click="openpaymentmodal({{ $inv['id'] }})"/>
                    @else
                    <x-button label="Pay"  disabled class="btn btn-sm btn-primary btn-outline"/>
                    @endif
           
                  
                </td>

            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center p-2 bg-red-400 text-red">No invoices found</td>
            </tr>
            @endforelse
                  
     
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            
            <td class="text-center">Total invoice</td>
            <td>{{ $invoices["invoice_currency"] }}{{ $invoices["total_invoice"] }}</td>
            <td>
                           </td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            
            <td class="text-center">Total paid</td>
            <td>{{ $invoices["invoice_currency"] }}{{ $invoices["total_paid"] }}</td>
            <td>
                           </td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            
            <td class="text-center">Total balance</td>
            <td>{{ $invoices["invoice_currency"] }}{{ $invoices["total_balance"] }}</td>
            <td>
                           </td>
        </tr>
       
       
        @endif 
            
        </tbody>
       </table>
       <x-slot:actions>
            <x-button label="Previous" icon="o-arrow-left" class="btn btn-primary btn-outline" wire:click="prevstep(2)"/>
        </x-slot:actions>
</x-card>
     
    </x-step>
</x-steps>
</x-card>

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

<x-modal wire:model="qualificationmodal" title="{{ $customerprofessionqualification_id ? 'Edit' : 'Add' }} Qualification" separator>
    <x-form wire:submit="savequalification">
        <div class="grid grid-cols-2  gap-2">
            <x-input label="Name" wire:model="name" />
            <x-select label="Category" wire:model="qualificationcategory_id" :options="$categories" option-label="name" option-value="id" placeholder="Select" />
            <x-select label="Level" wire:model="qualificationlevel_id" :options="$levels" option-label="name" option-value="id" placeholder="Select" />
            <x-input label="Institution" wire:model="institution" />
            <x-input label="Year" wire:model="year" type="number" />
            <x-input label="File" wire:model="qualificationfile" type="file" />
        </div>
        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.qualificationmodal = false" />
            <x-button label="{{ $customerprofessionqualification_id ? 'Update' : 'Save' }}" type="submit" class="btn-primary" spinner="savequalification" />
        </x-slot:actions>
    </x-form>
</x-modal>

<x-modal wire:model="paymentmodal" title="Receipting" separator box-class="h-screen">

    <div class="grid  gap-2">
    <x-button label="Attach Payment" icon="o-paper-clip" class="btn w-full btn-secondary" wire:click="getattachpayments"/>

    @if($invoice)
    <livewire:admin.components.receipts :invoice="$invoice" /> 
  
    @endif
    </div>

</x-modal>
<x-modal wire:model="attachmodal" title="Attach Payments" separator>
    <x-file wire:model.live="paymentfile" label="Receipt" hint="Only PDF" accept="application/pdf" />
     <x-hr />
    <table class="table table-zebra">
        <thead>
            <tr>
                <th>Document</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($proofofpayments as $proofofpayment)
            <tr>
                <td>{{ $proofofpayment->created_at }}</td>
                <td class="flex justify-end items-center space-x-2">
                    <x-button icon="o-trash" class="btn-sm btn-outline btn-error" 
                        wire:click="deleteattachpayment({{ $proofofpayment->id }})" spinner />
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="2" class="text-center p-2 bg-red-400 text-red">No documents assigned</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <x-slot:actions>
        @if($proofofpayments->count() > 0)
        <x-button label="Submit for receipting" icon="o-paper-clip" class="btn btn-secondary" wire:confirm="Are you sure you want to submit for receipting?" wire:click="submitforverification"/>
        @endif
    </x-slot:actions>
</x-modal>
</div>