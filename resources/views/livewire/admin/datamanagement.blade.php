<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />

    {{-- Template downloads --}}
    <x-card title="Download Import Templates" separator class="mt-5 border-2 border-gray-200">
        <p class="text-sm text-gray-500 mb-4">Download a CSV template for each import type, populate it with your data, then upload it in the corresponding tab below.</p>
        <div class="flex flex-wrap gap-3">
            <x-button label="Professions Template" icon="o-arrow-down-tray" class="btn-outline btn-sm"
                wire:click="downloadtemplate('professions')" spinner />
            <x-button label="Customers Template" icon="o-arrow-down-tray" class="btn-outline btn-sm"
                wire:click="downloadtemplate('customers')" spinner />
            <x-button label="Users Template" icon="o-arrow-down-tray" class="btn-outline btn-sm"
                wire:click="downloadtemplate('users')" spinner />
            <x-button label="Customer Professions Template" icon="o-arrow-down-tray" class="btn-outline btn-sm"
                wire:click="downloadtemplate('customerprofessions')" spinner />
            <x-button label="Customer Registrations Template" icon="o-arrow-down-tray" class="btn-outline btn-sm"
                wire:click="downloadtemplate('customerregistrations')" spinner />
            <x-button label="Customer Applications Template" icon="o-arrow-down-tray" class="btn-outline btn-sm"
                wire:click="downloadtemplate('customerapplications')" spinner />
            <x-button label="CDP Points Template" icon="o-arrow-down-tray" class="btn-outline btn-sm"
                wire:click="downloadtemplate('customercdp')" spinner />
        </div>
    </x-card>

    <x-card class="mt-5 border-2 border-gray-200">
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
