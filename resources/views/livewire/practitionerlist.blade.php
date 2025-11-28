<div>
    <div class="mx-auto max-w-7xl">
 <x-card title="Compliant  List" separator class="mt-5 border-2 border-gray-200">
    <x-alert class="alert-info" title="This is a list of compliant practitioners ,if you wish to search for a particular practitioner, you can use the search by name or email functionality below." />
   <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
    <div>
        <x-select wire:model.live="province_id" placeholder="Select Province" :options="$provinces" label="Province" />
    </div>
    <div>
        <x-select wire:model.live="city_id" placeholder="Select City" :options="$cities" label="City" />
    </div>
    <div>
        <x-select wire:model.live="profession_id" :options="$professions" label="Profession" />
    </div>
    
    <div>
        <x-select wire:model.live="gender" :options="$genderOptions" label="Gender" />
    </div>
    <div>
        <x-input wire:model.live="search" label="Search by name or email" placeholder="Search" />
    </div>
    
   </div>
   <x-table :headers="$headers" :rows="$applications" with-pagination >
        @scope('cell_gender', $application)
        <div>{{ $application->customerprofession->customer->gender }}</div>
        @endscope
        @scope('cell_regnumber', $application)
        <div>{{ $application->customerprofession->customer->regnumber }}</div>
        @endscope
        @scope('cell_name', $application)
        <div>{{ $application->customerprofession->customer->name }}</div>
        @endscope
        @scope('cell_surname', $application)
        <div>{{ $application->customerprofession->customer->surname }}</div>
        @endscope
        @scope('cell_profession', $application)
        <div>{{ $application->customerprofession->profession->name }}</div>
        @endscope
        @scope('cell_province', $application)
        <div>{{ $application->customerprofession->customer->province->name }}</div>
        @endscope
            @scope('cell_city', $application)
            <div>{{ $application->customerprofession->customer->city->name }}</div>
            @endscope
            @scope('cell_status', $application)
            <x-badge value="{{ $application->isValid() ? 'Valid' : 'Invalid' }}" class="{{ $application->isValid() ? 'badge-success' : 'badge-error' }}" />
                <small>Expiry Date: {{ $application->certificate_expiry_date->format('d-m-Y') }}</small>
            @endscope
   <x-slot:empty>
    <x-alert class="alert-error" title="No compliant practitioners found." />
  
   </x-slot:empty>
   </x-table>
 </x-card>
 </div>
</div>
