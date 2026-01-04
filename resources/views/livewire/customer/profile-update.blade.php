<div>
    <x-breadcrumbs :items="[
        [
            'label' => 'Dashboard',
            'icon' => 'o-home',
            'link' => route('dashboard'),
        ],
        [
            'label' => 'Profile Settings',
        ]
    ]" class="bg-base-300 p-3 rounded-box mt-2" />
    
    <x-card title="Profile Settings" separator class="mt-5 border-2 border-gray-200">
        <x-form wire:submit="save">
            <div class="grid lg:grid-cols-3 gap-4">
                <x-input label="Name" wire:model="name" />
                <x-input label="Surname" wire:model="surname" />
                <x-input label="Previous Name" wire:model="previousname" />
                <x-input label="Date of Birth" wire:model="dob" type="date" />
                <x-select label="Gender" wire:model="gender" :options="[['id'=>'MALE','label'=>'Male'], ['id'=>'FEMALE','label'=>'Female']]" option-label="label" option-value="id" placeholder="Select Gender" />
                <x-select label="Marital Status" wire:model="maritalstatus" :options="[['id'=>'SINGLE','label'=>'Single'], ['id'=>'MARRIED','label'=>'Married'], ['id'=>'DIVORCED','label'=>'Divorced'], ['id'=>'WIDOWED','label'=>'Widowed']]" option-label="label" option-value="id" placeholder="Select Marital Status" />
                <x-select label="Identity Type" wire:model="identitytype" :options="[['id'=>'NATIONAL_ID','label'=>'National ID'], ['id'=>'PASSPORT','label'=>'Passport']]" option-label="label" option-value="id" placeholder="Select Identity Type" />
                <x-input label="Identity Number" wire:model="identitynumber" />
                <x-select label="Nationality" wire:model.live="nationality_id" :options="$nationalities" option-label="name" option-value="id" placeholder="Select Nationality" />

                @if($nationality_id != 230)
                <x-select label="City" wire:model="city_id" :options="$cities" option-label="name" option-value="id" placeholder="Select City" disabled/>
                <x-select label="Province" wire:model="province_id" :options="$provinces" option-label="name" option-value="id" placeholder="Select Province" disabled/>
                @else
                <x-select label="City" wire:model="city_id" :options="$cities" option-label="name" option-value="id" placeholder="Select City"/>
                <x-select label="Province" wire:model="province_id" :options="$provinces" option-label="name" option-value="id" placeholder="Select Province"/>
                @endif
                     
                <x-input label="Address" wire:model="address" />
                <x-input label="Place of Birth" wire:model="placeofbirth" />
                <x-input label="Email" wire:model="email" type="email" />
                <x-input label="Phone" wire:model="phone" />
                <x-select label="Employment Status" wire:model="employmentstatus_id" :options="$employmentstatuses" option-label="name" option-value="id" placeholder="Select Employment Status" />
                <x-select label="Employment Location" wire:model="employmentlocation_id" :options="$employmentlocations" option-label="name" option-value="id" placeholder="Select Employment Location" />
                <x-input label="Profile Picture" wire:model="profile" type="file" accept="image/*" hint="Max size: 2MB. Supported formats: JPEG, PNG, JPG, GIF" />
            </div>

            <x-slot:actions>
                <x-button label="Cancel" link="{{ route('dashboard') }}" class="btn-outline" />
                <x-button label="Save" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-card>
</div>










