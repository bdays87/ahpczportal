<div>
    <x-breadcrumbs :items="[
        [
            'label' => 'Dashboard',
            'icon' => 'o-home',
            'link' => route('dashboard'),
        ],
        [
            'label' => 'Resources',
        ]
    ]" class="bg-base-300 p-3 rounded-box mt-2" />
    
    <x-card title="Downloadable Resources" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            <x-input placeholder="Search resources..." wire:model.live="search" />
        </x-slot:menu>

        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @forelse($resources as $resource)
            <div class="card bg-base-100 shadow-md border border-gray-200 hover:shadow-lg transition-shadow">
                <div class="card-body">
                    <h3 class="card-title text-lg">{{ $resource->title }}</h3>
                    
                    @if($resource->description)
                    <p class="text-sm text-gray-600 mt-2">{{ Str::limit($resource->description, 100) }}</p>
                    @endif

                    <div class="mt-4 flex items-center justify-between text-xs text-gray-500">
                        <span>
                            <x-icon name="o-document" class="w-4 h-4 inline" />
                            {{ $resource->file_name }}
                        </span>
                        @if($resource->file_size)
                        <span>
                            {{ number_format($resource->file_size / 1024, 2) }} KB
                        </span>
                        @endif
                    </div>

                    <div class="card-actions justify-end mt-4">
                        <x-button 
                            icon="o-arrow-down-tray" 
                            label="Download" 
                            class="btn-primary btn-sm" 
                            wire:click="download({{ $resource->id }})" 
                            spinner />
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full">
                <x-alert class="alert-info" title="No resources available at the moment." />
            </div>
            @endforelse
        </div>
    </x-card>
</div>
