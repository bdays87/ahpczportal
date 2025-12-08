<div>
    @if($journal)
    <div class="max-w-4xl mx-auto">
        <x-button 
            icon="o-arrow-left" 
            label="Back to Journals" 
            class="btn-ghost mb-4"
            :link="route('customer.journals')" />

        <x-card separator class="border-2 border-gray-200">
            <div class="mb-6">
                <h1 class="text-3xl font-bold mb-4">{{ $journal->title }}</h1>
                
                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 mb-4">
                    @if($journal->author)
                    <span class="flex items-center gap-2">
                        <x-icon name="o-user" class="w-4 h-4" />
                        <span class="font-medium">Author:</span> {{ $journal->author }}
                    </span>
                    @endif
                    @if($journal->published_date)
                    <span class="flex items-center gap-2">
                        <x-icon name="o-calendar" class="w-4 h-4" />
                        <span class="font-medium">Published:</span> {{ $journal->published_date->format('F d, Y') }}
                    </span>
                    @endif
                </div>
            </div>

            <div class="mb-6">
                <x-button 
                    label="Open Journal Link" 
                    icon="o-arrow-top-right-on-square"
                    class="btn-primary btn-lg"
                    :link="$journal->link"
                    target="_blank" />
            </div>

            <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="flex items-center justify-between text-sm text-gray-500">
                    <span>Created on {{ $journal->created_at->format('F d, Y') }}</span>
                    @if($journal->creator)
                    <span>By {{ $journal->creator->name }}</span>
                    @endif
                </div>
            </div>
        </x-card>
    </div>
    @else
    <div class="text-center py-12">
        <x-icon name="o-exclamation-triangle" class="w-16 h-16 mx-auto mb-4 text-yellow-500" />
        <p class="text-gray-500 text-lg">Journal not found.</p>
    </div>
    @endif
</div>
