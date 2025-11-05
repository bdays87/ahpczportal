<div>
    <x-breadcrumbs :items="$breadcrumbs"    class="bg-base-300 p-3 rounded-box mt-2" />
    <x-card title="Renew Application" subtitle="Renew your application for the year {{ $application->year }}" separator class="mt-5 border-2 border-gray-200">
    <!-- Required documents -->
    @php
    $countuploaded = 0;
    foreach($uploaddocuments as $uploaddocument){
        if(!$uploaddocument["upload"]){
            $countuploaded++;
        }
    }

    @endphp
     <x-card title="1. Required documents" separator class="mt-5 border-2 border-gray-200">
        <table class="table table-zebra">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @forelse($uploaddocuments as $uploaddocument)
            <tr>
                <td>{{ $uploaddocument["document_name"] }}</td>
                <td ><div class="{{ $uploaddocument["upload"] ? "text-green-500" : "text-red-500" }}">{{ $uploaddocument["upload"] ? "Uploaded" : "Not uploaded" }}</div></td>
                <td class="flex justify-end gap-2">
                    @if($uploaddocument["upload"])
                    <x-button icon="o-document-magnifying-glass" label="View" class="btn-sm btn-info btn-outline" wire:click="viewdocument('{{ $uploaddocument['path'] }}')" spinner />
                    @if($invoice['status'] == "PENDING")
                    <x-button icon="o-trash" label="Remove" class="btn-sm btn-error btn-outline" wire:click="removedocument({{ $uploaddocument['document_id'] }})" spinner wire:confirm="Are you sure you want to remove this document?"/>
                    @endif
                    @else
                    <x-button icon="o-arrow-up-tray" label="Upload" class="btn-sm btn-primary btn-outline" wire:click="openuploaddocument({{ $uploaddocument['document_id'] }})" spinner />
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="2" class="text-center">No documents required for this application</td>
            </tr>
            @endforelse
            </tbody>
            </table>
 
     </x-card>

     <x-card title="2. Invoice" separator class="mt-5 border-2 border-gray-200">
        <livewire:admin.components.walletbalances :customer="$application->customerprofession->customer" />

        @if($countuploaded > 0)
        <x-alert title="Documents" description="You have {{ $countuploaded }} documents to upload. Please upload to settle the invoice." icon="o-x-mark" class="alert-error mt-2"/>
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
                    <div>{{ $invoice['currency']->name }} {{ $invoice['amount'] }}</div>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    @if($countuploaded == 0)     
                    @if($invoice['status'] == "PENDING")
                   <livewire:admin.components.receipts :invoice="$invoice" />
                   <livewire:admin.components.attachpop :invoice="$invoice" />
                   @elseif($invoice['status'] == "AWAITING")
                   <p class="text-red-500 font-bold text-right">Invoice is awaiting approval</p>
                   @elseif($invoice['status'] == "PAID")
                   <p class="text-green-500">Invoice is paid</p>
                   @endif
                   @endif

                       
             
                </td>
            </tr>
            
         </table>
     </x-card>
    </x-card>

    <x-modal wire:model="uploadmodal" title="Upload Document" separator>
        <x-form wire:submit="uploadDocument">
            <div class="grid  gap-2">
                <x-input label="File" wire:model="file" type="file" />             
            </div>
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.uploadmodal = false" />
                <x-button label="Upload" type="submit" class="btn-primary" spinner="uploadDocument" />
            </x-slot:actions>
        </x-form>
    </x-modal>

    <x-modal wire:model="documentview" title="Document View" box-class="max-w-6xl h-screen" separator>
        <iframe src="{{$documenturl}}" class="w-full h-screen"></iframe>
    </x-modal>
</div>
