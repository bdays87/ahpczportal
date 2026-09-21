<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />

    <x-card title="Financial Reports" separator class="mt-5 border-2 border-gray-200">
        {{-- Report Selection Tabs --}}
        <div role="tablist" class="tabs tabs-bordered mb-4">
            <a role="tab" class="tab {{ $activeTab === 'trial_balance' ? 'tab-active' : '' }}" 
                wire:click="$set('activeTab', 'trial_balance')">Trial Balance</a>
            <a role="tab" class="tab {{ $activeTab === 'profit_loss' ? 'tab-active' : '' }}" 
                wire:click="$set('activeTab', 'profit_loss')">Profit & Loss</a>
            <a role="tab" class="tab {{ $activeTab === 'balance_sheet' ? 'tab-active' : '' }}" 
                wire:click="$set('activeTab', 'balance_sheet')">Balance Sheet</a>
            <a role="tab" class="tab {{ $activeTab === 'cash_flow' ? 'tab-active' : '' }}" 
                wire:click="$set('activeTab', 'cash_flow')">Cash Flow</a>
            <a role="tab" class="tab {{ $activeTab === 'general_ledger' ? 'tab-active' : '' }}" 
                wire:click="$set('activeTab', 'general_ledger')">General Ledger</a>
        </div>

        {{-- Filters --}}
        <div class="grid grid-cols-4 gap-3 mb-4">
            <x-input label="Start Date" wire:model.live="startDate" type="date" />
            <x-input label="End Date" wire:model.live="endDate" type="date" />
            <x-select label="Cost Center" wire:model.live="costCenterId" :options="$costCenters" 
                option-label="name" option-value="id" placeholder="All" />
            <div class="flex items-end">
                <x-button label="Generate Report" icon="o-document-chart-bar" class="btn-primary" 
                    wire:click="generateReport" spinner />
                <x-button label="Export PDF" icon="o-arrow-down-tray" class="btn-outline ml-2" 
                    wire:click="exportPdf" spinner />
            </div>
        </div>

        {{-- Trial Balance Tab --}}
        @if($activeTab === 'trial_balance')
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr class="bg-base-200">
                            <th>Account Code</th>
                            <th>Account Name</th>
                            <th class="text-right">Debit</th>
                            <th class="text-right">Credit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reportData as $row)
                            <tr>
                                <td>{{ $row['code'] }}</td>
                                <td>{{ $row['name'] }}</td>
                                <td class="text-right font-mono">{{ number_format($row['debit'], 2) }}</td>
                                <td class="text-right font-mono">{{ number_format($row['credit'], 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-gray-400">No data available. Click "Generate Report".</td></tr>
                        @endforelse
                    </tbody>
                    @if($reportData)
                        <tfoot>
                            <tr class="bg-base-200 font-bold">
                                <td colspan="2" class="text-right">TOTAL:</td>
                                <td class="text-right font-mono">{{ number_format($totals['debit'], 2) }}</td>
                                <td class="text-right font-mono">{{ number_format($totals['credit'], 2) }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        @endif

        {{-- Profit & Loss Tab --}}
        @if($activeTab === 'profit_loss')
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr class="bg-base-200">
                            <th>Account</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($reportData)
                            <tr class="bg-blue-50"><td colspan="2" class="font-semibold">REVENUE</td></tr>
                            @foreach($reportData['revenue'] ?? [] as $row)
                                <tr>
                                    <td class="pl-6">{{ $row['name'] }}</td>
                                    <td class="text-right font-mono">{{ number_format($row['amount'], 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="bg-blue-100">
                                <td class="font-semibold">Total Revenue</td>
                                <td class="text-right font-mono font-semibold">{{ number_format($reportData['total_revenue'] ?? 0, 2) }}</td>
                            </tr>

                            <tr class="bg-orange-50"><td colspan="2" class="font-semibold">EXPENSES</td></tr>
                            @foreach($reportData['expenses'] ?? [] as $row)
                                <tr>
                                    <td class="pl-6">{{ $row['name'] }}</td>
                                    <td class="text-right font-mono">{{ number_format($row['amount'], 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="bg-orange-100">
                                <td class="font-semibold">Total Expenses</td>
                                <td class="text-right font-mono font-semibold">{{ number_format($reportData['total_expenses'] ?? 0, 2) }}</td>
                            </tr>

                            <tr class="bg-green-200">
                                <td class="font-bold text-lg">NET PROFIT / (LOSS)</td>
                                <td class="text-right font-mono font-bold text-lg">
                                    {{ number_format($reportData['net_profit'] ?? 0, 2) }}
                                </td>
                            </tr>
                        @else
                            <tr><td colspan="2" class="text-center text-gray-400">No data available. Click "Generate Report".</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Balance Sheet Tab --}}
        @if($activeTab === 'balance_sheet')
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr class="bg-base-200">
                            <th>Account</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($reportData)
                            <tr class="bg-blue-50"><td colspan="2" class="font-semibold">ASSETS</td></tr>
                            @foreach($reportData['assets'] ?? [] as $row)
                                <tr>
                                    <td class="pl-6">{{ $row['name'] }}</td>
                                    <td class="text-right font-mono">{{ number_format($row['amount'], 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="bg-blue-100">
                                <td class="font-semibold">Total Assets</td>
                                <td class="text-right font-mono font-semibold">{{ number_format($reportData['total_assets'] ?? 0, 2) }}</td>
                            </tr>

                            <tr class="bg-orange-50"><td colspan="2" class="font-semibold">LIABILITIES</td></tr>
                            @foreach($reportData['liabilities'] ?? [] as $row)
                                <tr>
                                    <td class="pl-6">{{ $row['name'] }}</td>
                                    <td class="text-right font-mono">{{ number_format($row['amount'], 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="bg-orange-100">
                                <td class="font-semibold">Total Liabilities</td>
                                <td class="text-right font-mono font-semibold">{{ number_format($reportData['total_liabilities'] ?? 0, 2) }}</td>
                            </tr>

                            <tr class="bg-green-50"><td colspan="2" class="font-semibold">EQUITY</td></tr>
                            @foreach($reportData['equity'] ?? [] as $row)
                                <tr>
                                    <td class="pl-6">{{ $row['name'] }}</td>
                                    <td class="text-right font-mono">{{ number_format($row['amount'], 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="bg-green-100">
                                <td class="font-semibold">Total Equity</td>
                                <td class="text-right font-mono font-semibold">{{ number_format($reportData['total_equity'] ?? 0, 2) }}</td>
                            </tr>

                            <tr class="bg-purple-200">
                                <td class="font-bold text-lg">TOTAL LIABILITIES + EQUITY</td>
                                <td class="text-right font-mono font-bold text-lg">
                                    {{ number_format($reportData['total_liabilities_equity'] ?? 0, 2) }}
                                </td>
                            </tr>
                        @else
                            <tr><td colspan="2" class="text-center text-gray-400">No data available. Click "Generate Report".</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Cash Flow Tab --}}
        @if($activeTab === 'cash_flow')
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr class="bg-base-200">
                            <th>Description</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($reportData)
                            <tr class="bg-blue-50"><td colspan="2" class="font-semibold">OPERATING ACTIVITIES</td></tr>
                            @foreach($reportData['operating'] ?? [] as $row)
                                <tr>
                                    <td class="pl-6">{{ $row['description'] }}</td>
                                    <td class="text-right font-mono">{{ number_format($row['amount'], 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="bg-blue-100">
                                <td class="font-semibold">Net Cash from Operating</td>
                                <td class="text-right font-mono font-semibold">{{ number_format($reportData['total_operating'] ?? 0, 2) }}</td>
                            </tr>

                            <tr class="bg-orange-50"><td colspan="2" class="font-semibold">INVESTING ACTIVITIES</td></tr>
                            @foreach($reportData['investing'] ?? [] as $row)
                                <tr>
                                    <td class="pl-6">{{ $row['description'] }}</td>
                                    <td class="text-right font-mono">{{ number_format($row['amount'], 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="bg-orange-100">
                                <td class="font-semibold">Net Cash from Investing</td>
                                <td class="text-right font-mono font-semibold">{{ number_format($reportData['total_investing'] ?? 0, 2) }}</td>
                            </tr>

                            <tr class="bg-green-50"><td colspan="2" class="font-semibold">FINANCING ACTIVITIES</td></tr>
                            @foreach($reportData['financing'] ?? [] as $row)
                                <tr>
                                    <td class="pl-6">{{ $row['description'] }}</td>
                                    <td class="text-right font-mono">{{ number_format($row['amount'], 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="bg-green-100">
                                <td class="font-semibold">Net Cash from Financing</td>
                                <td class="text-right font-mono font-semibold">{{ number_format($reportData['total_financing'] ?? 0, 2) }}</td>
                            </tr>

                            <tr class="bg-purple-200">
                                <td class="font-bold text-lg">NET CASH FLOW</td>
                                <td class="text-right font-mono font-bold text-lg">
                                    {{ number_format($reportData['net_cash_flow'] ?? 0, 2) }}
                                </td>
                            </tr>
                        @else
                            <tr><td colspan="2" class="text-center text-gray-400">No data available. Click "Generate Report".</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        @endif

        {{-- General Ledger Tab --}}
        @if($activeTab === 'general_ledger')
            <div class="mb-3">
                <x-select label="Select Account" wire:model.live="selectedAccountId" :options="$accounts" 
                    option-label="name" option-value="id" placeholder="Choose an account" />
            </div>
            @if($selectedAccountId)
                <div class="overflow-x-auto">
                    <table class="table table-sm">
                        <thead>
                            <tr class="bg-base-200">
                                <th>Date</th>
                                <th>Reference</th>
                                <th>Description</th>
                                <th class="text-right">Debit</th>
                                <th class="text-right">Credit</th>
                                <th class="text-right">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reportData as $row)
                                <tr>
                                    <td>{{ $row['date'] }}</td>
                                    <td>{{ $row['reference'] }}</td>
                                    <td>{{ $row['description'] }}</td>
                                    <td class="text-right font-mono">{{ number_format($row['debit'], 2) }}</td>
                                    <td class="text-right font-mono">{{ number_format($row['credit'], 2) }}</td>
                                    <td class="text-right font-mono">{{ number_format($row['balance'], 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-gray-400">No transactions found for this account.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        @endif
    </x-card>
</div>
