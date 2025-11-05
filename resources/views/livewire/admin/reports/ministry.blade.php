<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />
    
    <x-card title="Ministry Report ({{ $applications->count() }} Practitioners)" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            <x-button 
                icon="o-arrow-path" 
                label="Clear Filters" 
                class="btn-sm btn-ghost" 
                wire:click="clearFilters" 
            />
            <x-button 
                icon="o-arrow-down-tray" 
                label="Export CSV" 
                class="btn-sm btn-primary" 
                wire:click="exportToCsv" 
            />
        </x-slot:menu>

        {{-- Filters Section --}}
        <div class="bg-base-200 p-4 rounded-box mb-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Search --}}
                <div>
                    <x-input 
                        wire:model.live.debounce.500ms="search" 
                        placeholder="Search name, email..." 
                        icon="o-magnifying-glass"
                        label="Search"
                    />
                </div>

                {{-- Date From --}}
                <div>
                    <x-input 
                        type="date" 
                        wire:model.live="date_from" 
                        label="Date From" 
                        icon="o-calendar"
                    />
                </div>

                {{-- Date To --}}
                <div>
                    <x-input 
                        type="date" 
                        wire:model.live="date_to" 
                        label="Date To" 
                        icon="o-calendar"
                    />
                </div>

                {{-- Year --}}
                <div>
                    <x-select 
                        wire:model.live="year" 
                        label="Year" 
                        :options="$applicationSessions" 
                        option-label="year" 
                        option-value="year" 
                        placeholder="All Years"
                        icon="o-calendar-days"
                    />
                </div>

                {{-- Province --}}
                <div>
                    <x-select 
                        wire:model.live="province_id" 
                        label="Province" 
                        :options="$provinces" 
                        option-label="name" 
                        option-value="id" 
                        placeholder="All Provinces"
                        icon="o-map-pin"
                    />
                </div>

                {{-- City --}}
                <div>
                    <x-select 
                        wire:model.live="city_id" 
                        label="City" 
                        :options="$cities" 
                        option-label="name" 
                        option-value="id" 
                        placeholder="All Cities"
                        icon="o-map"
                    />
                </div>

                {{-- Gender --}}
                <div>
                    <x-select 
                        wire:model.live="gender" 
                        label="Gender" 
                        :options="$genderOptions" 
                        option-label="name" 
                        option-value="id" 
                        placeholder="All Genders"
                        icon="o-user"
                    />
                </div>

                {{-- Age From --}}
                <div>
                    <x-input 
                        type="number" 
                        wire:model.live="age_from" 
                        label="Age From" 
                        placeholder="Min age"
                        icon="o-users"
                        min="0"
                        max="120"
                    />
                </div>

                {{-- Age To --}}
                <div>
                    <x-input 
                        type="number" 
                        wire:model.live="age_to" 
                        label="Age To" 
                        placeholder="Max age"
                        icon="o-users"
                        min="0"
                        max="120"
                    />
                </div>

                {{-- Profession --}}
                <div>
                    <x-select 
                        wire:model.live="profession_id" 
                        label="Profession" 
                        :options="$professions" 
                        option-label="name" 
                        option-value="id" 
                        placeholder="All Professions"
                        icon="o-briefcase"
                    />
                </div>

                {{-- Register Type --}}
                <div>
                    <x-select 
                        wire:model.live="registertype_id" 
                        label="Register Type" 
                        :options="$registertypes" 
                        option-label="name" 
                        option-value="id" 
                        placeholder="All Register Types"
                        icon="o-document-text"
                    />
                </div>

                {{-- Place of Birth --}}
                <div>
                    <x-input 
                        wire:model.live.debounce.500ms="place_of_birth" 
                        label="Place of Birth" 
                        placeholder="Search place..."
                        icon="o-globe-alt"
                    />
                </div>

                {{-- Compliance --}}
                <div>
                    <x-select 
                        wire:model.live="compliance" 
                        label="Compliance" 
                        :options="$complianceOptions" 
                        option-label="name" 
                        option-value="id" 
                        placeholder="All Compliance"
                        icon="o-shield-check"
                    />
                </div>
            </div>
        </div>

        <x-hr />

        {{-- Demographics Summary --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            <div class="stats shadow">
                <div class="stat">
                    <div class="stat-figure text-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-8 h-8 stroke-current">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="stat-title">Total Practitioners</div>
                    <div class="stat-value text-primary">{{ $demographics['total'] }}</div>
                    <div class="stat-desc">Approved: {{ $demographics['approved'] }}</div>
                </div>
            </div>
            
            <div class="stats shadow">
                <div class="stat">
                    <div class="stat-figure text-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-8 h-8 stroke-current">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <div class="stat-title">Gender Distribution</div>
                    <div class="stat-value text-info">{{ $demographics['male'] }}</div>
                    <div class="stat-desc">Male / Female: {{ $demographics['female'] }}</div>
                </div>
            </div>
            
            <div class="stats shadow">
                <div class="stat">
                    <div class="stat-figure text-success">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-8 h-8 stroke-current">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="stat-title">Compliance Status</div>
                    <div class="stat-value text-success">{{ $demographics['valid'] }}</div>
                    <div class="stat-desc">Valid / Expired: {{ $demographics['expired'] }}</div>
                </div>
            </div>
            
            <div class="stats shadow">
                <div class="stat">
                    <div class="stat-figure text-warning">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-8 h-8 stroke-current">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="stat-title">Average Age</div>
                    <div class="stat-value text-warning">{{ round($demographics['avg_age'], 1) }}</div>
                    <div class="stat-desc">Years old</div>
                </div>
            </div>
        </div>

        <x-hr />

        {{-- Data Table --}}
        <div class="overflow-x-auto">
            <table class="table table-zebra table-sm">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Year</th>
                        <th>Practitioner</th>
                        <th>Gender</th>
                        <th>Age</th>
                        <th>Date of Birth</th>
                        <th>Place of Birth</th>
                        <th>Province</th>
                        <th>City</th>
                        <th>Profession</th>
                        <th>Register Type</th>
                        <th>Status</th>
                        <th>Compliance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($applications as $application)
                    @if($application->customerprofession && $application->customerprofession->customer)
                    <tr>
                        <td>{{ $application->created_at->format('Y-m-d') }}</td>
                        <td>{{ $application->year }}</td>
                        <td>
                            <div class="font-semibold">
                                {{ $application->customerprofession->customer->name }} 
                                {{ $application->customerprofession->customer->surname }}
                            </div>
                            <div class="text-xs text-gray-500">{{ $application->customerprofession->customer->email }}</div>
                        </td>
                        <td>
                            <x-badge 
                                value="{{ $application->customerprofession->customer->gender }}" 
                                class="{{ $application->customerprofession->customer->gender == 'MALE' ? 'badge-info' : 'badge-secondary' }}" 
                            />
                        </td>
                        <td>{{ (int) $application->customerprofession->customer->getage() }} yrs</td>
                        <td>
                            @if($application->customerprofession->customer->dob)
                                {{ \Carbon\Carbon::parse($application->customerprofession->customer->dob)->format('Y-m-d') }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>{{ $application->customerprofession->customer->place_of_birth ?? 'N/A' }}</td>
                        <td>{{ $application->customerprofession->customer->province?->name ?? 'N/A' }}</td>
                        <td>{{ $application->customerprofession->customer->city?->name ?? 'N/A' }}</td>
                        <td>{{ $application->customerprofession->profession->name ?? 'N/A' }}</td>
                        <td>{{ $application->customerprofession->registertype->name ?? 'N/A' }}</td>
                        <td>
                            <x-badge 
                                value="{{ $application->status }}" 
                                class="{{ 
                                    $application->status == 'APPROVED' ? 'badge-success' : 
                                    ($application->status == 'REJECTED' ? 'badge-error' : 'badge-warning') 
                                }}" 
                            />
                        </td>
                        <td>
                            @if($application->status == 'APPROVED')
                                <x-badge 
                                    value="{{ $application->certificate_status }}" 
                                    class="{{ $application->isValid() ? 'badge-success' : 'badge-error' }}" 
                                />
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @endif
                    @empty
                    <tr>
                        <td colspan="13">
                            <div class="text-center py-8">
                                <div class="text-gray-400 mb-2">
                                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-500">No practitioners found</h3>
                                <p class="text-gray-400">Try adjusting your filters to see more results</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($applications->isNotEmpty())
        <div class="mt-4">
            <x-alert icon="o-information-circle" class="alert-info">
                Showing {{ $applications->count() }} practitioner(s) | Average Age: {{ round($demographics['avg_age'], 1) }} years | Male: {{ $demographics['male'] }} | Female: {{ $demographics['female'] }}
            </x-alert>
        </div>
        @endif
    </x-card>
</div>
