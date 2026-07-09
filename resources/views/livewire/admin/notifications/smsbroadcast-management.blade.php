<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />
    
    {{-- Credits Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-5">
        <div class="stats shadow">
            <div class="stat">
                <div class="stat-figure text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-8 h-8 stroke-current">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                    </svg>
                </div>
                <div class="stat-title">Total Credits</div>
                <div class="stat-value text-primary">{{ number_format($totalCredits) }}</div>
                <div class="stat-desc">SMS sending capacity</div>
            </div>
        </div>
        
        <div class="stats shadow">
            <div class="stat">
                <div class="stat-figure text-success">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-8 h-8 stroke-current">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="stat-title">Remaining Credits</div>
                <div class="stat-value text-success">{{ number_format($remainingCredits) }}</div>
                <div class="stat-desc">Available for use</div>
            </div>
        </div>
        
        <div class="stats shadow">
            <div class="stat">
                <div class="stat-figure text-warning">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-8 h-8 stroke-current">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div class="stat-title">Used Credits</div>
                <div class="stat-value text-warning">{{ number_format($usedCredits) }}</div>
                <div class="stat-desc">SMS sent</div>
            </div>
        </div>
    </div>

    {{-- Main Card --}}
    <x-card title="SMS Broadcasting" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            {{-- Provider badge --}}
            @if($provider === 'nhume')
                <x-badge value="Nhume" class="badge-info" />
                @if($nhumeBalance !== null)
                    <x-badge value="{{ $nhumeBalance }} credits" class="badge-success" />
                @endif
            @else
                <x-badge value="eSolutions" class="badge-ghost" />
            @endif

            <x-button icon="o-paper-airplane" label="Test SMS" class="btn-sm btn-outline"
                wire:click="$set('testmodal', true)" />
            <x-button icon="o-arrow-up-tray" label="Import Contacts" class="btn-sm btn-outline"
                wire:click="$set('importmodal', true)" />
            <x-button icon="o-plus-circle" label="Add Credits" class="btn-sm btn-ghost"
                wire:click="$set('addCreditsModal', true)" />
            <x-button icon="o-chat-bubble-left-right" label="New Campaign" class="btn-sm btn-primary"
                wire:click="$set('createCampaignModal', true)" />
        </x-slot:menu>

        {{-- Tabs --}}
        <x-tabs wire:model="selectedTab" class="tabs-boxed">
            <x-tab name="campaigns" label="Campaigns" icon="o-chat-bubble-left-right">
                {{-- Campaigns List --}}
                <div class="overflow-x-auto mt-4">
                    <table class="table table-zebra table-sm">
                        <thead>
                            <tr>
                                <th>Campaign</th>
                                <th>Message Preview</th>
                                <th>Recipients</th>
                                <th>Sent</th>
                                <th>Failed</th>
                                <th>Status</th>
                                <th>Credits Used</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($campaigns as $campaign)
                            <tr>
                                <td>
                                    <div class="font-semibold">{{ $campaign->campaign_name }}</div>
                                </td>
                                <td>
                                    <div class="max-w-xs truncate text-sm">{{ $campaign->message }}</div>
                                </td>
                                <td>{{ $campaign->total_recipients }}</td>
                                <td>
                                    <x-badge value="{{ $campaign->sent_count }}" class="badge-success" />
                                </td>
                                <td>
                                    <x-badge value="{{ $campaign->failed_count }}" class="badge-error" />
                                </td>
                                <td>
                                    <x-badge 
                                        value="{{ $campaign->status }}" 
                                        class="{{ 
                                            $campaign->status == 'SENT' ? 'badge-success' : 
                                            ($campaign->status == 'SENDING' ? 'badge-warning' : 
                                            ($campaign->status == 'FAILED' ? 'badge-error' : 'badge-ghost'))
                                        }}" 
                                    />
                                </td>
                                <td>{{ $campaign->credits_used }}</td>
                                <td>{{ $campaign->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <x-button 
                                            icon="o-eye" 
                                            class="btn-xs btn-ghost" 
                                            wire:click="viewCampaign({{ $campaign->id }})" 
                                        />
                                        @if($campaign->status == 'DRAFT' && $campaign->pending_count > 0)
                                        <x-button 
                                            icon="o-paper-airplane" 
                                            class="btn-xs btn-primary" 
                                            wire:click="sendCampaign({{ $campaign->id }})" 
                                            wire:confirm="Send this campaign to {{ $campaign->total_recipients }} recipients?"
                                            spinner
                                        />
                                        @endif
                                        @if($campaign->status == 'SENDING')
                                        <x-button
                                            icon="o-check-circle"
                                            class="btn-xs btn-success btn-outline"
                                            wire:click="markSent({{ $campaign->id }})"
                                            wire:confirm="Mark this campaign as SENT?"
                                            spinner
                                            tooltip="Mark as Sent"
                                        />
                                        @if($campaign->provider === 'nhume')
                                        <x-button
                                            icon="o-arrow-path"
                                            class="btn-xs btn-info btn-outline"
                                            wire:click="checkDeliveryStatus({{ $campaign->id }})"
                                            spinner
                                            tooltip="Check Nhume Delivery Status"
                                        />
                                        @endif
                                        @endif
                                        <x-button
                                            icon="o-trash"
                                            class="btn-xs btn-error btn-outline"
                                            wire:click="deleteCampaign({{ $campaign->id }})"
                                            wire:confirm="Delete this campaign and all its recipients?"
                                            spinner
                                        />
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-8">
                                    <div class="text-gray-500">
                                        No SMS campaigns created yet
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-tab>

            <x-tab name="credits" label="Credits History" icon="o-currency-dollar">
                {{-- Credits History --}}
                <div class="overflow-x-auto mt-4">
                    <table class="table table-zebra table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Credits Added</th>
                                <th>Description</th>
                                <th>Added By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($creditHistory as $credit)
                            <tr>
                                <td>{{ $credit->created_at->format('M d, Y H:i') }}</td>
                                <td>
                                    <x-badge value="+{{ number_format($credit->credits) }}" class="badge-success" />
                                </td>
                                <td>{{ $credit->description ?? 'N/A' }}</td>
                                <td>{{ $credit->addedBy->name ?? 'N/A' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-8">
                                    <div class="text-gray-500">
                                        No credits added yet
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-tab>
        </x-tabs>
    </x-card>

    {{-- Add Credits Modal --}}
    <x-modal wire:model="addCreditsModal" title="Add SMS Credits" box-class="max-w-md">
        <div class="space-y-4">
            <x-input 
                type="number" 
                wire:model="credit_amount" 
                label="Number of Credits" 
                placeholder="Enter credits amount"
                min="1"
                required
            />
            
            <x-textarea 
                wire:model="credit_description" 
                label="Description" 
                placeholder="Optional note about this credit purchase"
                rows="2"
            />
        </div>

        <x-slot:actions>
            <x-button label="Cancel" wire:click="addCreditsModal = false" />
            <x-button label="Add Credits" class="btn-primary" wire:click="addCredits" spinner />
        </x-slot:actions>
    </x-modal>

    {{-- Create Campaign Modal --}}
    <x-modal wire:model="createCampaignModal" title="Create SMS Campaign" box-class="max-w-3xl">
        <div class="space-y-4">

            {{-- Campaign name --}}
            <x-input wire:model="campaign_name" label="Campaign Name"
                placeholder="e.g. Renewal Reminder - Jan 2025" required />

            {{-- Provider --}}
            <x-select wire:model="campaign_provider" label="SMS Provider"
                :options="$providers" option-label="name" option-value="id"
                placeholder="Use default ({{ config('services.sms_provider') }})"
                icon="o-signal" />

            {{-- Message --}}
            <div>
                <x-textarea wire:model.live="campaign_message" label="SMS Message"
                    placeholder="Enter your SMS message (max 160 characters)"
                    rows="4" required />
                <div class="text-right mt-1">
                    <span class="text-sm {{ strlen($campaign_message ?? '') > 160 ? 'text-error' : 'text-gray-500' }}">
                        {{ strlen($campaign_message ?? '') }}/160 characters
                    </span>
                </div>
            </div>

            <x-hr />

            {{-- Contact source toggle --}}
            <div class="bg-base-200 p-4 rounded-box">
                <h4 class="font-semibold mb-3">Target Recipients</h4>

                <div class="flex gap-3 mb-4">
                    <button type="button"
                        wire:click="$set('contact_source','db')"
                        class="btn btn-sm {{ $contact_source === 'db' ? 'btn-primary' : 'btn-outline' }}">
                        <x-icon name="o-users" class="w-4 h-4" /> From Database
                    </button>
                    <button type="button"
                        wire:click="$set('contact_source','file')"
                        class="btn btn-sm {{ $contact_source === 'file' ? 'btn-primary' : 'btn-outline' }}">
                        <x-icon name="o-document-text" class="w-4 h-4" /> From File (.txt/.csv)
                    </button>
                </div>

                {{-- DB filters --}}
                @if($contact_source === 'db')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-select wire:model.live="filter_compliance" label="Compliance Status"
                        :options="$complianceOptions" option-label="name" option-value="id"
                        placeholder="All Compliance" icon="o-shield-check" />
                    <x-select wire:model.live="filter_profession_id" label="Profession"
                        :options="$professions" option-label="name" option-value="id"
                        placeholder="All Professions" icon="o-briefcase" />
                    <x-select wire:model.live="filter_registertype_id" label="Register Type"
                        :options="$registertypes" option-label="name" option-value="id"
                        placeholder="All Register Types" icon="o-document-text" />
                    <x-select wire:model.live="filter_province_id" label="Province"
                        :options="$provinces" option-label="name" option-value="id"
                        placeholder="All Provinces" icon="o-map-pin" />
                    <x-select wire:model.live="filter_city_id" label="City"
                        :options="$cities" option-label="name" option-value="id"
                        placeholder="All Cities" icon="o-map" />
                </div>
                <div class="mt-3">
                    <x-alert icon="o-information-circle" class="alert-info">
                        <strong>{{ $recipientsCount }}</strong> practitioner(s) matched
                    </x-alert>
                </div>
                @endif

                {{-- File upload --}}
                @if($contact_source === 'file')
                <div class="space-y-3">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-sm font-medium text-gray-700">Download template:</span>
                        <x-button label="TXT" icon="o-arrow-down-tray" class="btn-xs btn-outline"
                            wire:click="downloadTemplate('txt')" spinner />
                        <x-button label="CSV" icon="o-arrow-down-tray" class="btn-xs btn-outline"
                            wire:click="downloadTemplate('csv')" spinner />
                    </div>
                    <x-input type="file" wire:model="contactfile"
                        label="Upload contact file (.txt or .csv — one number per line)"
                        accept=".txt,.csv" hint="e.g. 263772000111" />
                    <div wire:loading wire:target="contactfile" class="text-xs text-info flex items-center gap-1">
                        <span class="loading loading-spinner loading-xs"></span> Uploading...
                    </div>
                    <x-button label="Load Contacts" icon="o-arrow-up-tray" class="btn-sm btn-outline"
                        wire:click="importContacts" spinner="importContacts"
                        wire:loading.attr="disabled" wire:target="contactfile" />
                    @if(count($importedcontacts))
                    <x-alert class="alert-success"
                        title="{{ count($importedcontacts) }} number(s) loaded from file." />
                    <div class="max-h-24 overflow-y-auto bg-white rounded p-2 text-xs font-mono border">
                        @foreach(array_slice($importedcontacts, 0, 10) as $c)
                            <div>{{ $c }}</div>
                        @endforeach
                        @if(count($importedcontacts) > 10)
                            <div class="text-gray-400">+{{ count($importedcontacts) - 10 }} more…</div>
                        @endif
                    </div>
                    @endif
                </div>
                @endif
            </div>

            {{-- Test numbers --}}
            <div class="bg-warning/10 border border-warning/30 rounded-box p-4">
                <h4 class="font-semibold mb-1 flex items-center gap-2">
                    <x-icon name="o-beaker" class="w-4 h-4 text-warning" />
                    Test Numbers
                    <span class="text-xs font-normal text-gray-500">(optional — verify before sending)</span>
                </h4>
                <x-input wire:model="test_numbers"
                    placeholder="263772000111, 263771000222, ..."
                    hint="Comma-separated. Campaign stays DRAFT until you're ready to send." />
            </div>

        </div>

        <x-slot:actions>
            <x-button label="Cancel" wire:click="$set('createCampaignModal', false)" />
            <x-button label="Create as DRAFT" class="btn-primary" icon="o-document-check"
                wire:click="createCampaign"
                :disabled="strlen($campaign_message ?? '') > 160"
                spinner />
        </x-slot:actions>
    </x-modal>

    {{-- View Campaign Modal --}}
    <x-modal wire:model="viewCampaignModal" title="SMS Campaign Details" box-class="max-w-4xl" wire:poll.5s>
        @if($selectedCampaign)
        <div class="space-y-4">
            <div class="bg-base-200 p-4 rounded-box">
                <h3 class="font-bold text-lg">{{ $selectedCampaign->campaign_name }}</h3>
                <div class="flex items-center gap-3 mt-2">
                    <x-badge value="{{ $selectedCampaign->status }}" class="badge-primary" />
                    <span class="text-sm">Created: {{ $selectedCampaign->created_at->format('M d, Y H:i') }}</span>
                </div>
            </div>

            {{-- Progress Statistics --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="stats shadow">
                    <div class="stat">
                        <div class="stat-title">Total</div>
                        <div class="stat-value text-sm">{{ $selectedCampaign->total_recipients }}</div>
                    </div>
                </div>
                <div class="stats shadow">
                    <div class="stat">
                        <div class="stat-title">Sent</div>
                        <div class="stat-value text-sm text-success">{{ $selectedCampaign->sent_count }}</div>
                    </div>
                </div>
                <div class="stats shadow">
                    <div class="stat">
                        <div class="stat-title">Failed</div>
                        <div class="stat-value text-sm text-error">{{ $selectedCampaign->failed_count }}</div>
                    </div>
                </div>
                <div class="stats shadow">
                    <div class="stat">
                        <div class="stat-title">Pending</div>
                        <div class="stat-value text-sm text-warning">{{ $selectedCampaign->pending_count }}</div>
                    </div>
                </div>
            </div>

            {{-- Progress Bar --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold">Sending Progress</span>
                    <span class="text-sm">{{ number_format($selectedCampaign->progress_percentage, 1) }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-primary h-3 rounded-full transition-all duration-300" style="width: {{ $selectedCampaign->progress_percentage }}%"></div>
                </div>
            </div>

            {{-- Message Content --}}
            <div class="bg-white p-4 rounded-box border">
                <h4 class="font-semibold mb-2">Message Content</h4>
                <div class="text-sm">
                    {{ $selectedCampaign->message }}
                </div>
                <div class="mt-2 text-xs text-gray-500">
                    {{ strlen($selectedCampaign->message) }} characters
                </div>
            </div>

            {{-- Recipients Details --}}
            <div>
                <h4 class="font-semibold mb-2">Recipients ({{ $selectedCampaign->recipients->count() }})</h4>
                <div class="overflow-x-auto max-h-64">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Phone</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Sent At</th>
                                <th>Error</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($selectedCampaign->recipients as $recipient)
                            <tr>
                                <td>{{ $recipient->phone }}</td>
                                <td>{{ $recipient->customer->name ?? 'N/A' }} {{ $recipient->customer->surname ?? '' }}</td>
                                <td>
                                    <x-badge 
                                        value="{{ $recipient->status }}" 
                                        class="{{ 
                                            $recipient->status == 'SENT' ? 'badge-success' : 
                                            ($recipient->status == 'FAILED' ? 'badge-error' : 'badge-warning')
                                        }}" 
                                    />
                                </td>
                                <td>{{ $recipient->sent_at?->format('M d, H:i') ?? '-' }}</td>
                                <td>
                                    @if($recipient->error_message)
                                    <span class="text-xs text-error truncate max-w-xs block">{{ $recipient->error_message }}</span>
                                    @else
                                    -
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <x-slot:actions>
            @if($selectedCampaign && $selectedCampaign->provider === 'nhume' && $selectedCampaign->status === 'SENDING')
            <x-button label="Check Delivery Status" icon="o-arrow-path" class="btn-info"
                wire:click="checkDeliveryStatus({{ $selectedCampaign->id }})" spinner />
            @endif
            <x-button label="Close" wire:click="viewCampaignModal = false" />
        </x-slot:actions>
    </x-modal>

    {{-- ── Test SMS Modal ── --}}
    <x-modal wire:model="testmodal" title="Send Test SMS" box-class="max-w-md">
        <div class="space-y-4">
            <x-alert class="alert-info" title="Send a test message to verify your content before launching a campaign." />
            <x-input wire:model="test_phone" label="Phone Number"
                placeholder="e.g. 263772000111 or 0772000111"
                hint="Use international format" />
            <div>
                <x-textarea wire:model.live="test_message" label="Message"
                    placeholder="Enter test message (max 160 chars)" rows="4" />
                <div class="text-right mt-1">
                    <span class="text-sm {{ strlen($test_message ?? '') > 160 ? 'text-error' : 'text-gray-500' }}">
                        {{ strlen($test_message ?? '') }}/160 characters
                    </span>
                </div>
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" wire:click="$set('testmodal', false)" />
            <x-button label="Send Test SMS" class="btn-warning" icon="o-paper-airplane"
                wire:click="sendTest" spinner="sendTest" />
        </x-slot:actions>
    </x-modal>

    {{-- ── Import Contacts Modal ── --}}
    <x-modal wire:model="importmodal" title="Import Contacts from File" box-class="max-w-md">
        <div class="space-y-4">
            <x-alert class="alert-info"
                title="Upload a .txt or .csv file with one phone number per line in international format (e.g. 263772000111)." />
            <x-input type="file" wire:model="contactfile" label="Contact File (.txt or .csv)"
                accept=".txt,.csv" />
            @if(count($importedcontacts))
                <x-alert class="alert-success"
                    title="{{ count($importedcontacts) }} phone number(s) imported successfully." />
                <div class="max-h-36 overflow-y-auto bg-base-200 rounded-lg p-3 text-xs font-mono space-y-1">
                    @foreach(array_slice($importedcontacts, 0, 15) as $c)
                        <div>{{ $c }}</div>
                    @endforeach
                    @if(count($importedcontacts) > 15)
                        <div class="text-gray-400">... and {{ count($importedcontacts) - 15 }} more</div>
                    @endif
                </div>
            @endif
        </div>
        <x-slot:actions>
            <x-button label="Cancel" wire:click="$set('importmodal', false)" />
            <x-button label="Import" class="btn-primary" icon="o-arrow-up-tray"
                wire:click="importContacts" spinner="importContacts" />
        </x-slot:actions>
    </x-modal>

</div>





