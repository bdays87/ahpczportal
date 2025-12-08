<div>
    <x-card title="Journals" separator class="border-2 border-gray-200 mt-3">
        <div class="mb-4">
            <x-input 
                wire:model.live.debounce.300ms="search" 
                placeholder="Search journals by title or author..." 
                icon="o-magnifying-glass" />
        </div>

        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Published Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($journals as $journal)
                    <tr>
                        <td>
                            <div class="font-bold">{{ $journal->title }}</div>
                        </td>
                        <td>{{ $journal->author ?? 'N/A' }}</td>
                        <td>{{ $journal->published_date ? $journal->published_date->format('M d, Y') : 'N/A' }}</td>
                        <td>
                            <x-button 
                                label="View Journal" 
                                icon="o-arrow-top-right-on-square"
                                class="btn-primary btn-sm"
                                :link="$journal->link"
                                target="_blank" />
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-8">
                            <div class="text-gray-500">
                                <x-icon name="o-document-text" class="w-12 h-12 mx-auto mb-2 opacity-50" />
                                <p>No journals found.</p>
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
            {{ $journals->links() }}
        </div>
    </x-card>
</div>
