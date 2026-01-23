<div>
    <x-card title="Latest Journals" separator class="border-2 border-gray-200">
        <x-slot:menu>
            <x-button 
                icon="o-arrow-right" 
                label="View All" 
                class="btn-sm btn-ghost" 
                :link="route('customer.journals')" 
            />
        </x-slot:menu>

        @if($journals->count() > 0)
        <div class="grid grid-cols-1 gap-4">
            @foreach($journals as $journal)
            <div class="border rounded-lg p-4 hover:shadow-lg transition-shadow bg-base-100">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <h3 class="font-semibold text-base mb-2 line-clamp-2">{{ $journal->title }}</h3>
                        <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500 mb-3">
                            @if($journal->author)
                            <span class="flex items-center gap-1">
                                <x-icon name="o-user" class="w-4 h-4" />
                                {{ $journal->author }}
                            </span>
                            @endif
                            @if($journal->published_date)
                            <span class="flex items-center gap-1">
                                <x-icon name="o-calendar" class="w-4 h-4" />
                                {{ $journal->published_date->format('M d, Y') }}
                            </span>
                            @endif
                        </div>
                    </div>
                    <x-button 
                        label="View" 
                        icon="o-arrow-top-right-on-square"
                        class="btn-primary btn-sm flex-shrink-0"
                        :link="route('customer.journals.view', ['slug' => $journal->slug])"
                        />
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8">
            <div class="text-gray-500">
                <x-icon name="o-document-text" class="w-12 h-12 mx-auto mb-2 opacity-50" />
                <p>No journals available.</p>
            </div>
        </div>
        @endif
    </x-card>
</div>

