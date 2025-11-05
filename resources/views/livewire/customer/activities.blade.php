<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />
    
    <!-- My Enrollments -->
    <x-card title="My Enrolled Activities" separator class="border-2 mt-3 border-gray-200 mb-6">
        @if($myEnrollments->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($myEnrollments as $enrollment)
            <div class="border rounded-lg p-4 hover:shadow-lg transition-shadow">
                <div class="mb-4">
                    <h3 class="font-semibold text-lg mb-2">{{ $enrollment->activity->title }}</h3>
                    <p class="text-gray-600 text-sm mb-3">{{ Str::limit($enrollment->activity->description, 100) }}</p>
                    
                    <div class="flex items-center justify-between mb-3">
                        <span class="badge 
                            @if($enrollment->status === 'COMPLETED') badge-success
                            @elseif($enrollment->status === 'IN_PROGRESS') badge-warning
                            @else badge-info
                            @endif">
                            {{ $enrollment->status }}
                        </span>
                        <span class="text-sm font-medium text-green-600">{{ $enrollment->activity->points }} pts</span>
                    </div>
                </div>
                
                <div class="flex space-x-2">
                    @if($enrollment->status !== 'COMPLETED')
                        @if($enrollment->activity->quiz)
                        <x-button 
                            label="Take Quiz" 
                            class="btn-primary btn-sm flex-1"
                            :link="route('customer.quiz', $enrollment->id)" />
                        @endif
                    @else
                        <span class="text-green-600 text-sm font-medium">✓ Completed - {{ $enrollment->points_earned }} pts earned</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8">
            <p class="text-gray-500">You haven't enrolled in any activities yet.</p>
        </div>
        @endif
    </x-card>
    
    <!-- Available Activities -->
    <x-card title="Available Activities" separator class="border-2 border-gray-200">
        @if($availableActivities->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($availableActivities as $activity)
            @if($activity->enrollments->isEmpty())
            <div class="border rounded-lg p-4 hover:shadow-lg transition-shadow">
                <div class="mb-4">
                    <div class="flex items-start justify-between mb-2">
                        <h3 class="font-semibold text-lg">{{ $activity->title }}</h3>
                        <span class="badge badge-primary">{{ $activity->type }}</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-3">{{ Str::limit($activity->description, 100) }}</p>
                    
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm text-gray-500">{{ $activity->duration_minutes }}min</span>
                        <span class="text-sm font-medium text-green-600">{{ $activity->points }} points</span>
                    </div>
                    
                    <div class="flex flex-wrap gap-1 mb-3">
                        @foreach($activity->professions as $profession)
                        <span class="badge badge-outline badge-xs">{{ $profession->name }}</span>
                        @endforeach
                    </div>
                </div>
                
                <x-button 
                    label="Enroll Now" 
                    class="btn-primary btn-sm w-full"
                    wire:click="enroll({{ $activity->id }})"
                    spinner="enroll" />
            </div>
            @endif
            @endforeach
        </div>
        @else
        <div class="text-center py-8">
            <p class="text-gray-500">No activities available for your professions at this time.</p>
        </div>
        @endif
    </x-card>
</div>