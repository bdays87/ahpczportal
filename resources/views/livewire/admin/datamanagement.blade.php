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

    {{-- Process imports / run migrations --}}
    <x-card title="Process Imports (Run Migrations)" separator class="mt-5 border-2 border-gray-200">
        <p class="text-sm text-gray-500 mb-4">
            After uploading data in the tabs below, run these steps <b>in order</b> to move the staged data into the live tables.
            Click <b>Run</b> on a step to execute it and watch the output in the console below — no terminal needed.
        </p>

        <div class="space-y-2">
            @foreach($migrations as $i => $m)
                @php $pending = $this->pendingCount($m['table']); @endphp
                <div class="flex flex-wrap items-center justify-between gap-3 border border-gray-200 rounded-lg p-3">
                    <div class="min-w-0">
                        <div class="font-semibold text-sm flex flex-wrap items-center gap-2">
                            <span>{{ $i + 1 }}. {{ $m['label'] }}</span>
                            <code class="text-xs bg-gray-100 px-2 py-0.5 rounded text-gray-600">php artisan {{ $m['signature'] }}</code>
                        </div>
                        <div class="text-xs text-gray-500 mt-0.5">{{ $m['description'] }}</div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if(! is_null($pending))
                            <x-badge value="{{ $pending }} pending" class="{{ $pending > 0 ? 'badge-warning' : 'badge-success' }} badge-soft" />
                        @endif
                        <x-button label="Run" icon="o-play" class="btn-primary btn-sm"
                            wire:click="runMigration('{{ $m['signature'] }}')"
                            wire:confirm="Run this command now?&#10;&#10;php artisan {{ $m['signature'] }}"
                            spinner="runMigration('{{ $m['signature'] }}')" />
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Output console --}}
        <div class="mt-4">
            <div wire:loading wire:target="runMigration" class="text-sm text-blue-600 flex items-center gap-2 mb-2">
                <span class="loading loading-spinner loading-sm"></span> Running… please wait, do not close this page.
            </div>
            @if($commandOutput)
                <div class="text-xs text-gray-400 mb-1">{{ $lastRun }}</div>
                <pre class="bg-gray-900 text-green-400 text-xs p-4 rounded-lg overflow-auto max-h-96 whitespace-pre-wrap font-mono">{{ $commandOutput }}</pre>
            @endif
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
