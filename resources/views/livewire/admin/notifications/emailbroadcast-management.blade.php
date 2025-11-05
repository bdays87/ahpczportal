<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />
    
    {{-- Credits Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-5">
        <div class="stats shadow">
            <div class="stat">
                <div class="stat-figure text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-8 h-8 stroke-current">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="stat-title">Total Credits</div>
                <div class="stat-value text-primary">{{ number_format($totalCredits) }}</div>
                <div class="stat-desc">Email sending capacity</div>
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
                <div class="stat-desc">Emails sent</div>
            </div>
        </div>
    </div>

    {{-- Main Card --}}
    <x-card title="Email Broadcasting" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            <x-button 
                icon="o-plus-circle" 
                label="Add Credits" 
                class="btn-sm btn-ghost" 
                wire:click="addCreditsModal = true" 
            />
            <x-button 
                icon="o-paper-airplane" 
                label="New Campaign" 
                class="btn-sm btn-primary" 
                wire:click="createCampaignModal = true" 
            />
        </x-slot:menu>

        {{-- Tabs --}}
        <x-tabs wire:model="selectedTab" class="tabs-boxed">
            <x-tab name="campaigns" label="Campaigns" icon="o-envelope">
                {{-- Campaigns List --}}
                <div class="overflow-x-auto mt-4">
                    <table class="table table-zebra table-sm">
                        <thead>
                            <tr>
                                <th>Campaign</th>
                                <th>Subject</th>
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
                                <td>{{ $campaign->subject }}</td>
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
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-8">
                                    <div class="text-gray-500">
                                        No campaigns created yet
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
    <x-modal wire:model="addCreditsModal" title="Add Email Credits" box-class="max-w-md">
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
    <x-modal wire:model="createCampaignModal" title="Create Email Campaign" box-class="max-w-4xl">
        <div class="space-y-4">
            <x-input 
                wire:model="campaign_name" 
                label="Campaign Name" 
                placeholder="e.g., Monthly Newsletter - Jan 2025"
                required
            />
            
            <x-input 
                wire:model="campaign_subject" 
                label="Email Subject" 
                placeholder="Enter email subject line"
                required
            />
            
            <x-textarea 
                wire:model="campaign_message" 
                label="Message" 
                placeholder="Enter your email message (HTML supported)"
                rows="6"
                required
            />

            <div>
                <label class="label">
                    <span class="label-text font-semibold">Attachments (Optional)</span>
                </label>
                <input 
                    type="file" 
                    wire:model="uploaded_files" 
                    multiple 
                    class="file-input file-input-bordered w-full" 
                />
                <div class="label">
                    <span class="label-text-alt">Max 5MB per file</span>
                </div>
            </div>

            <x-hr />

            {{-- Recipient Filters --}}
            <div class="bg-base-200 p-4 rounded-box">
                <h4 class="font-semibold mb-3">Target Recipients</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-select 
                        wire:model.live="filter_compliance" 
                        label="Compliance Status" 
                        :options="$complianceOptions" 
                        option-label="name" 
                        option-value="id" 
                        placeholder="All Compliance"
                        icon="o-shield-check"
                    />
                    
                    <x-select 
                        wire:model.live="filter_profession_id" 
                        label="Profession" 
                        :options="$professions" 
                        option-label="name" 
                        option-value="id" 
                        placeholder="All Professions"
                        icon="o-briefcase"
                    />
                    
                    <x-select 
                        wire:model.live="filter_registertype_id" 
                        label="Register Type" 
                        :options="$registertypes" 
                        option-label="name" 
                        option-value="id" 
                        placeholder="All Register Types"
                        icon="o-document-text"
                    />
                    
                    <x-select 
                        wire:model.live="filter_province_id" 
                        label="Province" 
                        :options="$provinces" 
                        option-label="name" 
                        option-value="id" 
                        placeholder="All Provinces"
                        icon="o-map-pin"
                    />
                    
                    <x-select 
                        wire:model.live="filter_city_id" 
                        label="City" 
                        :options="$cities" 
                        option-label="name" 
                        option-value="id" 
                        placeholder="All Cities"
                        icon="o-map"
                    />
                </div>

                <div class="mt-4">
                    <x-alert icon="o-information-circle" class="alert-info">
                        <strong>{{ $recipientsCount }}</strong> practitioner(s) will receive this email
                    </x-alert>
                </div>
            </div>
        </div>

        <x-slot:actions>
            <x-button label="Cancel" wire:click="createCampaignModal = false" />
            <x-button 
                label="Create Campaign" 
                class="btn-primary" 
                wire:click="createCampaign" 
                :disabled="$recipientsCount == 0"
                spinner 
            />
        </x-slot:actions>
    </x-modal>

    {{-- View Campaign Modal --}}
    <x-modal wire:model="viewCampaignModal" title="Campaign Details" box-class="max-w-4xl" wire:poll.5s>
        @if($selectedCampaign)
        <div class="space-y-4">
            <div class="bg-base-200 p-4 rounded-box">
                <h3 class="font-bold text-lg">{{ $selectedCampaign->campaign_name }}</h3>
                <p class="text-sm text-gray-500 mt-1">
                    <strong>Subject:</strong> {{ $selectedCampaign->subject }}
                </p>
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
                    <span class="text-sm font-semibold">Progress</span>
                    <span class="text-sm">{{ number_format($selectedCampaign->progress_percentage, 1) }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-primary h-3 rounded-full transition-all duration-300" style="width: {{ $selectedCampaign->progress_percentage }}%"></div>
                </div>
            </div>

            {{-- Message Content --}}
            <div class="bg-white p-4 rounded-box border">
                <h4 class="font-semibold mb-2">Message Content</h4>
                <div class="prose max-w-none">
                    {!! nl2br(e($selectedCampaign->message)) !!}
                </div>
            </div>

            {{-- Attachments --}}
            @if($selectedCampaign->attachments)
            <div>
                <h4 class="font-semibold mb-2">Attachments</h4>
                <div class="flex flex-wrap gap-2">
                    @foreach($selectedCampaign->attachments as $attachment)
                    <x-badge value="{{ basename($attachment) }}" class="badge-outline" />
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Recipients Details --}}
            <div>
                <h4 class="font-semibold mb-2">Recipients ({{ $selectedCampaign->recipients->count() }})</h4>
                <div class="overflow-x-auto max-h-64">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Email</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Sent At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($selectedCampaign->recipients as $recipient)
                            <tr>
                                <td>{{ $recipient->email }}</td>
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
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <x-slot:actions>
            <x-button label="Close" wire:click="viewCampaignModal = false" />
        </x-slot:actions>
    </x-modal>
</div>





