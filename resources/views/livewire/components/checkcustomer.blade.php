<div>
<x-modal title="Update personal details" wire:model="modal" box-class="max-w-6xl" persistent separator>
    <x-form wire:submit="register">
        <div class="grid lg:grid-cols-3 gap-2">
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
          <x-select label="City" wire:model.live="city_id" :options="$cities->where('province_id', $province_id)" option-label="name" option-value="id" placeholder="Select City"/>
     
        @endif
             
        <x-input label="Address" wire:model="address" />
        <x-input label="Place of Birth" wire:model="placeofbirth" />
        <x-input label="Email" wire:model="email"  readonly/>
        <x-input label="Phone" wire:model="phone" />
            
        </div>
        <div >
            <x-checkbox wire:model.live="signup_type" class="self-start">
                <x-slot:label>
                   Tick here if you are an existing customer with a valid registration number?
                </x-slot:label>
            </x-checkbox>
           
            @if($signup_type == 1)
            <div>
                <x-input label="Please enter your current registration number" wire:model="registration_number" />
            </div>
            @endif
        </div>
        <x-slot:actions>
            <x-button label="Submit" type="submit" class="btn-primary" spinner="register" />
        </x-slot:actions>
    </x-form>
  
</x-modal>
</div>
