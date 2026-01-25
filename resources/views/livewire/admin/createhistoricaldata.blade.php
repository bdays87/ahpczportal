<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />
    
    <x-card title="Create Historical Data" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            <x-button label="Create Single Record" icon="o-plus" class="btn-primary" wire:click="openCreateModal" />
            <x-button label="Import from File" icon="o-arrow-down-tray" class="btn-secondary" wire:click="openImportModal" />
            <x-button label="Download Template" icon="o-document-arrow-down" class="btn-outline" wire:click="downloadTemplate" />
        </x-slot:menu>

        <div class="p-4 bg-blue-50 rounded-lg mb-4">
            <h3 class="font-semibold text-blue-900 mb-2">Instructions:</h3>
            <ul class="list-disc list-inside text-sm text-blue-800 space-y-1">
                <li>Use "Create Single Record" to add one historical data entry manually</li>
                <li>Use "Import from File" to bulk import multiple records from CSV or Excel</li>
                <li>Download the template file to see the required format for imports</li>
                <li>All created records will be sent for approval</li>
            </ul>
        </div>
    </x-card>

    {{-- Pending Records Table --}}
    <x-card title="My Pending Records ({{ $pendingRecords->count() }})" separator class="mt-5 border-2 border-gray-200">
        <table class="table table-zebra">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Surname</th>
                    <th>National ID</th>
                    <th>Email</th>
                    <th>Professions</th>
                    <th>Submitted</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pendingRecords as $record)
                <tr>
                    <td>{{ $record->name }}</td>
                    <td>{{ $record->surname }}</td>
                    <td>{{ $record->identificationnumber }}</td>
                    <td>{{ $record->user->email ?? 'N/A' }}</td>
                    <td>
                        @foreach($record->professions as $prof)
                            <span class="badge badge-info">{{ $prof->profession->name ?? 'N/A' }}</span>
                        @endforeach
                    </td>
                    <td>{{ $record->created_at->diffForHumans() }}</td>
                    <td>
                        <x-badge value="{{ $record->status }}" class="badge-warning" />
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="text-center text-gray-500 p-5 font-bold bg-gray-50">
                            No pending records found.
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </x-card>

    {{-- Create Single Record Modal --}}
    <x-modal title="Create Historical Data Record" wire:model="createmodal" box-class="max-w-6xl" separator>
        <x-form wire:submit.prevent="save">
            @if ($errors->any())
                <x-alert icon="o-exclamation-triangle" class="alert-error mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-alert>
            @endif
            <div class="space-y-6 overflow-y-auto max-h-[calc(90vh-200px)]">
                <div class="border-b pb-4">
                    <h3 class="font-semibold text-lg mb-4">Personal Information</h3>
                    <div class="grid lg:grid-cols-3 gap-4">
                        <x-input label="Name" wire:model="name" required />
                        <x-input label="Surname" wire:model="surname" required />
                        <x-input label="Email" wire:model="email" type="email" required />
                        <x-input label="Phone" wire:model="phone" required />
                        <x-select 
                            label="Gender" 
                            wire:model="gender" 
                            :options="[['id'=>'MALE','label'=>'Male'], ['id'=>'FEMALE','label'=>'Female'], ['id'=>'OTHER','label'=>'Other']]" 
                            option-label="label" 
                            option-value="id" 
                            placeholder="Select Gender" 
                            required 
                        />
                        <x-input label="Identification Number" wire:model="identificationnumber" required />
                        <x-input label="Date of Birth" wire:model="dob" type="date" required />
                        <x-select 
                            label="Identification Type" 
                            wire:model="identificationtype" 
                            :options="[['id'=>'NATIONAL_ID','label'=>'National ID'], ['id'=>'PASSPORT','label'=>'Passport']]" 
                            option-label="label" 
                            option-value="id" 
                            required 
                        />
                        <x-select 
                            label="Nationality" 
                            wire:model="nationality_id" 
                            :options="$nationalities" 
                            option-label="name" 
                            option-value="id" 
                            placeholder="Select Nationality" 
                            required 
                        />
                        <x-input label="Address" wire:model="address" required />
                        <x-input label="Place of Birth" wire:model="placeofbirth" required />
                    </div>
                </div>

                <div class="border-t pt-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold text-lg">Professions</h3>
                        <x-button label="Add Profession" icon="o-plus" class="btn-sm btn-secondary" wire:click="addProfession" />
                    </div>

                    @foreach($professions as $index => $profession)
                        <div class="border rounded-lg p-4 mb-4 bg-gray-50">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="font-semibold">Profession {{ $index + 1 }}</h4>
                                @if(count($professions) > 1)
                                    <x-button wire:click="removeProfession({{ $index }})" icon="o-trash" class="btn-error btn-sm" />
                                @endif
                            </div>

                            <div class="grid lg:grid-cols-3 gap-4">
                                <x-select 
                                    label="Profession" 
                                    wire:model.live="professions.{{ $index }}.profession_id" 
                                    :options="$professionsList" 
                                    option-label="name" 
                                    option-value="id" 
                                    placeholder="Select Profession" 
                                    required 
                                />
                                @php
                                    $selectedProfessionId = $professions[$index]['profession_id'] ?? null;
                                    $tires = $selectedProfessionId ? $this->getTiresForProfession($selectedProfessionId) : [];
                                @endphp
                                <x-select 
                                    label="Tier" 
                                    wire:model="professions.{{ $index }}.tire_id" 
                                    :options="$tires" 
                                    option-label="name" 
                                    option-value="id" 
                                    placeholder="Select Tier" 
                                    :disabled="!$selectedProfessionId"
                                />
                                <x-input 
                                    label="Registration Number" 
                                    wire:model="professions.{{ $index }}.registrationnumber" 
                                />
                                <x-input 
                                    label="Registration Year" 
                                    wire:model="professions.{{ $index }}.registrationyear" 
                                    type="number" 
                                />
                                <x-input 
                                    label="Practising Certificate Number" 
                                    wire:model="professions.{{ $index }}.practisingcertificatenumber" 
                                />
                                <x-select 
                                    label="Register Type" 
                                    wire:model="professions.{{ $index }}.registertype_id" 
                                    :options="$registertypes" 
                                    option-label="name" 
                                    option-value="id" 
                                    placeholder="Select Register Type" 
                                />
                                <x-input 
                                    label="Last Renewal Year" 
                                    wire:model.live="professions.{{ $index }}.last_renewal_year" 
                                    type="number" 
                                    placeholder="e.g., 2023"
                                />
                                <x-input 
                                    label="Last Renewal Expire Date" 
                                    wire:model="professions.{{ $index }}.last_renewal_expire_date" 
                                    type="date" 
                                    readonly
                                    hint="Automatically set to December 31st of the renewal year"
                                />
                                <x-input 
                                    label="Total CDP Points on Last Renewal Year" 
                                    wire:model="professions.{{ $index }}.last_renewal_year_cdp_points" 
                                    type="number" 
                                    placeholder="e.g., 50"
                                />
                            </div>

                            {{-- Optional Certificates Section --}}
                            <div class="border-t pt-4 mt-4">
                                <div class="flex justify-between items-center mb-2">
                                    <h5 class="font-semibold text-sm text-gray-700">Certificates (Optional)</h5>
                                    <x-button 
                                        label="Add Certificate" 
                                        icon="o-plus" 
                                        class="btn-xs btn-secondary" 
                                        wire:click="addCertificate({{ $index }})" 
                                    />
                                </div>
                                <p class="text-xs text-gray-500 mb-3">You can optionally attach certificates for this profession</p>
                                
                                @if(isset($profession['certificates']) && count($profession['certificates']) > 0)
                                    @foreach($profession['certificates'] as $certIndex => $certificate)
                                        <div class="flex gap-2 mb-2 items-end">
                                            <div class="flex-1">
                                                <x-input 
                                                    type="file" 
                                                    wire:model="professions.{{ $index }}.certificates.{{ $certIndex }}" 
                                                    label="Certificate File"
                                                    accept=".pdf,.jpg,.jpeg,.png"
                                                />
                                            </div>
                                            <div class="flex-1">
                                                <x-input 
                                                    wire:model="professions.{{ $index }}.descriptions.{{ $certIndex }}" 
                                                    label="Description" 
                                                    placeholder="e.g., Registration Certificate"
                                                />
                                            </div>
                                            <x-button 
                                                wire:click="removeCertificate({{ $index }}, {{ $certIndex }})" 
                                                icon="o-trash" 
                                                class="btn-error btn-sm" 
                                            />
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <x-slot:actions>
                <x-button label="Cancel" wire:click="$set('createmodal', false)" class="btn-secondary" />
                <x-button label="Submit for Approval" type="submit" class="btn-primary" spinner="save" wire:loading.attr="disabled" />
            </x-slot:actions>
        </x-form>
    </x-modal>

    {{-- Import Modal --}}
    <x-modal title="Import Historical Data from File" wire:model="importmodal" separator>
        <x-form wire:submit="import">
            <div class="space-y-4">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <h4 class="font-semibold text-yellow-900 mb-2">File Requirements:</h4>
                    <ul class="list-disc list-inside text-sm text-yellow-800 space-y-1">
                        <li>Supported formats: CSV, XLS, XLSX</li>
                        <li>Maximum file size: 10MB</li>
                        <li>First row must contain headers</li>
                        <li>Download the template to see the required format</li>
                    </ul>
                </div>

                <x-input 
                    label="Select File" 
                    wire:model="file" 
                    type="file" 
                    accept=".csv,.xlsx,.xls"
                    required 
                />

                @if($file)
                    <div class="text-sm text-gray-600">
                        Selected: {{ $file->getClientOriginalName() }} ({{ number_format($file->getSize() / 1024, 2) }} KB)
                    </div>
                @endif
            </div>

            <x-slot:actions>
                <x-button label="Cancel" wire:click="$set('importmodal', false)" class="btn-secondary" />
                <x-button label="Import" type="submit" class="btn-primary" spinner="import" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
