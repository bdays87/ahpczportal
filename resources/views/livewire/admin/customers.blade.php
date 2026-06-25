<div>
    <x-breadcrumbs :items="$breadcrumbs"    class="bg-base-300 p-3 rounded-box mt-2" />
    @can('customers.access')
    <x-card title="Customers" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            <x-input placeholder="Search" wire:model.live="search" />
            @can('customers.modify')
            <x-button label="Upload Excel" responsive icon="o-arrow-up-tray" class="btn-outline" @click="$wire.importmodal = true" />
            <x-button label="New" responsive icon="o-plus" class="btn-primary" @click="$wire.modal = true" />
            @endcan
        </x-slot:menu>

        <x-table :headers="$headers" :rows="$customers" with-pagination>
            @scope('cell_profile',$customer)
            <img src="@fileurl($customer->profile, '/imgs/noimage.jpg')" class="w-12 h-12 rounded-full" alt="">
            @endscope
            @scope('actions', $customer)
            @can('customers.modify')
            <div class="flex items-center space-x-2">
                @can('customers.modify')
                <x-button icon="o-pencil" class="btn-sm btn-info btn-outline" 
                    wire:click="edit({{ $customer->id }})" spinner />
                <x-button icon="o-trash" class="btn-sm btn-outline btn-error" 
                    wire:click="delete({{ $customer->id }})" wire:confirm="Are you sure?" spinner />
                @endcan
                @can('customers.access')
                <x-button icon="o-eye" class="btn-sm btn-info btn-outline" 
                    link="{{ route('customers.show', $customer->uuid) }}" spinner />
                @endcan
            </div>
            @endcan
            @endscope
            <x-slot:empty>
                <x-alert class="alert-error" title="No customers found." />
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

    <x-modal wire:model="importmodal" title="Import Customers from Excel" box-class="max-w-xl">
        <div x-data="{ uploading: false }"
             x-on:livewire-upload-start="uploading = true"
             x-on:livewire-upload-finish="uploading = false"
             x-on:livewire-upload-error="uploading = false">
        <x-form wire:submit="saveexcelimport">
            <p class="text-sm text-gray-600 mb-3">
                Upload the <strong>customers.xlsx</strong> file. The system reads the Name, Customer (registration number),
                Title, E-mail, Physical Address 1 and Contact Person columns. Registration numbers ending in a letter are
                trimmed (e.g. 511285E &rarr; 511285), rows starting with "L" and customers that already exist are skipped.
            </p>
            <x-input label="Excel File (.xlsx)" wire:model="excelfile" type="file" accept=".xlsx" />
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
