<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />
    
    <x-card title="Applications Report ({{ $applications->count() }})" separator class="mt-5 border-2 border-gray-200">
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
                        label="Search by name or email"
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

                {{-- Application Type --}}
                <div>
                    <x-select 
                        wire:model.live="applicationtype_id" 
                        label="Application Type" 
                        :options="$applicationTypes" 
                        option-label="name" 
                        option-value="id" 
                        placeholder="All Application Types"
                        icon="o-document-duplicate"
                    />
                </div>

                {{-- Status --}}
                <div>
                    <x-select 
                        wire:model.live="status" 
                        label="Status" 
                        :options="$statusOptions" 
                        option-label="name" 
                        option-value="id" 
                        placeholder="All Statuses"
                        icon="o-check-circle"
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

        {{-- Summary Statistics --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
            <div class="stats shadow">
                <div class="stat">
                    <div class="stat-title">Total Applications</div>
                    <div class="stat-value text-primary">{{ $applications->count() }}</div>
                </div>
            </div>
            <div class="stats shadow">
                <div class="stat">
                    <div class="stat-title">Approved</div>
                    <div class="stat-value text-success">{{ $applications->where('status', 'APPROVED')->count() }}</div>
                </div>
            </div>
            <div class="stats shadow">
                <div class="stat">
                    <div class="stat-title">Pending/Awaiting</div>
                    <div class="stat-value text-warning">{{ $applications->whereIn('status', ['PENDING', 'AWAITING'])->count() }}</div>
                </div>
            </div>
            <div class="stats shadow">
                <div class="stat">
                    <div class="stat-title">Rejected</div>
                    <div class="stat-value text-error">{{ $applications->where('status', 'REJECTED')->count() }}</div>
                </div>
            </div>
            <div class="stats shadow">
                <div class="stat">
                    <div class="stat-title">Valid</div>
                    <div class="stat-value text-info">{{ $applications->filter(fn($app) => $app->status === 'APPROVED' && $app->isValid())->count() }}</div>
                    <div class="stat-desc">Expired: {{ $applications->filter(fn($app) => $app->status === 'APPROVED' && $app->isExpired())->count() }}</div>
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
                        <th>Email</th>
                        <th>Gender</th>
                        <th>Province</th>
                        <th>City</th>
                        <th>Profession</th>
                        <th>Register Type</th>
                        <th>Application Type</th>
                        <th>Status</th>
                        <th>Certificate #</th>
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
                        </td>
                        <td>{{ $application->customerprofession->customer->email }}</td>
                        <td>
                            <x-badge 
                                value="{{ $application->customerprofession->customer->gender }}" 
                                class="{{ $application->customerprofession->customer->gender == 'MALE' ? 'badge-info' : 'badge-secondary' }}" 
                            />
                        </td>
                        <td>{{ $application->customerprofession->customer->province?->name ?? 'N/A' }}</td>
                        <td>{{ $application->customerprofession->customer->city?->name ?? 'N/A' }}</td>
                        <td>{{ $application->customerprofession->profession->name ?? 'N/A' }}</td>
                        <td>{{ $application->customerprofession->registertype->name ?? 'N/A' }}</td>
                        <td>
                            <x-badge 
                                value="{{ $application->applicationtype->name ?? 'N/A' }}" 
                                class="badge-primary" 
                            />
                        </td>
                        <td>
                            <x-badge 
                                value="{{ $application->status }}" 
                                class="{{ 
                                    $application->status == 'APPROVED' ? 'badge-success' : 
                                    ($application->status == 'REJECTED' ? 'badge-error' : 'badge-warning') 
                                }}" 
                            />
                        </td>
                        <td>{{ $application->certificate_number ?? 'N/A' }}</td>
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
                                <h3 class="text-lg font-semibold text-gray-500">No applications found</h3>
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
                Showing {{ $applications->count() }} application(s) based on selected filters
            </x-alert>
        </div>
        @endif
    </x-card>
</div>
