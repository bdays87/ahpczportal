<div>
    <x-breadcrumbs :items="$breadcrumbs"    class="bg-base-300 p-3 rounded-box mt-2" />
    @can('customers.access')
    <x-card title="Customers" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            <x-input placeholder="Search" wire:model.live="search" />
            @can('customers.modify')
            <x-button label="New" responsive icon="o-plus" class="btn-primary" @click="$wire.modal = true" />
            @endcan
        </x-slot:menu>

        <x-table :headers="$headers" :rows="$customers" with-pagination>
            @scope('cell_profile',$customer)
            <img src="{{ $customer->profile ? '/storage/' . $customer->profile : '/imgs/noimage.jpg' }}" class="w-12 h-12 rounded-full" alt="">
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
            <x-input label="Email" wire:model="email" />
            <x-input label="Phone" wire:model="phone" />
            <x-input label="Address" wire:model="address" />
            <x-input label="Place of Birth" wire:model="placeofbirth" />
            <x-input label="Profile" wire:model="profile" type="file" />
            </div>
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.modal = false" />
                <x-button label="{{ $id ? 'Update' : 'Save' }}" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>
    </div>
