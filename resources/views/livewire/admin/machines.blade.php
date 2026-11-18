<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />
    @can('customers.access')
        <livewire:admin.components.practitionermachines />
    @else
        <x-alert class="alert-error mt-3" title="You do not have permission to view machines." />
    @endcan
</div>
