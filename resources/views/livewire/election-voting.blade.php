<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />
    
    @if(!$isCompliant)
    <x-alert icon="o-exclamation-triangle" class="alert-error mt-5">
        <strong>You are not eligible to vote</strong><br>
        Your certificate has expired or you do not have an approved application. Please ensure your registration is current and compliant to participate in elections.
    </x-alert>
    @else
    <x-card title="Elections & Voting" separator class="mt-5 border-2 border-gray-200">
        @if(!$selectedElection)
        {{-- Active Elections List --}}
        <div class="space-y-4">
            @forelse($activeElections as $election)
            <div class="border-2 border-success rounded-box p-4 hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <h3 class="text-xl font-bold">{{ $election->name }}</h3>
                        <p class="text-gray-500 text-sm">{{ $election->description }}</p>
                        <div class="flex items-center gap-4 mt-2">
                            @if($election->isActive())
                                <span class="text-sm">
                                    <strong>Ends:</strong> {{ $election->end_date->diffForHumans() }}
                                </span>
                                <x-badge value="ACTIVE" class="badge-success" />
                            @else
                                <span class="text-sm">
                                    <strong>Starts:</strong> {{ $election->start_date->diffForHumans() }}
                                </span>
                                <x-badge value="READY TO START" class="badge-warning" />
                            @endif
                            <span class="text-sm text-gray-500">{{ $election->positions->count() }} positions</span>
                        </div>
                    </div>
                    @if($election->isActive())
                        <x-button 
                            icon="o-hand-raised" 
                            label="Vote Now" 
                            class="btn-primary" 
                            wire:click="selectElection({{ $election->id }})" 
                        />
                    @else
                        <x-button 
                            icon="o-eye" 
                            label="View Details" 
                            class="btn-ghost" 
                            wire:click="selectElection({{ $election->id }})" 
                        />
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <div class="text-gray-400 mb-2">
                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-500">No active elections</h3>
                <p class="text-gray-400">There are currently no elections open for voting</p>
            </div>
            @endforelse
        </div>
        @else
        {{-- Voting Interface --}}
        <div>
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-2xl font-bold">{{ $selectedElection->name }}</h3>
                    <p class="text-gray-500">{{ $selectedElection->description }}</p>
                    @if(!$selectedElection->isActive())
                        <x-alert icon="o-clock" class="alert-warning mt-2">
                            <strong>Election not yet active</strong><br>
                            This election will start {{ $selectedElection->start_date->diffForHumans() }} ({{ $selectedElection->start_date->format('M j, Y \a\t g:i A') }})
                        </x-alert>
                    @endif
                </div>
                <x-button 
                    icon="o-arrow-left" 
                    label="Back to Elections" 
                    class="btn-ghost" 
                    wire:click="selectedElection = null" 
                />
            </div>

            <x-hr />

            {{-- Positions & Candidates --}}
            <div class="space-y-6 mt-6" wire:poll.5s>
                @foreach($selectedElection->positions as $position)
                <div class="border-2 rounded-box p-4">
                    <h4 class="font-semibold text-lg mb-4">{{ $position->name }}</h4>
                    <p class="text-sm text-gray-500 mb-4">{{ $position->description }}</p>

                    @if($this->hasVotedForPosition($position->id))
                    <x-alert icon="o-check-circle" class="alert-success mb-4">
                        You have already voted for this position
                    </x-alert>
                    @endif

                    {{-- Candidates Grid --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($position->candidates as $candidate)
                        <div class="bg-white border-2 rounded-lg p-4 {{ $this->hasVotedForPosition($position->id) ? 'opacity-60' : 'hover:border-primary hover:shadow-lg transition cursor-pointer' }}">
                            <div class="flex flex-col items-center text-center">
                                @if($candidate->profile_picture)
                                <img src="{{ Storage::url($candidate->profile_picture) }}" class="w-24 h-24 rounded-full mb-3" alt="{{ $candidate->customer->name }}">
                                @else
                                <div class="w-24 h-24 rounded-full bg-primary text-white flex items-center justify-center text-3xl font-bold mb-3">
                                    {{ substr($candidate->customer->name, 0, 1) }}{{ substr($candidate->customer->surname, 0, 1) }}
                                </div>
                                @endif
                                
                                <h5 class="font-bold text-lg">{{ $candidate->customer->name }} {{ $candidate->customer->surname }}</h5>
                                <p class="text-sm text-gray-500 mb-3">{{ $candidate->customer->email }}</p>
                                
                                @if($candidate->description)
                                <p class="text-sm text-gray-600 mb-4">{{ $candidate->description }}</p>
                                @endif

                                @if(!$this->hasVotedForPosition($position->id) && $selectedElection->isActive())
                                <x-button 
                                    icon="o-hand-raised" 
                                    label="Vote" 
                                    class="btn-primary w-full" 
                                    wire:click="castVote({{ $position->id }}, {{ $candidate->id }})" 
                                    wire:confirm="Are you sure you want to vote for {{ $candidate->customer->name }} {{ $candidate->customer->surname }}?"
                                    spinner
                                />
                                @elseif($this->hasVotedForPosition($position->id))
                                <x-badge value="Voted" class="badge-success" />
                                @else
                                <x-badge value="Not Yet Active" class="badge-warning" />
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>

                    @if($position->candidates->count() === 0)
                    <div class="text-center text-gray-400 py-4">
                        No candidates for this position yet
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </x-modal>
    @endif
</div>




