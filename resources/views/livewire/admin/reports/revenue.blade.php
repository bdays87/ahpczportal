<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />
    
    <x-card title="Revenue Report ({{ $revenueData->count() }} Transactions)" separator class="mt-5 border-2 border-gray-200">
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
                        placeholder="Search customer, reference..." 
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

                {{-- Source --}}
                <div>
                    <x-select 
                        wire:model.live="source" 
                        label="Source" 
                        :options="$sourceOptions" 
                        option-label="name" 
                        option-value="id" 
                        placeholder="All Sources"
                        icon="o-building-library"
                    />
                </div>
            </div>
        </div>

        <x-hr />

        {{-- Summary Statistics --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            <div class="stats shadow">
                <div class="stat">
                    <div class="stat-figure text-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-8 h-8 stroke-current">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="stat-title">Total Revenue</div>
                    <div class="stat-value text-primary">${{ number_format($summaryStats['total_revenue'], 2) }}</div>
                    <div class="stat-desc">{{ $summaryStats['total_transactions'] }} transactions</div>
                </div>
            </div>
            
            <div class="stats shadow">
                <div class="stat">
                    <div class="stat-figure text-success">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-8 h-8 stroke-current">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="stat-title">Paid Amount</div>
                    <div class="stat-value text-success">${{ number_format($summaryStats['paid_amount'], 2) }}</div>
                    <div class="stat-desc">Settled transactions</div>
                </div>
            </div>
            
            <div class="stats shadow">
                <div class="stat">
                    <div class="stat-figure text-warning">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-8 h-8 stroke-current">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="stat-title">Pending Amount</div>
                    <div class="stat-value text-warning">${{ number_format($summaryStats['pending_amount'], 2) }}</div>
                    <div class="stat-desc">Awaiting payment</div>
                </div>
            </div>
            
            <div class="stats shadow">
                <div class="stat">
                    <div class="stat-figure text-info">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-8 h-8 stroke-current">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div class="stat-title">Invoices</div>
                    <div class="stat-value text-info">{{ $summaryStats['invoice_count'] }}</div>
                    <div class="stat-desc">Manual: {{ $summaryStats['manual_payment_count'] }}</div>
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
                        <th>Type</th>
                        <th>Reference</th>
                        <th>Customer</th>
                        <th>Province</th>
                        <th>City</th>
                        <th>Description</th>
                        <th>Source</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($revenueData as $transaction)
                    <tr>
                        <td>{{ $transaction['transaction_date']->format('Y-m-d H:i') }}</td>
                        <td>
                            <x-badge 
                                value="{{ $transaction['type'] }}" 
                                class="{{ $transaction['type'] == 'Invoice' ? 'badge-info' : 'badge-secondary' }}" 
                            />
                        </td>
                        <td class="font-mono text-xs">{{ $transaction['reference'] }}</td>
                        <td>
                            <div class="font-semibold">
                                {{ $transaction['customer']->name ?? 'N/A' }} 
                                {{ $transaction['customer']->surname ?? '' }}
                            </div>
                            <div class="text-xs text-gray-500">{{ $transaction['customer']->email ?? '' }}</div>
                        </td>
                        <td>{{ $transaction['customer']->province?->name ?? 'N/A' }}</td>
                        <td>{{ $transaction['customer']->city?->name ?? 'N/A' }}</td>
                        <td>
                            <div class="max-w-xs truncate" title="{{ $transaction['description'] }}">
                                {{ $transaction['description'] }}
                            </div>
                        </td>
                        <td>
                            <x-badge 
                                value="{{ $transaction['source'] }}" 
                                class="badge-outline" 
                            />
                        </td>
                        <td class="font-semibold">
                            {{ $transaction['currency']->code ?? 'USD' }} 
                            {{ number_format($transaction['amount'], 2) }}
                        </td>
                        <td>
                            <x-badge 
                                value="{{ $transaction['status'] }}" 
                                class="{{ 
                                    $transaction['status'] == 'PAID' || $transaction['status'] == 'SETTLED' ? 'badge-success' : 
                                    ($transaction['status'] == 'PENDING' || $transaction['status'] == 'AWAITING' ? 'badge-warning' : 'badge-error') 
                                }}" 
                            />
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10">
                            <div class="text-center py-8">
                                <div class="text-gray-400 mb-2">
                                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-500">No revenue data found</h3>
                                <p class="text-gray-400">Try adjusting your filters to see more results</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($revenueData->isNotEmpty())
        <div class="mt-4">
            <x-alert icon="o-information-circle" class="alert-info">
                Showing {{ $revenueData->count() }} transaction(s) with total revenue of ${{ number_format($summaryStats['total_revenue'], 2) }}
            </x-alert>
        </div>
        @endif
    </x-card>
</div>
