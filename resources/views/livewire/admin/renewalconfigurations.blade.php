<div>
    <x-breadcrumbs :items="$breadcrumbs"    class="bg-base-300 p-3 rounded-box mt-2" />
    @can('renewalconfiguration.access')
    <x-card title="Renewal Configurations" separator class="mt-5 border-2 border-gray-200">
        <x-tabs wire:model="tab">
            <x-tab name="renewalfees" label="Renewal Fees" icon="o-credit-card">
                <livewire:admin.components.renewalfees />
            </x-tab>
            <x-tab name="renewaldocuments-tab" label="Renewal Documents" icon="o-document">
               <livewire:admin.components.renewaldocuments />
            </x-tab>
        </x-tabs>
    </x-card>
    @else
    <x-alert class="alert-error mt-3" title="You do not have permission to view renewal configurations." />
    @endcan
</div>
 