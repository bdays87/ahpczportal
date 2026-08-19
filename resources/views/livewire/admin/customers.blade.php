<div>
    <x-breadcrumbs :items="$breadcrumbs"    class="bg-base-300 p-3 rounded-box mt-2" />
    @can('customers.access')
    <x-card separator class="mt-5 border-2 border-gray-200">
        <x-slot:title>
            <div class="flex items-center gap-2">
                <span>Customers</span>
                <span class="badge badge-primary badge-outline font-semibold">{{ $customers->total() }}</span>
            </div>
        </x-slot:title>
        <x-slot:menu>
            <x-input placeholder="Search name, reg number, profession..." icon="o-magnifying-glass" wire:model.live.debounce.400ms="search" clearable />
            <x-button label="Clear Filters" responsive icon="o-arrow-path" class="btn-ghost" wire:click="clearfilters" />
            <x-button label="Export CSV" responsive icon="o-arrow-down-tray" class="btn-outline" wire:click="exportcsv" spinner="exportcsv" />
            @can('customers.modify')
            <x-button label="Upload Excel" responsive icon="o-arrow-up-tray" class="btn-outline" @click="$wire.importmodal = true" />
            <x-button label="New" responsive icon="o-plus" class="btn-primary" @click="$wire.modal = true" />
            @endcan
        </x-slot:menu>

        {{-- Filters --}}
        <div class="bg-base-200 p-4 rounded-box mb-4">
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <x-select wire:model.live="filter_gender" label="Gender"
                    :options="[['id'=>'MALE','name'=>'Male'], ['id'=>'FEMALE','name'=>'Female']]"
                    option-label="name" option-value="id" placeholder="All Genders" icon="o-user" />

                <x-select wire:model.live="filter_province_id" label="Province" :options="$provinces"
                    option-label="name" option-value="id" placeholder="All Provinces" icon="o-map-pin" />

                <x-select wire:model.live="filter_city_id" label="City" :options="$filtercities"
                    option-label="name" option-value="id" placeholder="All Cities" icon="o-map" />

                <x-select wire:model.live="filter_profession_id" label="Profession" :options="$professions"
                    option-label="name" option-value="id" placeholder="All Professions" icon="o-briefcase" />

                <x-select wire:model.live="filter_registertype_id" label="Register Type" :options="$registertypes"
                    option-label="name" option-value="id" placeholder="All Register Types" icon="o-document-text" />

                <x-select wire:model.live="filter_compliant" label="Compliance" :options="$complianceoptions"
                    option-label="name" option-value="id" placeholder="All" icon="o-shield-check" />
            </div>
        </div>

        <x-table :headers="$headers" :rows="$customers" with-pagination striped class="[&_th]:!bg-base-200/70 [&_th]:!font-semibold [&_td]:align-top
            [&_thead_th:last-child]:sticky [&_thead_th:last-child]:right-0 [&_thead_th:last-child]:z-20 [&_thead_th:last-child]:!bg-base-200
            [&_tbody_td:last-child]:sticky [&_tbody_td:last-child]:right-0 [&_tbody_td:last-child]:z-10 [&_tbody_td:last-child]:!bg-base-100
            [&_tbody_td:last-child]:shadow-[-4px_0_6px_-2px_rgba(0,0,0,0.08)]">
            @scope('cell_profile',$customer)
            <div class="flex items-center gap-3">
                <img src="@fileurl($customer->profile, '/imgs/noimage.jpg')"
                    class="w-8 h-8 shrink-0 rounded-full object-cover bg-base-200 ring ring-primary/20 ring-offset-1 ring-offset-base-100" alt="">
                <div>
                    <div class="font-semibold">{{ $customer->name }} {{ $customer->surname }}</div>
                    <div class="text-xs text-gray-500">{{ $customer->nationality->name ?? '—' }}</div>
                </div>
            </div>
            @endscope
            @scope('cell_regnumber',$customer)
            <span class="badge badge-neutral badge-outline font-mono">{{ $customer->regnumber ?? '—' }}</span>
            @endscope
            @scope('cell_gender',$customer)
            @if($customer->gender)
            <span class="badge badge-sm {{ strtoupper($customer->gender) == 'FEMALE' ? 'badge-secondary' : 'badge-info' }} badge-outline">{{ ucfirst(strtolower($customer->gender)) }}</span>
            @else
            <span class="text-gray-400">—</span>
            @endif
            @endscope
            @scope('cell_identificationnumber',$customer)
            {{ $customer->identificationnumber ?? '—' }}
            @endscope
            @scope('cell_dob',$customer)
            {{ $customer->dob ? \Carbon\Carbon::parse($customer->dob)->format('d M Y') : '—' }}
            @endscope
            @scope('cell_profession',$customer)
            @if($customer->customerprofessions->isNotEmpty())
            <div class="flex flex-col gap-1">
                @foreach($customer->customerprofessions->unique('profession_id') as $cp)
                <span class="inline-block text-xs leading-snug px-2 py-1 rounded-md border border-gray-300 bg-base-100 whitespace-normal break-words">{{ $cp->profession->name ?? 'N/A' }}</span>
                @endforeach
            </div>
            @else
            <span class="text-gray-400">—</span>
            @endif
            @endscope
            @scope('cell_registertype',$customer)
            @if($customer->customerprofessions->isNotEmpty())
            <div class="flex flex-col gap-1">
                @foreach($customer->customerprofessions->unique('registertype_id') as $cp)
                <span class="inline-block text-xs leading-snug px-2 py-1 rounded-md bg-base-200 whitespace-normal break-words">{{ $cp->registertype->name ?? 'N/A' }}</span>
                @endforeach
            </div>
            @else
            <span class="text-gray-400">—</span>
            @endif
            @endscope
            @scope('cell_compliance',$customer)
            @php($compliance = $customer->compliance_label)
            <span class="badge badge-sm {{ $compliance == 'Compliant' ? 'badge-success' : ($compliance == 'Non-compliant' ? 'badge-error' : 'badge-ghost') }}">{{ $compliance }}</span>
            @endscope
            @scope('actions', $customer)
            <div class="flex items-center justify-end gap-2">
                @can('customers.access')
                <x-button icon="o-eye" class="btn-sm btn-info btn-outline" tooltip="View profile"
                    link="{{ route('customers.show', $customer->uuid) }}" spinner />
                @endcan
                @can('customers.modify')
                <x-button icon="o-pencil" class="btn-sm btn-warning btn-outline" tooltip="Edit"
                    wire:click="edit({{ $customer->id }})" spinner />
                <x-button icon="o-trash" class="btn-sm btn-outline btn-error" tooltip="Delete customer and all linked records"
                    wire:click="delete({{ $customer->id }})"
                    wire:confirm="This will permanently delete this customer's profile, their user account, and every application, registration, payment and document linked to them. This cannot be undone. Continue?" spinner />
                @endcan
            </div>
            @endscope
            <x-slot:empty>
                <div class="flex flex-col items-center justify-center py-10 gap-2 text-gray-400">
                    <x-icon name="o-users" class="w-10 h-10" />
                    <span>No customers found{{ $search ? " for \"{$search}\"" : '' }}.</span>
                </div>
            </x-slot:empty>
        </x-table>
    </x-card>
    @else
    <x-alert class="alert-error mt-3" title="You do not have permission to view customers." />
    @endcan

    <x-modal wire:model="modal"  title="{{ $id ? 'Edit Customer' : 'New Customer' }}" box-class="max-w-4xl">
        <div x-data="{ uploading: false }"
             x-on:livewire-upload-start="uploading = true"
             x-on:livewire-upload-finish="uploading = false"
             x-on:livewire-upload-error="uploading = false">
        <x-form wire:submit="{{ $id ? 'update' : 'save' }}">
            <div class="grid grid-cols-3 gap-2">
            <x-input label="Name" wire:model="name" />
            <x-input label="Surname" wire:model="surname" />
            <x-input label="Previous Name" wire:model="previousname" />
            <x-input label="Date of Birth" wire:model="dob" type="date" />
            <x-select label="Gender" wire:model="gender" :options="[['id'=>'MALE','label'=>'Male'], ['id'=>'FEMALE','label'=>'Female']]" option-label="label" option-value="id" placeholder="Select Gender" />
            <x-select label="Marital Status" wire:model="maritalstatus" :options="[['id'=>'SINGLE','label'=>'Single'], ['id'=>'MARRIED','label'=>'Married'], ['id'=>'DIVORCED','label'=>'Divorced'], ['id'=>'WIDOWED','label'=>'Widowed']]" option-label="label" option-value="id" placeholder="Select Marital Status" />
            <x-select label="Identity Type" wire:model="identitytype" :options="[['id'=>'NATIONAL_ID','label'=>'National ID'], ['id'=>'PASSPORT','label'=>'Passport']]" option-label="label" option-value="id" placeholder="Select Identity Type" />
            <x-input label="Identity Number" wire:model="identitynumber" />
            <x-select label="Nationality" wire:model.live="nationality_id" :options="$nationalities" option-label="name" option-value="id" placeholder="Select Nationality" />

            @if($nationality_id !=230)
            <x-select label="City" wire:model="city_id" :options="$cities" option-label="name" option-value="id" placeholder="Select City" disabled/>
            <x-select label="Province" wire:model="province_id" :options="$provinces" option-label="name" option-value="id" placeholder="Select Province" disabled/>
            @else
            
            <x-select label="Province" wire:model.live="province_id" :options="$provinces" option-label="name" option-value="id" placeholder="Select Province"/>
            <x-select label="City" wire:model="city_id" :options="$cities->where('province_id', $province_id)" option-label="name" option-value="id" placeholder="Select City"/>
            @endif
                 <x-select label="Employment Status" wire:model="employmentstatus_id" :options="$employmentstatuses" option-label="name" option-value="id" placeholder="Select Employment Status"/>
            <x-select label="Employment Location" wire:model="employmentlocation_id" :options="$employmentlocations" option-label="name" option-value="id" placeholder="Select Employment Location" />
            <div class="col-span-1">
                <x-input label="Email" wire:model.blur="email" type="email" />
                @error('email')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="col-span-1">
                <x-input label="Phone" wire:model.blur="phone" type="tel" placeholder="0771234567 or +263771234567" />
                @error('phone')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <x-input label="Address" wire:model="address" />
            <x-input label="Place of Birth" wire:model="placeofbirth" />
            <x-input label="Profile" wire:model="profile" type="file" />
            <p x-show="uploading" class="text-sm text-blue-600 mt-1 flex items-center gap-1">
                <span class="loading loading-spinner loading-xs"></span> Uploading file, please wait...
            </p>
            </div>
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.modal = false" wire:loading.attr="disabled" wire:target="save, update" />
                <x-button label="{{ $id ? 'Update' : 'Save' }}" type="submit" class="btn-primary" spinner="save"
                    wire:loading.attr="disabled" wire:target="save, update"
                    x-bind:disabled="uploading" />
            </x-slot:actions>
        </x-form>
        </div>
    </x-modal>

    <x-modal wire:model="importmodal" title="Import Customers from Excel or CSV" box-class="max-w-xl">
        <div x-data="{ uploading: false }"
             x-on:livewire-upload-start="uploading = true"
             x-on:livewire-upload-finish="uploading = false"
             x-on:livewire-upload-error="uploading = false">
        <x-form wire:submit="saveexcelimport">
            <p class="text-sm text-gray-600 mb-3">
                Upload the <strong>customers.xlsx</strong> or <strong>customers.csv</strong> file (same column order). The system reads the
                Name, Customer (registration number), Title, E-mail, Physical Address 1 and Contact Person columns. Registration numbers
                ending in a letter are trimmed (e.g. 511285E &rarr; 511285), rows starting with "L" and customers that already exist are skipped.
            </p>
            <x-input label="Excel or CSV File (.xlsx / .csv)" wire:model="excelfile" type="file" accept=".xlsx,.csv" />
            <p x-show="uploading" class="text-sm text-blue-600 mt-1 flex items-center gap-1">
                <span class="loading loading-spinner loading-xs"></span> Uploading file, please wait...
            </p>
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.importmodal = false" wire:loading.attr="disabled" wire:target="saveexcelimport" />
                <x-button label="Import" type="submit" class="btn-primary" spinner="saveexcelimport"
                    wire:loading.attr="disabled" wire:target="saveexcelimport"
                    x-bind:disabled="uploading" />
            </x-slot:actions>
        </x-form>
        </div>
    </x-modal>
    </div>
