<div>
    <div class="mx-auto max-w-7xl">
        <x-card title="Accredited Institutions" separator class="mt-5 border-2 border-gray-200">
            <x-alert class="alert-info" title="This is a list of accredited institutions that offer recognized qualifications and training programs in allied health professions. Use the search functionality below to find specific institutions." />
            
            <div class="mb-6">
                <x-input wire:model.live.debounce.300ms="search" label="Search Institutions" placeholder="Search by institution name..." />
            </div>
            
            <x-table :headers="$headers" :rows="$institutions" with-pagination>
                @scope('cell_name', $institution)
                    <div class="font-semibold text-gray-800">{{ $institution->name }}</div>
                @endscope
                
                @scope('cell_accredited', $institution)
                    <x-badge 
                        value="{{ $institution->accredited ? 'Accredited' : 'Not Accredited' }}" 
                        class="{{ $institution->accredited ? 'badge-success' : 'badge-warning' }}" 
                    />
                @endscope
                
                <x-slot:empty>
                    <x-alert class="alert-error" title="No institutions found matching your search criteria." />
                </x-slot:empty>
            </x-table>
        </x-card>
    </div>
</div>





















