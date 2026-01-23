<div>
    <x-card title="Latest Resources" separator class="border-2 border-gray-200">
        <x-slot:menu>
            <x-button 
                icon="o-arrow-right" 
                label="View All" 
                class="btn-sm btn-ghost" 
                :link="route('customer.resources.index')" 
            />
        </x-slot:menu>

        @if($resources->count() > 0)
        <div class="grid grid-cols-1 gap-4">
            @foreach($resources as $resource)
            <div class="border rounded-lg p-4 hover:shadow-lg transition-shadow bg-base-100">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <h3 class="font-semibold text-base mb-2 line-clamp-2">{{ $resource->title }}</h3>
                        @if($resource->description)
                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ Str::limit($resource->description, 100) }}</p>
                        @endif
                        <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500">
                            <span class="flex items-center gap-1">
                                <x-icon name="o-document" class="w-4 h-4" />
                                {{ $resource->file_name }}
                            </span>
                            @if($resource->file_size)
                            <span class="flex items-center gap-1">
                                <x-icon name="o-arrow-down-tray" class="w-4 h-4" />
                                {{ number_format($resource->file_size / 1024, 2) }} KB
                            </span>
                            @endif
                            @if($resource->created_at)
                            <span class="flex items-center gap-1">
                                <x-icon name="o-calendar" class="w-4 h-4" />
                                {{ $resource->created_at->format('M d, Y') }}
                            </span>
                            @endif
                        </div>
                    </div>
                    <x-button 
                        label="Download" 
                        icon="o-arrow-down-tray"
                        class="btn-primary btn-sm flex-shrink-0"
                        wire:click="download({{ $resource->id }})"
                        spinner
                        />
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8">
            <div class="text-gray-500">
                <x-icon name="o-folder-open" class="w-12 h-12 mx-auto mb-2 opacity-50" />
                <p>No resources available.</p>
            </div>
        </div>
        @endif
    </x-card>
</div>
