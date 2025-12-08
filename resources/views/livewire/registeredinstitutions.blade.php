<div>
    {{-- If your happiness depends on money, you will never be happy with yourself. --}}
    <div class="mx-auto max-w-7xl">
        <x-card title="Registered Institutions" separator class="mt-5 border-2 border-gray-200">
            <x-slot:menu>
                <x-input wire:model.live.debounce.300ms="search" label="Search Institutions" placeholder="Search by institution name..." />
            </x-slot:menu>
            <x-hr />
            <x-table :headers="$headers" :rows="$institutions" with-pagination>
                @scope('cell_practitioner', $practitioner)
                    <div class="font-semibold text-gray-800">{{ $practitioner->customerprofession?->customer?->name }} {{ $practitioner->customerprofession?->customer?->surname }}</div>
                @endscope
                @scope('cell_period', $practitioner)
                    <div class="font-semibold text-gray-800">{{ $practitioner->period }}</div>
                @endscope
                @scope('cell_status', $practitioner)
                    <div class="font-semibold text-gray-800">{{ $practitioner->status }}</div>
                @endscope
               
                <x-slot:empty>
                    <x-alert class="alert-error" title="No institutions found." />
                </x-slot:empty>
            </x-table>
        </x-card>
        
    </div>
</div>
