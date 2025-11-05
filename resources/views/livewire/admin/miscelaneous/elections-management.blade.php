<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />
    
    <x-card title="Elections Management" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            <x-button 
                icon="o-plus" 
                label="Create Election" 
                class="btn-primary" 
                wire:click="createElectionModal = true" 
            />
        </x-slot:menu>

        {{-- Elections List --}}
        <div class="space-y-4">
            @forelse($elections as $election)
            <div class="border-2 rounded-box p-4 {{ $election->isActive() ? 'border-success' : 'border-gray-200' }}">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="text-xl font-bold">{{ $election->name }}</h3>
                            <x-badge value="Year: {{ $election->year }}" class="badge-neutral" />
                        </div>
                        <p class="text-gray-500 text-sm">{{ $election->description }}</p>
                        <div class="flex items-center gap-4 mt-2 flex-wrap">
                            <span class="text-sm">
                                <strong>Start:</strong> {{ $election->start_date->format('M d, Y H:i') }}
                            </span>
                            <span class="text-sm">
                                <strong>End:</strong> {{ $election->end_date->format('M d, Y H:i') }}
                            </span>
                            <x-badge 
                                value="Status: {{ $election->status }}" 
                                class="{{ 
                                    $election->status == 'RUNNING' ? 'badge-success' : 
                                    ($election->status == 'CLOSED' ? 'badge-error' : 'badge-warning') 
                                }}" 
                            />
                            <x-badge 
                                value="Publish: {{ $election->publish_status }}" 
                                class="{{ $election->publish_status == 'PUBLISHED' ? 'badge-info' : 'badge-ghost' }}" 
                            />
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <x-button 
                            icon="o-chart-bar" 
                            label="Results" 
                            class="btn-sm btn-info" 
                            wire:click="viewResults({{ $election->id }})" 
                        />
                        @if($election->status == 'DRAFT')
                        <x-button 
                            icon="o-pencil" 
                            label="Edit" 
                            class="btn-sm btn-ghost" 
                            wire:click="editElection({{ $election->id }})" 
                        />
                        @if($election->publish_status == 'DRAFT')
                        <x-button 
                            icon="o-paper-airplane" 
                            label="Publish" 
                            class="btn-sm btn-info" 
                            wire:click="publishElection({{ $election->id }})" 
                            wire:confirm="Publish this election?"
                        />
                        @endif
                        @if($election->publish_status == 'PUBLISHED')
                        <x-button 
                            icon="o-play" 
                            label="Start" 
                            class="btn-sm btn-success" 
                            wire:click="startElection({{ $election->id }})" 
                            wire:confirm="Start voting for this election?"
                        />
                        @endif
                        @endif
                        @if($election->status == 'RUNNING')
                        <x-button 
                            icon="o-stop" 
                            label="Close" 
                            class="btn-sm btn-error" 
                            wire:click="closeElection({{ $election->id }})" 
                            wire:confirm="Close this election and stop voting?"
                        />
                        @endif
                    </div>
                </div>

                {{-- Positions --}}
                <div class="bg-base-200 rounded-box p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-semibold">Positions ({{ $election->positions->count() }})</h4>
                        @if($election->status == 'DRAFT')
                        <x-button 
                            icon="o-plus" 
                            label="Add Position" 
                            class="btn-xs btn-primary" 
                            wire:click="openAddPositionModal({{ $election->id }})" 
                        />
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse($election->positions as $position)
                        <div class="bg-white rounded-lg p-4 shadow">
                            <h5 class="font-semibold text-lg mb-2">{{ $position->name }}</h5>
                            <p class="text-sm text-gray-500 mb-3">{{ $position->description }}</p>
                            
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-semibold">Candidates: {{ $position->candidates->count() }}</span>
                                @if($election->status == 'DRAFT')
                                <x-button 
                                    icon="o-plus" 
                                    label="Add" 
                                    class="btn-xs btn-ghost" 
                                    wire:click="openAddCandidateModal({{ $position->id }})" 
                                />
                                @endif
                            </div>

                            {{-- Candidates List --}}
                            <div class="space-y-2">
                                @foreach($position->candidates as $candidate)
                                <div class="flex items-center justify-between bg-base-100 p-2 rounded">
                                    <div class="flex items-center gap-2">
                                        @if($candidate->profile_picture)
                                        <img src="{{ Storage::url($candidate->profile_picture) }}" class="w-8 h-8 rounded-full" alt="{{ $candidate->customer->name }}">
                                        @else
                                        <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-xs">
                                            {{ substr($candidate->customer->name, 0, 1) }}{{ substr($candidate->customer->surname, 0, 1) }}
                                        </div>
                                        @endif
                                        <span class="text-sm">{{ $candidate->customer->name }} {{ $candidate->customer->surname }}</span>
                                    </div>
                                    @if($election->status == 'DRAFT')
                                    <x-button 
                                        icon="o-trash" 
                                        class="btn-xs btn-ghost text-error" 
                                        wire:click="removeCandidate({{ $candidate->id }})" 
                                        wire:confirm="Remove this candidate?"
                                    />
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @empty
                        <div class="col-span-3 text-center text-gray-400 py-4">
                            No positions added yet
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <div class="text-gray-400 mb-2">
                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-500">No elections found</h3>
                <p class="text-gray-400">Create your first election to get started</p>
            </div>
            @endforelse
        </div>
    </x-card>

    {{-- Create Election Modal --}}
    <x-modal wire:model="createElectionModal" title="Create New Election" box-class="max-w-2xl">
        <div class="space-y-4">
            <x-input 
                wire:model="election_name" 
                label="Election Name" 
                placeholder="e.g., Annual Board Elections 2025"
                required
            />
            
            <x-textarea 
                wire:model="election_description" 
                label="Description" 
                placeholder="Brief description of the election"
                rows="3"
            />

            <x-input 
                type="number" 
                wire:model="election_year" 
                label="Year" 
                placeholder="{{ date('Y') }}"
                min="2020"
                max="2100"
                required
            />
            
            <div class="grid grid-cols-2 gap-4">
                <x-input 
                    type="datetime-local" 
                    wire:model="election_start_date" 
                    label="Start Date & Time" 
                    required
                />
                
                <x-input 
                    type="datetime-local" 
                    wire:model="election_end_date" 
                    label="End Date & Time" 
                    required
                />
            </div>
        </div>

        <x-slot:actions>
            <x-button label="Cancel" wire:click="createElectionModal = false" />
            <x-button label="Create Election" class="btn-primary" wire:click="createElection" spinner />
        </x-slot:actions>
    </x-modal>

    {{-- Edit Election Modal --}}
    <x-modal wire:model="editElectionModal" title="Edit Election" box-class="max-w-2xl">
        <div class="space-y-4">
            <x-input 
                wire:model="election_name" 
                label="Election Name" 
                required
            />
            
            <x-textarea 
                wire:model="election_description" 
                label="Description" 
                rows="3"
            />

            <x-input 
                type="number" 
                wire:model="election_year" 
                label="Year" 
                min="2020"
                max="2100"
                required
            />
            
            <div class="grid grid-cols-2 gap-4">
                <x-input 
                    type="datetime-local" 
                    wire:model="election_start_date" 
                    label="Start Date & Time" 
                    required
                />
                
                <x-input 
                    type="datetime-local" 
                    wire:model="election_end_date" 
                    label="End Date & Time" 
                    required
                />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <x-select 
                    wire:model="election_status" 
                    label="Status" 
                    :options="[
                        ['id' => 'DRAFT', 'name' => 'Draft'],
                        ['id' => 'RUNNING', 'name' => 'Running'],
                        ['id' => 'CLOSED', 'name' => 'Closed']
                    ]" 
                    option-label="name" 
                    option-value="id" 
                />

                <x-select 
                    wire:model="election_publish_status" 
                    label="Publish Status" 
                    :options="[
                        ['id' => 'DRAFT', 'name' => 'Draft'],
                        ['id' => 'PUBLISHED', 'name' => 'Published']
                    ]" 
                    option-label="name" 
                    option-value="id" 
                />
            </div>
        </div>

        <x-slot:actions>
            <x-button label="Cancel" wire:click="editElectionModal = false" />
            <x-button label="Update Election" class="btn-primary" wire:click="updateElection" spinner />
        </x-slot:actions>
    </x-modal>

    {{-- Add Position Modal --}}
    <x-modal wire:model="addPositionModal" title="Add Position" box-class="max-w-lg">
        <div class="space-y-4">
            <x-input 
                wire:model="position_name" 
                label="Position Name" 
                placeholder="e.g., President, Secretary"
                required
            />
            
            <x-textarea 
                wire:model="position_description" 
                label="Description" 
                placeholder="Position responsibilities"
                rows="3"
            />
        </div>

        <x-slot:actions>
            <x-button label="Cancel" wire:click="addPositionModal = false" />
            <x-button label="Add Position" class="btn-primary" wire:click="addPosition" spinner />
        </x-slot:actions>
    </x-modal>

    {{-- Add Candidate Modal --}}
    <x-modal wire:model="addCandidateModal" title="Add Candidate" box-class="max-w-3xl">
        <div class="space-y-4">
            {{-- Practitioner Search Filters --}}
            <div class="bg-base-200 p-3 rounded-box">
                <h4 class="font-semibold mb-3">Search Compliant Practitioners</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <x-input 
                        wire:model.live.debounce.500ms="search" 
                        placeholder="Search name, email..." 
                        icon="o-magnifying-glass"
                    />
                    <x-select 
                        wire:model.live="province_id" 
                        :options="$provinces" 
                        option-label="name" 
                        option-value="id" 
                        placeholder="All Provinces"
                    />
                    <x-select 
                        wire:model.live="city_id" 
                        :options="$cities" 
                        option-label="name" 
                        option-value="id" 
                        placeholder="All Cities"
                    />
                </div>
            </div>

            <x-select 
                wire:model="candidate_customer_id" 
                label="Select Practitioner (Compliant Only)" 
                :options="$compliantPractitioners->map(fn($p) => ['id' => $p->id, 'name' => $p->name . ' ' . $p->surname . ' (' . $p->email . ')'])" 
                option-label="name" 
                option-value="id" 
                placeholder="Select a compliant practitioner"
                required
            />

            <x-textarea 
                wire:model="candidate_description" 
                label="Candidate Statement" 
                placeholder="Why this practitioner should be elected"
                rows="4"
            />

            <x-file 
                wire:model="candidate_profile_picture" 
                label="Profile Picture" 
                accept="image/*"
                hint="Optional profile picture for the candidate"
            />
        </div>

        <x-slot:actions>
            <x-button label="Cancel" wire:click="addCandidateModal = false" />
            <x-button label="Add Candidate" class="btn-primary" wire:click="addCandidate" spinner />
        </x-slot:actions>
    </x-modal>

    {{-- View Results Modal --}}
    <x-modal wire:model="viewResultsModal" title="Election Results" box-class="max-w-6xl" wire:poll.5s>
        @if($selectedElection)
        <div class="space-y-6">
            <div class="bg-base-200 p-4 rounded-box">
                <h3 class="text-lg font-bold">{{ $selectedElection->name }}</h3>
                <p class="text-sm">{{ $selectedElection->description }}</p>
                <div class="flex items-center gap-4 mt-2">
                    <x-badge value="Total Votes: {{ $selectedElection->votes->count() }}" class="badge-primary" />
                    <x-badge value="{{ $selectedElection->status }}" class="badge-info" />
                </div>
            </div>

            @foreach($selectedElection->positions as $position)
            <div class="border-2 rounded-box p-4">
                <h4 class="font-semibold text-lg mb-4">{{ $position->name }}</h4>
                
                @php
                    $totalVotes = $position->votes->count();
                @endphp

                <div class="space-y-3">
                    @foreach($position->candidates->sortByDesc(fn($c) => $c->votes->count()) as $candidate)
                    @php
                        $voteCount = $candidate->votes->count();
                        $percentage = $totalVotes > 0 ? ($voteCount / $totalVotes) * 100 : 0;
                    @endphp
                    <div class="bg-white p-4 rounded-lg shadow">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3">
                                @if($candidate->profile_picture)
                                <img src="{{ Storage::url($candidate->profile_picture) }}" class="w-12 h-12 rounded-full" alt="{{ $candidate->customer->name }}">
                                @else
                                <div class="w-12 h-12 rounded-full bg-primary text-white flex items-center justify-center font-bold">
                                    {{ substr($candidate->customer->name, 0, 1) }}{{ substr($candidate->customer->surname, 0, 1) }}
                                </div>
                                @endif
                                <div>
                                    <div class="font-semibold">{{ $candidate->customer->name }} {{ $candidate->customer->surname }}</div>
                                    <div class="text-xs text-gray-500">{{ $candidate->customer->email }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-primary">{{ $voteCount }}</div>
                                <div class="text-sm text-gray-500">votes ({{ number_format($percentage, 1) }}%)</div>
                            </div>
                        </div>
                        
                        {{-- Vote Progress Bar --}}
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-primary h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>

                        @if($candidate->description)
                        <p class="mt-2 text-sm text-gray-600">{{ $candidate->description }}</p>
                        @endif
                    </div>
                    @endforeach

                    @if($position->candidates->count() === 0)
                    <div class="text-center text-gray-400 py-4">
                        No candidates for this position
                    </div>
                    @endif
                </div>

                <div class="mt-3 text-sm text-gray-500">
                    Total votes for this position: {{ $totalVotes }}
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <x-slot:actions>
            <x-button label="Close" wire:click="viewResultsModal = false" />
        </x-slot:actions>
    </x-modal>
</div>

