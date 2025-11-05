<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />
    @can('invoices.access')
    <x-card title="Invoices" separator class="mt-5 border-2 border-gray-200" separator progress-indicator>
        <x-slot:menu>
           <x-select wire:model.live="status" :options="[['id'=>'AWAITING','name'=>'AWAITING'],['id'=>'PAID','name'=>'PAID']]" option-label="name" option-value="id" placeholder="Select" />
        </x-slot:menu>

        <x-table :headers="$headers" :rows="$invoices">
            @scope('cell_customer', $invoice)
            {{ $invoice->customer->name }} {{ $invoice->customer->surname }}

            @endscope
            @scope('cell_amount', $invoice)
            {{ $invoice->currency->name }} {{ $invoice->amount }} 

            @endscope
            @scope('actions', $invoice)
            <div class="flex items-center space-x-2">
                @can('invoices.receipt')
                <x-button icon="o-document-magnifying-glass" label="View" class="btn-sm btn-info btn-outline" 
                    wire:click="viewinvoice({{ $invoice->id }})" spinner />
                @endcan
               
            </div>
            @endscope
            <x-slot:empty>
                <x-alert class="alert-error" title="No invoices found." />
            </x-slot:empty>
        </x-table>
    </x-card>
    @else
    <x-alert class="alert-error mt-3" title="You do not have permission to view invoices." />
    @endcan

    <x-modal title="Invoice" wire:model="modal" box-class="max-w-6xl">
        <table class="table table-compact">
            <tr>
                <td>Date</td>
                <td>{{ $invoice?->created_at }}</td>
            </tr>
            <tr>
                <td>Customer</td>
                <td>{{ $invoice?->customer->name }} {{ $invoice?->customer->surname }}</td>
            </tr>
            <tr>
                <td>Invoice Number</td>
                <td>{{ $invoice?->invoice_number }}</td>
            </tr>
            <tr>
                <td>Description</td>
                <td>{{ $invoice?->description }}</td>
            </tr>
            <tr>
                <td>Amount</td>
                <td>{{ $invoice?->currency->name }}{{ $invoice?->amount }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td>{{ $invoice?->status }}</td>
            </tr>
        </table>
        @if($invoice?->status != "PAID")
        <x-card title="Attachments" separator>
            <table class="table table-compact">
                <tr>
                    <td>File</td>
                    <td></td>
                </tr>
                @forelse ($invoice?->proofofpayment??[] as $proof)
                    <tr>
                        <td>{{ $proof?->created_at }}</td>
                        <td class="flex items-center justify-end space-x-2">
                            <x-button icon="o-arrow-down-on-square-stack" label="View" class="btn-sm btn-info btn-outline" 
                                wire:click="viewdocument({{ $proof->id }})" spinner />
                        </td>
                    </tr>
                @empty
                <tr>
                    <td colspan="2" class="text-center p-2 bg-red-400 text-red">No documents assigned</td>
                </tr>
                @endforelse
            </table>
        </x-card>
        <x-slot:actions>
            @if($invoice)
         <livewire:admin.components.receipts :invoice="$invoice" /> 
               @endif 
        </x-slot:actions>
        @else
        <x-button icon="o-arrow-right" label="Download receipt" class="btn btn-primary btn-outline" wire:click="downloadreceipt({{ $invoice?->id }})"/>
  
        @endif
    </x-modal>

    <x-modal title="Document" wire:model="documentmodal" box-class="max-w-5xl">
        <x-tabs wire:model="selectedTab">
          
                <x-tab label="Document" name="document">
                    <iframe src="{{ $documenturl }}" width="100%" height="600px"></iframe>
                </x-tab>
                <x-tab label="Bank transactions" name="banktransactions">
                    <x-input wire:model.live="search" placeholder="Search" />  
                    <x-hr/>
                    <x-table :headers="$banktransactionheaders" :rows="$banktransactions">
                        @scope('cell_amount', $banktransaction)
                        {{ $banktransaction->currency->name }} {{ $banktransaction->amount }}
                        @endscope
                        @scope('cell_status', $banktransaction)
                        <x-badge class="{{ $banktransaction->status == 'CLAIMED' ? 'badge-success' : 'badge-warning' }}" value="{{ $banktransaction->status }}" />
                        @endscope
                        @scope('actions', $banktransaction)
                        <div class="flex items-center space-x-2">
                            @can('banktransactions.claim')
                            @if($banktransaction->status == 'PENDING')
                            <x-button icon="o-pencil" label="Claim" class="btn-sm btn-info btn-outline" 
                                wire:click="claimbanktransaction({{ $banktransaction->id }})" spinner />
                            @endif
                            @endcan
                        </div>
                        @endscope
                        <x-slot:empty>
                            <x-alert class="alert-error" title="No bank transactions found." />
                        </x-slot:empty>
                    </x-table>
                </x-tab>
           
        </x-tabs>
    </x-modal>
  
</div>
