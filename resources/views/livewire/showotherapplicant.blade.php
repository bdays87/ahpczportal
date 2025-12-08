<div>
    <x-breadcrumbs :items="$breadcrumbs"    class="bg-base-300 p-3 rounded-box mt-2" />
    {{-- Do your work, then step back. --}}

     <x-card title="Other Application" separator class="mt-5 border-2 border-gray-200">
      <table class="table table-compact">
        <tr>
            <td>Other Service</td>
            <td>{{ $otherapplication->otherservice->name }}</td>
        </tr>
        <tr>
            <td>Period</td>
            <td>{{ $otherapplication->period }}</td>
        </tr>
        <tr>
            <td>Status</td>
            <td>{{ $otherapplication->status }}</td>
        </tr>
      </table>
     </x-card>

     <x-card title="Required Documents" separator class="mt-5 border-2 border-gray-200">
        <table class="table table-compact">
            <thead></thead>
                <tr>
                    <th>Document</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($uploaddocuments as $uploaddocument)
                <tr>
                    <td>{{ $uploaddocument["document_name"] }}</td>
                    <td><div class="{{ $uploaddocument["upload"] ? "text-green-500" : "text-red-500" }}">{{ $uploaddocument["upload"] ? "Uploaded" : "Not uploaded" }}</div></td>
                    <td>
                        <div class="flex items-center justify-end space-x-2">
                        @if($uploaddocument["upload"])
                        <x-button icon="o-trash" label="Remove" class="btn-sm btn-error " wire:click="removedocument({{ $uploaddocument['id'] }})" spinner />
                        <x-button icon="o-document-magnifying-glass" label="View" class="btn-sm btn-info " wire:click="viewdocument('{{ $uploaddocument['file'] }}')" spinner />
                        @else
                        <x-button icon="o-arrow-up-tray" label="Upload" class="btn-sm btn-primary " wire:click="openuploaddocument({{ $uploaddocument['otherservicedocument_id'] }},{{ $uploaddocument['document_id'] }})" spinner />
                        @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center">No documents found</td>
                </tr>
            </tbody>
            @endforelse
        </table>
     </x-card>
     <x-card title="Invoice details" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>

         
        </x-slot:menu>
        <livewire:admin.components.walletbalances :customer="$otherapplication->customer" />
            <table class="table table-compact">
                <thead>
                    <tr>
                        <th>Invoice Number</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
            <tr>
                <td>Invoice Number</td>
                <td>{{ $invoice->invoice_number }}</td>
            </tr>
            <tr>
                <td>Amount</td>
                <td>{{ $invoice->currency->name }} {{ $invoice->amount }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td>{{ $invoice->status }}</td>
            </tr>
            </tbody>
        </table>
        @if($invoice->status != "PAID")
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2">
        <livewire:admin.components.receipts :invoice="$invoice" />
        <livewire:admin.components.attachpop :invoice="$invoice" />
        </div>
        @endif
     </x-card>
     <x-modal wire:model="uploadmodal" title="Upload Document" separator>
        <x-form wire:submit="uploaddocument">
        <x-file wire:model.live="file" label="Upload Document" accept="application/pdf" />
        <x-slot:actions>
            <x-button label="Upload"  class="btn btn-primary" type="submit" spinner="uploaddocument" />
        </x-slot:actions>
        </x-form>
     </x-modal>
     <x-modal wire:model="documentview" title="View Document" box-class="max-w-4xl h-screen" separator class="backdrop-blur">
        <iframe src="{{$documenturl}}" class="w-full h-screen"></iframe>
    </x-modal>
</div>
