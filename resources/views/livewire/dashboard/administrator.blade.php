<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />

    <div class="bg-white border rounded-lg mt-3">
        <div class="container mx-auto px-4 py-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Administrator Dashboard</h1>
                    <p class="mt-1 text-sm text-gray-500">Welcome back, {{ auth()->user()->name }} {{ auth()->user()->surname }}</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <x-select 
                        wire:model.live="selectedYear" 
                        label="Filter by Year" 
                        :options="$applicationSessions" 
                        option-label="year" 
                        option-value="year" 
                        icon="o-calendar"
                        class="w-48"
                    />
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto py-4">
        {{-- Applications Statistics --}}
        <x-card title="Applications ({{ $selectedYear }})" subtitle="Current year applications overview" separator class="border-2 border-gray-200 mb-5">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="stats shadow">
                    <div class="stat">
                        <div class="stat-figure text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-8 h-8 stroke-current">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="stat-title">Total</div>
                        <div class="stat-value text-primary">{{ $applicationStats['total'] }}</div>
                        <div class="stat-desc">Applications</div>
                    </div>
                </div>

                <div class="stats shadow">
                    <div class="stat">
                        <div class="stat-title">Pending</div>
                        <div class="stat-value text-warning">{{ $applicationStats['pending'] }}</div>
                        <div class="stat-desc">Need review</div>
                    </div>
                </div>

                <div class="stats shadow">
                    <div class="stat">
                        <div class="stat-title">Awaiting</div>
                        <div class="stat-value text-info">{{ $applicationStats['awaiting'] }}</div>
                        <div class="stat-desc">Under review</div>
                    </div>
                </div>

                <div class="stats shadow">
                    <div class="stat">
                        <div class="stat-title">Approved</div>
                        <div class="stat-value text-success">{{ $applicationStats['approved'] }}</div>
                        <div class="stat-desc">Completed</div>
                    </div>
                </div>

                <div class="stats shadow">
                    <div class="stat">
                        <div class="stat-title">Rejected</div>
                        <div class="stat-value text-error">{{ $applicationStats['rejected'] }}</div>
                        <div class="stat-desc">Declined</div>
                    </div>
                </div>
            </div>
        </x-card>

        {{-- Registrations Statistics --}}
        <x-card title="Registrations ({{ $selectedYear }})" subtitle="Current year registrations overview" separator class="border-2 border-gray-200 mb-5">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="stats shadow">
                    <div class="stat">
                        <div class="stat-figure text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-8 h-8 stroke-current">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="stat-title">Total</div>
                        <div class="stat-value text-primary">{{ $registrationStats['total'] }}</div>
                        <div class="stat-desc">Registrations</div>
                    </div>
                </div>

                <div class="stats shadow">
                    <div class="stat">
                        <div class="stat-title">Pending</div>
                        <div class="stat-value text-warning">{{ $registrationStats['pending'] }}</div>
                        <div class="stat-desc">Need review</div>
                    </div>
                </div>

                <div class="stats shadow">
                    <div class="stat">
                        <div class="stat-title">Awaiting</div>
                        <div class="stat-value text-info">{{ $registrationStats['awaiting'] }}</div>
                        <div class="stat-desc">Under review</div>
                    </div>
                </div>

                <div class="stats shadow">
                    <div class="stat">
                        <div class="stat-title">Approved</div>
                        <div class="stat-value text-success">{{ $registrationStats['approved'] }}</div>
                        <div class="stat-desc">Completed</div>
                    </div>
                </div>

                <div class="stats shadow">
                    <div class="stat">
                        <div class="stat-title">Rejected</div>
                        <div class="stat-value text-error">{{ $registrationStats['rejected'] }}</div>
                        <div class="stat-desc">Declined</div>
                    </div>
                </div>
            </div>
        </x-card>

        {{-- Revenue Statistics --}}
        <x-card title="Revenue ({{ $selectedYear }})" subtitle="Current year revenue overview" separator class="border-2 border-gray-200 mb-5">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="stats shadow">
                    <div class="stat">
                        <div class="stat-figure text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-8 h-8 stroke-current">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="stat-title">Total Invoices</div>
                        <div class="stat-value text-primary">{{ $invoiceStats['total'] }}</div>
                        <div class="stat-desc">${{ number_format($invoiceStats['total_amount'], 2) }}</div>
                    </div>
                </div>

                <div class="stats shadow">
                    <div class="stat">
                        <div class="stat-figure text-success">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-8 h-8 stroke-current">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="stat-title">Paid</div>
                        <div class="stat-value text-success">{{ $invoiceStats['paid'] }}</div>
                        <div class="stat-desc">${{ number_format($invoiceStats['paid_amount'], 2) }}</div>
                    </div>
                </div>

                <div class="stats shadow">
                    <div class="stat">
                        <div class="stat-figure text-warning">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-8 h-8 stroke-current">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="stat-title">Pending</div>
                        <div class="stat-value text-warning">{{ $invoiceStats['pending'] }}</div>
                        <div class="stat-desc">${{ number_format($invoiceStats['pending_amount'], 2) }}</div>
                    </div>
                </div>
            </div>
        </x-card>

        {{-- Recent Applications --}}
        <x-card title="Recent Applications" subtitle="Latest 10 applications" separator class="border-2 border-gray-200 mb-5">
            <x-slot:menu>
                <x-button 
                    icon="o-arrow-right" 
                    label="View All" 
                    class="btn-sm btn-ghost" 
                    link="{{ route('reports.applications') }}" 
                />
            </x-slot:menu>

            <div class="overflow-x-auto">
                <table class="table table-zebra table-sm">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Practitioner</th>
                            <th>Profession</th>
                            <th>Register Type</th>
                            <th>Application Type</th>
                            <th>Status</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentApplications as $application)
                        @if($application->customerprofession && $application->customerprofession->customer)
                        <tr>
                            <td>{{ $application->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="font-semibold">
                                    {{ $application->customerprofession->customer->name }} 
                                    {{ $application->customerprofession->customer->surname }}
                                </div>
                                <div class="text-xs text-gray-500">{{ $application->customerprofession->customer->email }}</div>
                            </td>
                            <td>{{ $application->customerprofession->profession->name ?? 'N/A' }}</td>
                            <td>
                                <x-badge 
                                    value="{{ $application->customerprofession->registertype->name ?? 'N/A' }}" 
                                    class="badge-outline badge-sm" 
                                />
                            </td>
                            <td>
                                <x-badge 
                                    value="{{ $application->applicationtype->name ?? 'N/A' }}" 
                                    class="badge-primary badge-sm" 
                                />
                            </td>
                            <td>
                                <x-badge 
                                    value="{{ $application->status }}" 
                                    class="{{ 
                                        $application->status == 'APPROVED' ? 'badge-success' : 
                                        ($application->status == 'REJECTED' ? 'badge-error' : 'badge-warning') 
                                    }} badge-sm" 
                                />
                            </td>
                            <td class="text-xs text-gray-500">{{ $application->created_at->diffForHumans() }}</td>
                        </tr>
                        @endif
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-8">
                                <div class="text-gray-500">
                                    No recent applications
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        {{-- Groupings by Category --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
            {{-- By Profession --}}
            <x-card title="Applications by Profession" subtitle="Top professions for {{ $selectedYear }}" separator class="border-2 border-gray-200">
                <div class="space-y-3">
                    @forelse($applicationsByProfession->take(5) as $item)
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700">{{ $item['name'] }}</span>
                            <x-badge value="{{ $item['count'] }}" class="badge-primary" />
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            @php
                                $percentage = $applicationStats['total'] > 0 ? ($item['count'] / $applicationStats['total']) * 100 : 0;
                            @endphp
                            <div class="bg-primary h-2 rounded-full transition-all" style="width: {{ $percentage }}%"></div>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">{{ number_format($percentage, 1) }}%</div>
                    </div>
                    @empty
                    <div class="text-center text-gray-400 py-4">No data available</div>
                    @endforelse
                </div>
            </x-card>

            {{-- By Register Type --}}
            <x-card title="Applications by Register Type" subtitle="Register types for {{ $selectedYear }}" separator class="border-2 border-gray-200">
                <div class="space-y-3">
                    @forelse($applicationsByRegistertype as $item)
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700">{{ $item['name'] }}</span>
                            <x-badge value="{{ $item['count'] }}" class="badge-secondary" />
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            @php
                                $percentage = $applicationStats['total'] > 0 ? ($item['count'] / $applicationStats['total']) * 100 : 0;
                            @endphp
                            <div class="bg-secondary h-2 rounded-full transition-all" style="width: {{ $percentage }}%"></div>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">{{ number_format($percentage, 1) }}%</div>
                    </div>
                    @empty
                    <div class="text-center text-gray-400 py-4">No data available</div>
                    @endforelse
                </div>
            </x-card>

            {{-- By Customer Type --}}
            <x-card title="Applications by Customer Type" subtitle="Customer types for {{ $selectedYear }}" separator class="border-2 border-gray-200">
                <div class="space-y-3">
                    @forelse($applicationsByCustomertype as $item)
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700">{{ $item['name'] }}</span>
                            <x-badge value="{{ $item['count'] }}" class="badge-accent" />
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            @php
                                $percentage = $applicationStats['total'] > 0 ? ($item['count'] / $applicationStats['total']) * 100 : 0;
                            @endphp
                            <div class="bg-accent h-2 rounded-full transition-all" style="width: {{ $percentage }}%"></div>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">{{ number_format($percentage, 1) }}%</div>
                    </div>
                    @empty
                    <div class="text-center text-gray-400 py-4">No data available</div>
                    @endforelse
                </div>
            </x-card>
        </div>

        {{-- Quick Actions --}}
        <x-card title="Quick Actions" separator class="border-2 border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('reports.applications') }}" class="btn btn-outline btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-6 h-6 stroke-current mr-2">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Applications Report
                </a>

                <a href="{{ route('reports.registrations') }}" class="btn btn-outline btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-6 h-6 stroke-current mr-2">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Registrations Report
                </a>

                <a href="{{ route('reports.revenue') }}" class="btn btn-outline btn-accent">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-6 h-6 stroke-current mr-2">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Revenue Report
                </a>

                <a href="{{ route('reports.ministry') }}" class="btn btn-outline btn-info">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-6 h-6 stroke-current mr-2">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Ministry Report
                </a>
            </div>
        </x-card>
    </div>
</div>
