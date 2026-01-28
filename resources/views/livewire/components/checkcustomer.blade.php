<div>
    {{-- Show pending approval status --}}
    @if($hasPendingApproval)
        <x-alert class="alert-warning mt-5" icon="o-clock">
            <p class="mt-2">Your historical registration data is currently awaiting admin approval. Please wait while we review your submission.</p>
            <div class="mt-4 space-y-2">
                <p class="font-semibold">Pending Submissions:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach($pendingSubmissions as $submission)
                        @foreach($submission->professions as $profession)
                            <li>
                                {{ $profession->profession->name ?? 'N/A' }} - 
                                Registration Number: {{ $profession->registrationnumber }} - 
                                Submitted: {{ $submission->created_at->format('M d, Y') }}
                            </li>
                        @endforeach
                    @endforeach
                </ul>
            </div>
            <p class="mt-4 text-sm text-gray-600">You will be notified once your registration has been reviewed and approved.</p>
        </x-alert>
    @endif

    <x-modal title="Customer Registration" wire:model="modal" box-class="max-w-6xl" persistent separator>
        {{-- Step 1: Valid Certificate Question --}}
        @if($currentStep == 1)
            <div class="space-y-4">
                <p class="text-lg font-semibold">Do you have a valid registration certificate?</p>
                <div class="flex gap-4">
                    <x-button label="Yes" wire:click="setHasValidCertificate(1)" class="btn-primary" />
                    <x-button label="No" wire:click="setHasValidCertificate(0)" class="btn-secondary" />
                </div>
            </div>
        @endif

        {{-- Step 2: National ID Input --}}
        @if($currentStep == 2)
            <x-form wire:submit="searchCustomer">
                <div class="space-y-4">
                    <p class="text-lg font-semibold">Please enter your National ID to search for your customer record</p>
                    <x-input label="National ID" wire:model="nationalID" placeholder="Enter your National ID" required />
                    <x-slot:actions>
                        <x-button label="Search" type="submit" class="btn-primary" spinner="searchCustomer" />
                        <x-button label="Back" wire:click="$set('currentStep', 1)" class="btn-secondary" />
                    </x-slot:actions>
                </div>
            </x-form>
        @endif

        {{-- Step 3: Customer Found Confirmation --}}
        @if($currentStep == 3 && $foundCustomer)
            <div class="space-y-4">
                <p class="text-lg font-semibold">We found your customer record. Please confirm the details:</p>
                <div class="bg-gray-50 p-4 rounded-lg space-y-2">
                    <p><strong>Name:</strong> {{ $foundCustomer->name }}</p>
                    <p><strong>Surname:</strong> {{ $foundCustomer->surname }}</p>
                    <p><strong>National ID:</strong> {{ $foundCustomer->identificationnumber }}</p>
                    <p><strong>Date of Birth:</strong> {{ $foundCustomer->dob }}</p>
                    <p><strong>Gender:</strong> {{ $foundCustomer->gender }}</p>
                    @if($foundCustomer->email)
                        <p><strong>Email:</strong> {{ $foundCustomer->email }}</p>
                    @endif
                </div>
                <div class="flex gap-4">
                    <x-button label="Confirm" wire:click="confirmCustomer" class="btn-primary" spinner="confirmCustomer" />
                    <x-button label="Cancel" wire:click="$set('currentStep', 2)" class="btn-secondary" />
                </div>
            </div>
        @endif

        {{-- Step 4: Historical Data Capture --}}
        @if($currentStep == 4)
            <x-form wire:submit="submitHistoricalData">
                <div class="space-y-4">
                    <p class="text-lg font-semibold">Please provide your historical registration information:</p>
                    
                    <div class="grid lg:grid-cols-3 gap-4">
                        <x-input label="Name" wire:model="historicalName" required />
                        <x-input label="Surname" wire:model="historicalSurname" required />
                        <x-select label="Gender" wire:model="historicalGender" :options="[['id'=>'MALE','label'=>'Male'], ['id'=>'FEMALE','label'=>'Female']]" option-label="label" option-value="id" placeholder="Select Gender" required />
                       
                        <x-input label="Date of Birth" wire:model="historicalDOB" type="date" required />
                        <x-select label="Identity Type" wire:model="historicalIdentityType" :options="[['id'=>'NATIONAL_ID','label'=>'National ID'], ['id'=>'PASSPORT','label'=>'Passport']]" option-label="label" option-value="id" placeholder="Select Identity Type" required />
                        <x-input label="Identity Number" wire:model="historicalIdentityNumber" required />
                        <x-select label="Nationality" wire:model.live="historicalNationalityId" :options="$nationalities" option-label="name" option-value="id" placeholder="Select Nationality" required />
                        <x-input label="Address" wire:model="historicalAddress" required />
                        <x-input label="Place of Birth" wire:model="historicalPlaceOfBirth" required />
                        <x-input label="Phone" wire:model="historicalPhone" required />
                    </div>

                    <div class="border-t pt-4">
                        <div class="flex justify-between items-center mb-4">
                            <p class="font-semibold">Professions</p>
                            <x-button wire:click="addHistoricalProfession" label="Add Profession" class="btn-secondary" />
                        </div>

                        @foreach($historicalProfessions as $index => $profession)
                            <div class="border p-4 rounded-lg mb-4 space-y-4">
                                <div class="flex justify-between items-center">
                                    <h4 class="font-semibold">Profession {{ $index + 1 }}</h4>
                                    @if(count($historicalProfessions) > 1)
                                        <x-button wire:click="removeHistoricalProfession({{ $index }})" icon="o-trash" class="btn-error btn-sm" />
                                    @endif
                                </div>

                                <div class="grid lg:grid-cols-3 gap-4">
                                    <x-select 
                                        label="Profession" 
                                        wire:model.live="historicalProfessions.{{ $index }}.profession_id" 
                                        :options="$professions" 
                                        option-label="name" 
                                        option-value="id" 
                                        placeholder="Select Profession" 
                                        required 
                                    />
                                    @php
                                        $selectedProfessionId = $historicalProfessions[$index]['profession_id'] ?? null;
                                        $tires = $selectedProfessionId ? $this->getTiresForProfession($selectedProfessionId) : [];
                                    @endphp
                                    <x-select 
                                        label="Tier" 
                                        wire:model="historicalProfessions.{{ $index }}.tire_id" 
                                        :options="$tires" 
                                        option-label="name" 
                                        option-value="id" 
                                        placeholder="Select Tier" 
                                        required
                                        :disabled="!$selectedProfessionId"
                                    />
                                    <x-input 
                                        label="Registration Number" 
                                        wire:model="historicalProfessions.{{ $index }}.registration_number" 
                                        required 
                                    />
                                    <x-input 
                                        label="Registration Year" 
                                        wire:model="historicalProfessions.{{ $index }}.registration_year" 
                                        type="number" 
                                        required 
                                    />
                                    <x-input 
                                    label="Last Renewal Year" 
                                    wire:model.live="historicalProfessions.{{ $index }}.last_renewal_year" 
                                    type="number" 
                                    placeholder="e.g., 2023"
                                />
                                <x-input 
                                    label="Last Renewal Expire Date" 
                                    wire:model="historicalProfessions.{{ $index }}.last_renewal_expire_date" 
                                    type="date" 
                                    readonly
                                    hint="Automatically calculated as December 31st of the renewal year"
                                />
                                    <x-input 
                                        label="Last Renewal Practising Certificate Number" 
                                        wire:model="historicalProfessions.{{ $index }}.practising_certificate_number" 
                                        required 
                                    />
                                    <x-select 
                                        label="Register Type" 
                                        wire:model="historicalProfessions.{{ $index }}.registertype_id" 
                                        :options="$registertypes" 
                                        option-label="name" 
                                        option-value="id" 
                                        placeholder="Select Register Type" 
                                        required 
                                    />
                               
                                    <x-input 
                                        label="Total CDP Points on Last Renewal Year" 
                                        wire:model="historicalProfessions.{{ $index }}.last_renewal_year_cdp_points" 
                                        type="number" 
                                        placeholder="e.g., 50"
                                    />
                                </div>

                                <div class="border-t pt-4">
                                    <p class="font-semibold mb-2">Attach Certificates for this Profession <span class="text-red-500">*</span></p>
                                    <p class="text-sm text-gray-600 mb-4">Both Registration Certificate and Practising Certificate are required.</p>
                                    
                                    @if(isset($profession['certificates']))
                                        @foreach($profession['certificates'] as $certIndex => $certificate)
                                            <div class="flex gap-2 mb-2">
                                                <x-input 
                                                    type="file" 
                                                    wire:model="historicalProfessions.{{ $index }}.certificates.{{ $certIndex }}" 
                                                    :label="$certIndex == 0 ? 'Registration Certificate *' : ($certIndex == 1 ? 'Practising Certificate *' : 'Additional Certificate ' . ($certIndex - 1))"
                                                    :required="$certIndex < 2"
                                                />
                                                <x-input 
                                                    wire:model="historicalProfessions.{{ $index }}.descriptions.{{ $certIndex }}" 
                                                    label="Description" 
                                                    :placeholder="$certIndex == 0 ? 'Registration Certificate' : ($certIndex == 1 ? 'Practising Certificate' : 'e.g., Additional Certificate')"
                                                    :readonly="$certIndex < 2"
                                                />
                                                @if(count($profession['certificates']) > 2 && $certIndex >= 2)
                                                    <x-button 
                                                        wire:click="removeHistoricalCertificate({{ $index }}, {{ $certIndex }})" 
                                                        icon="o-trash" 
                                                        class="btn-error mt-8" 
                                                    />
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif
                                    <x-button 
                                        wire:click="addHistoricalCertificate({{ $index }})" 
                                        label="Add Additional Certificate" 
                                        class="btn-secondary" 
                                    />
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <x-slot:actions>
                        <x-button label="Submit for Approval" type="submit" class="btn-primary" spinner="submitHistoricalData" />
                        @if($hasValidCertificate === 1)
                            <x-button label="Back" wire:click="$set('currentStep', 2)" class="btn-secondary" />
                        @else
                            <x-button label="Back" wire:click="$set('currentStep', 1)" class="btn-secondary" />
                        @endif
                    </x-slot:actions>
                </div>
            </x-form>
        @endif

        {{-- Step 5: Update Personal Details --}}
        @if($currentStep == 5)
            @if($hasValidCertificate === 0)
                <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-800">
                        <strong>Note:</strong> Since you don't have a valid registration certificate, you can create your customer account directly. 
                        A new registration number will be automatically generated for you.
                    </p>
                </div>
            @endif
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

                    @if($nationality_id != 230)
                        <x-select label="City" wire:model="city_id" :options="$cities" option-label="name" option-value="id" placeholder="Select City" disabled/>
                        <x-select label="Province" wire:model="province_id" :options="$provinces" option-label="name" option-value="id" placeholder="Select Province" disabled/>
                    @else
                        <x-select label="Province" wire:model.live="province_id" :options="$provinces" option-label="name" option-value="id" placeholder="Select Province"/>
                        <x-select label="City" wire:model.live="city_id" :options="$cities->where('province_id', $province_id)" option-label="name" option-value="id" placeholder="Select City"/>
                    @endif
                         
                    <x-input label="Address" wire:model="address" />
                    <x-input label="Place of Birth" wire:model="placeofbirth" />
                    <x-input label="Email" wire:model="email" readonly/>
                    <x-input label="Phone" wire:model="phone" />
                    <x-input label="Profile Picture" wire:model="profile" type="file" accept="image/*" />
                </div>
                <x-slot:actions>
                    <x-button label="Submit" type="submit" class="btn-primary" spinner="register" />
                    @if($hasValidCertificate === 0)
                        <x-button label="Back" wire:click="$set('currentStep', 1)" class="btn-secondary" />
                    @endif
                </x-slot:actions>
            </x-form>
        @endif
    </x-modal>
</div>
