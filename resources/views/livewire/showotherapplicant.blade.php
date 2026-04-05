<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />

    {{-- Application details --}}
    <x-card title="Other Application" separator class="mt-5 border-2 border-gray-200">
        <table class="table table-compact">
            <tr><td>Other Service</td><td>{{ $otherapplication->otherservice->name }}</td></tr>
            <tr><td>Period</td><td>{{ $otherapplication->period }}</td></tr>
            <tr><td>Status</td><td>
                <x-badge value="{{ $otherapplication->status }}"
                    class="{{ $otherapplication->status == 'APPROVED' ? 'badge-success' : ($otherapplication->status == 'REJECTED' ? 'badge-error' : 'badge-warning') }}" />
            </td></tr>
            @if($otherapplication->tradename)
            <tr><td>Trade Name</td><td>{{ $otherapplication->tradename }}</td></tr>
            @endif
        </table>
    </x-card>

    {{-- Required Documents --}}
    <x-card title="Required Documents" separator class="mt-5 border-2 border-gray-200">
        <table class="table table-compact">
            <thead>
                <tr><th>Document</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
                @forelse ($uploaddocuments as $uploaddocument)
                <tr>
                    <td>{{ $uploaddocument['document_name'] }}</td>
                    <td><span class="{{ $uploaddocument['upload'] ? 'text-green-500' : 'text-red-500' }}">
                        {{ $uploaddocument['upload'] ? 'Uploaded' : 'Not uploaded' }}
                    </span></td>
                    <td>
                        <div class="flex items-center justify-end space-x-2">
                            @if($uploaddocument['upload'])
                            <x-button icon="o-document-magnifying-glass" label="View" class="btn-sm btn-info btn-outline" wire:click="viewdocument('{{ $uploaddocument['file'] }}')" spinner />
                            <x-button icon="o-trash" label="Remove" class="btn-sm btn-error btn-outline" wire:click="removedocument({{ $uploaddocument['id'] }})" spinner />
                            @else
                            <x-button icon="o-arrow-up-tray" label="Upload" class="btn-sm btn-primary btn-outline" wire:click="openuploaddocument({{ $uploaddocument['otherservicedocument_id'] }},{{ $uploaddocument['document_id'] }})" spinner />
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center">No documents required</td></tr>
                @endforelse
            </tbody>
        </table>
    </x-card>

    {{-- Institution Services (only if isinstitution == 'Y') --}}
    @if($otherapplication->otherservice->isinstitution == 'Y')

    <x-card title="Institution Services" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            <x-button icon="o-plus" label="Add Service" class="btn-sm btn-primary btn-outline" wire:click="$set('servicemodal', true)" />
        </x-slot:menu>
        <table class="table table-zebra">
            <thead>
                <tr><th>Service Name</th><th>Description</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($otherapplication->instservices as $service)
                <tr>
                    <td>{{ $service->name }}</td>
                    <td>{{ $service->description ?? '—' }}</td>
                    <td class="text-right">
                        <x-button icon="o-trash" class="btn-sm btn-error btn-outline"
                            wire:click="removeservice({{ $service->id }})"
                            wire:confirm="Remove this service?"
                            spinner />
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center text-gray-400 p-4">No services added yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </x-card>

    <x-card title="Institution Employees" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            <x-button icon="o-plus" label="Add Practitioner" class="btn-sm btn-primary btn-outline" wire:click="$set('employeemodal', true)" />
        </x-slot:menu>
        <table class="table table-zebra">
            <thead>
                <tr><th>Practitioner</th><th>Reg No</th><th>Employment Type</th><th>Date Employed</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($otherapplication->instcustomers as $emp)
                <tr>
                    <td>{{ $emp->customer->name }} {{ $emp->customer->surname }}</td>
                    <td>{{ $emp->customer->regnumber ?? '—' }}</td>
                    <td>{{ $emp->employmenttype }}</td>
                    <td>{{ $emp->date_employed ?? '—' }}</td>
                    <td class="text-right">
                        <x-button icon="o-trash" class="btn-sm btn-error btn-outline"
                            wire:click="removeemployee({{ $emp->id }})"
                            wire:confirm="Remove this practitioner?"
                            spinner />
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-gray-400 p-4">No practitioners added yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </x-card>

    @endif

    {{-- Invoice --}}
    <x-card title="Invoice Details" separator class="mt-5 border-2 border-gray-200">
        <livewire:admin.components.walletbalances :customer="$otherapplication->customer" />
        <table class="table table-compact mt-2">
            <tr><td>Invoice Number</td><td>{{ $invoice->invoice_number }}</td></tr>
            <tr><td>Amount</td><td>{{ $invoice->currency->name }} {{ $invoice->amount }}</td></tr>
            <tr><td>Status</td><td>{{ $invoice->status }}</td></tr>
        </table>
        @if($invoice->status != 'PAID')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2">
            <livewire:admin.components.receipts :invoice="$invoice" />
            <livewire:admin.components.attachpop :invoice="$invoice" />
        </div>
        @endif
    </x-card>

    {{-- Upload Document Modal --}}
    <x-modal wire:model="uploadmodal" title="Upload Document" separator>
        <x-form wire:submit="uploaddocument">
            <x-file wire:model.live="file" label="Upload Document" accept="application/pdf" />
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.uploadmodal = false" />
                <x-button label="Upload" class="btn-primary" type="submit" spinner="uploaddocument" />
            </x-slot:actions>
        </x-form>
    </x-modal>

    {{-- View Document Modal --}}
    <x-modal wire:model="documentview" title="View Document" box-class="max-w-4xl h-screen" separator>
        <iframe src="{{ $documenturl }}" class="w-full h-screen"></iframe>
    </x-modal>

    {{-- Add Service Modal --}}
    <x-modal wire:model="servicemodal" title="Add Institution Service" separator>
        <x-form wire:submit="saveservice">
            <x-input label="Service Name" wire:model="service_name" placeholder="e.g. Laboratory Testing" />
            <x-textarea label="Description" wire:model="service_description" placeholder="Optional description" />
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.servicemodal = false" />
                <x-button label="Save" class="btn-primary" type="submit" spinner="saveservice" />
            </x-slot:actions>
        </x-form>
    </x-modal>

    {{-- Add Employee Modal --}}
    <x-modal wire:model="employeemodal" title="Add Practitioner" separator>
        <x-form wire:submit="saveemployee">
            <div class="relative">
                <x-input label="Search Practitioner" wire:model.live="customer_search" placeholder="Type name or reg number..." />
                @if(count($customer_results) > 0)
                <div class="absolute z-50 w-full bg-white border border-gray-200 rounded-lg shadow-lg mt-1 max-h-48 overflow-y-auto">
                    @foreach($customer_results as $result)
                    <div class="px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm"
                        wire:click="selectcustomer({{ $result['id'] }})">
                        {{ $result['name'] }} {{ $result['surname'] }}
                        @if($result['regnumber'] ?? null) — {{ $result['regnumber'] }} @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            <x-select label="Employment Type" wire:model="employmenttype"
                :options="[['id'=>'CONTRACT','name'=>'Contract'],['id'=>'PERMANENT','name'=>'Permanent'],['id'=>'PARTTIME','name'=>'Part Time']]"
                option-label="name" option-value="id" />
            <x-input label="Date Employed" wire:model="date_employed" type="date" />
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.employeemodal = false" />
                <x-button label="Add" class="btn-primary" type="submit" spinner="saveemployee" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
