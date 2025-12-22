<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />
  
    <x-card  class="mt-5 border-2 border-gray-200">
        <x-tabs wire:model="selectedTab">
            <x-tab name="professionimports-tab" label="Professions" icon="o-arrow-up-tray">
             <livewire:admin.components.professionimports />
            </x-tab>
            <x-tab name="tricks-tab" label="Customers" icon="o-arrow-up-tray">
                <livewire:admin.components.customerimports />
            </x-tab>
            <x-tab name="users-tab" label="Users" icon="o-arrow-up-tray">
                <livewire:admin.components.customeruserimports />
            </x-tab>
            <x-tab name="customerprofessions-tab" label="Customer professions" icon="o-arrow-up-tray">
                <livewire:admin.components.customerprofessionimports />
            </x-tab>
            <x-tab name="customerregistrations-tab" label="Customer registrations" icon="o-arrow-up-tray">
                <livewire:admin.components.customerregistrationimports />
            </x-tab>

            <x-tab name="customerapplications-tab" label="Customer applications" icon="o-arrow-up-tray">
                <livewire:admin.components.customerapplicationimports />
            </x-tab>
            <x-tab name="customerrenewalpoints-tab" label="Customer renewal points" icon="o-arrow-up-tray">
                <livewire:admin.components.customercdpimports />
            </x-tab>
        </x-tabs>
    </x-card>
</div>
