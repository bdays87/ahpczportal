<div>
    <x-breadcrumbs :items="$breadcrumbs"    class="bg-base-300 p-3 rounded-box mt-2" />
    @can('assessments.access')
    <x-card title="Assessments" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            <x-input type="text" placeholder="Search" wire:model.live="search" />
            <x-input type="date" placeholder="From Date" wire:model.live="fromdate" />
            <x-input type="date" placeholder="To Date" wire:model.live="todate" />
            <x-button class="btn-sm btn-primary btn-outline" label="Search" wire:click="search" />
        </x-slot:menu>
      
        <table class="table table-zebra">
            <thead>
                <tr>
                    <th>Practitioner</th>
                    <th>Profession</th>
                    <th>Customertype</th>
                    <th>Status</th>
                    <th>Time decay</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customerprofessions as $customerprofession)
                <tr>
                    <td>{{ $customerprofession->customer->name }} {{ $customerprofession->customer->surname }}</td>
                    <td>{{ $customerprofession->profession->name }}</td> 
                    <td>{{ $customerprofession->customertype->name }}</td>
                    <td>
                        <x-badge value="{{ $customerprofession->status }}" class="{{ $customerprofession->status=='PENDING' ? 'badge-warning' : 'badge-success' }}" />
                    </td>
                    <td>
                        {{ $customerprofession->updated_at->diffForHumans() }}
                    </td>
                    <td>
                        <div class="flex items-center justify-end space-x-2">
                          <x-button icon="o-eye" label="View" class="btn-sm btn-info btn-outline" link="{{ route('assessment.show',['uuid'=>$customerprofession->uuid]) }}"  spinner />
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="text-center text-red-500 p-5 font-bold bg-red-50">
                       No profession found.</div>
                        </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </x-card>   
    @else
    <x-alert class="alert-error mt-3" title="You do not have permission to view assessments." />
    @endcan

   
</div>
