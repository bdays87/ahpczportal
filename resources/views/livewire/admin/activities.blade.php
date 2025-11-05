<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />
    
    <x-card title="Activities Management" separator class="border-2 border-gray-200">
        <x-slot:menu>
            <x-button 
                icon="o-plus" 
                label="Create Activity" 
                class="btn-primary" 
                wire:click="openCreateModal" />
        </x-slot:menu>
        
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Professions</th>
                        <th>Points</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Enrollments</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($activities as $activity)
                    <tr>
                        <td>
                            <div class="font-bold">{{ $activity->title }}</div>
                            <div class="text-sm opacity-50 truncate max-w-xs">{{ $activity->description }}</div>
                        </td>
                        <td>
                            <span class="badge 
                                @if($activity->type === 'VIDEO') badge-primary
                                @elseif($activity->type === 'ARTICLE') badge-secondary
                                @else badge-accent
                                @endif">
                                {{ $activity->type }}
                            </span>
                        </td>
                        <td>
                            <div class="flex flex-wrap gap-1">
                                @foreach($activity->professions->take(2) as $profession)
                                    <span class="badge badge-outline badge-xs">{{ $profession->name }}</span>
                                @endforeach
                                @if($activity->professions->count() > 2)
                                    <span class="badge badge-outline badge-xs">+{{ $activity->professions->count() - 2 }} more</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="font-semibold text-green-600">{{ $activity->points }}</span>
                        </td>
                        <td>{{ $activity->duration_minutes }}min</td>
                        <td>
                            <span class="badge 
                                @if($activity->status === 'PUBLISHED') badge-success
                                @elseif($activity->status === 'DRAFT') badge-warning
                                @else badge-error
                                @endif">
                                {{ $activity->status }}
                            </span>
                        </td>
                        <td>
                            <span class="text-sm">{{ $activity->enrollments->count() }}</span>
                        </td>
                        <td>
                            <div class="flex items-center space-x-2">
                                <x-button 
                                    icon="o-eye" 
                                    class="btn-sm btn-ghost" 
                                    wire:click="openViewModal({{ $activity->id }})" />
                                <x-button 
                                    icon="o-academic-cap" 
                                    class="btn-sm btn-info" 
                                    :link="route('admin.activity.quiz', $activity->id)"
                                    title="Manage Quiz" />
                                <x-button 
                                    icon="o-pencil" 
                                    class="btn-sm btn-warning" 
                                    wire:click="openEditModal({{ $activity->id }})" />
                                <x-button 
                                    icon="o-trash" 
                                    class="btn-sm btn-error" 
                                    wire:click="delete({{ $activity->id }})" 
                                    wire:confirm="Are you sure you want to delete this activity?" />
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-8">
                            <div class="text-gray-500">
                                <x-icon name="o-document" class="w-12 h-12 mx-auto mb-2 opacity-50" />
                                <p>No activities found. Create your first activity!</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <!-- Create/Edit Modal -->
    <x-modal wire:model="createModal" title="Create Activity" box-class=" max-w-4xl">
        @include('livewire.admin.partials.activity-form')
    </x-modal>

    <x-modal wire:model="editModal" title="Edit Activity" box-class="max-w-4xl">
        @include('livewire.admin.partials.activity-form')
    </x-modal>

    <!-- View Modal -->
    <x-modal wire:model="viewModal" title="Activity Details" box-class="max-w-4xl">
        @if($selectedActivity)
        <div class="space-y-6">
            <!-- Basic Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <div class="p-3 bg-gray-50 rounded-lg">{{ $selectedActivity->title }}</div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <span class="badge badge-primary">{{ $selectedActivity->type }}</span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Points</label>
                    <div class="p-3 bg-gray-50 rounded-lg font-semibold text-green-600">{{ $selectedActivity->points }}</div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Duration</label>
                    <div class="p-3 bg-gray-50 rounded-lg">{{ $selectedActivity->duration_minutes }} minutes</div>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <div class="p-4 bg-gray-50 rounded-lg">{{ $selectedActivity->description }}</div>
            </div>

            <!-- Professions -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Associated Professions</label>
                <div class="p-3 bg-gray-50 rounded-lg">
                    <div class="flex flex-wrap gap-2">
                        @foreach($selectedActivity->professions as $profession)
                            <span class="badge badge-outline">{{ $profession->name }}</span>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Content -->
            @if($selectedActivity->type === 'VIDEO' && $selectedActivity->content_url)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Video URL</label>
                <div class="p-3 bg-gray-50 rounded-lg">
                    <a href="{{ $selectedActivity->content_url }}" target="_blank" class="text-blue-600 hover:underline">
                        {{ $selectedActivity->content_url }}
                    </a>
                </div>
            </div>
            @endif

            @if($selectedActivity->type === 'ARTICLE' && $selectedActivity->content_text)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Article Content</label>
                <div class="p-4 bg-gray-50 rounded-lg max-h-60 overflow-y-auto">
                    {!! nl2br(e($selectedActivity->content_text)) !!}
                </div>
            </div>
            @endif

            @if($selectedActivity->type === 'ATTACHMENT' && $selectedActivity->attachment_path)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Attachment</label>
                <div class="p-3 bg-gray-50 rounded-lg">
                    <a href="{{ asset('storage/' . $selectedActivity->attachment_path) }}" target="_blank" 
                       class="inline-flex items-center text-blue-600 hover:underline">
                        <x-icon name="o-document" class="w-4 h-4 mr-2" />
                        Download Attachment
                    </a>
                </div>
            </div>
            @endif

            <!-- Stats -->
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $selectedActivity->enrollments->count() }}</div>
                    <div class="text-sm text-blue-600">Total Enrollments</div>
                </div>
                <div class="bg-green-50 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-green-600">
                        {{ $selectedActivity->enrollments->where('status', 'COMPLETED')->count() }}
                    </div>
                    <div class="text-sm text-green-600">Completed</div>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-yellow-600">
                        {{ $selectedActivity->enrollments->where('status', 'IN_PROGRESS')->count() }}
                    </div>
                    <div class="text-sm text-yellow-600">In Progress</div>
                </div>
            </div>
        </div>
        @endif

        <x-slot:actions>
            <x-button label="Close" wire:click="closeModals" class="btn-ghost" />
        </x-slot:actions>
    </x-modal>
</div>