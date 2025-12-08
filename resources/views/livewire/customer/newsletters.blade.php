<div>
    <x-card title="Newsletters" separator class="border-2 border-gray-200 mt-3">
        <div class="mb-4 flex gap-4 items-end">
            <div class="flex-1">
                <x-input 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Search newsletters..." 
                    icon="o-magnifying-glass" />
            </div>
            <div class="w-48">
                <x-input 
                    type="number" 
                    wire:model.live="selectedYear" 
                    label="Year" 
                    placeholder="Year" />
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Published Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($newsletters as $newsletter)
                    <tr>
                        <td>
                            <div class="font-bold">{{ $newsletter->title }}</div>
                        </td>
                        <td>{{ $newsletter->published_date ? $newsletter->published_date->format('M d, Y') : 'N/A' }}</td>
                        <td>
                            <x-button 
                                label="View" 
                                icon="o-arrow-top-right-on-square"
                                class="btn-primary btn-sm"
                                :link="$newsletter->link"
                                target="_blank" />
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center py-8">
                            <div class="text-gray-500">
                                <x-icon name="o-envelope" class="w-12 h-12 mx-auto mb-2 opacity-50" />
                                <p>No newsletters found.</p>
                                @if($search)
                                <p class="text-sm mt-2">Try adjusting your search terms.</p>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $newsletters->links() }}
        </div>
    </x-card>
</div>

