<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />

    @can('customers.access')
    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mt-4">
        <x-card class="border border-gray-200">
            <div class="text-xs text-gray-500 uppercase">Total Contacts</div>
            <div class="text-2xl font-bold">{{ number_format($summary['total']) }}</div>
        </x-card>
        <x-card class="border border-gray-200">
            <div class="text-xs text-gray-500 uppercase">Compliant</div>
            <div class="text-2xl font-bold text-green-600">{{ number_format($summary['compliant']) }}</div>
        </x-card>
        <x-card class="border border-gray-200">
            <div class="text-xs text-gray-500 uppercase">Non-compliant</div>
            <div class="text-2xl font-bold text-red-600">{{ number_format($summary['noncompliant']) }}</div>
        </x-card>
        <x-card class="border border-gray-200">
            <div class="text-xs text-gray-500 uppercase">With Email</div>
            <div class="text-2xl font-bold text-blue-600">{{ number_format($summary['with_email']) }}</div>
        </x-card>
        <x-card class="border border-gray-200">
            <div class="text-xs text-gray-500 uppercase">With Phone</div>
            <div class="text-2xl font-bold text-blue-600">{{ number_format($summary['with_phone']) }}</div>
        </x-card>
    </div>

    <x-card title="Customer Contacts" separator class="mt-4 border-2 border-gray-200">
        <x-slot:menu>
            @if($tab === 'email')
                <x-button label="Send Email" icon="o-envelope" class="btn-primary btn-sm" wire:click="openEmailModal" spinner />
            @else
                <x-button label="Send SMS" icon="o-chat-bubble-left-right" class="btn-primary btn-sm" wire:click="openSmsModal" spinner />
            @endif
        </x-slot:menu>

        {{-- TABS --}}
        <div class="flex gap-2 border-b border-gray-200 mb-4">
            <button type="button" wire:click="setTab('email')"
                class="px-4 py-2 text-sm font-semibold {{ $tab === 'email' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500' }}">
                <x-icon name="o-envelope" class="w-4 h-4 inline" /> Emails
            </button>
            <button type="button" wire:click="setTab('phone')"
                class="px-4 py-2 text-sm font-semibold {{ $tab === 'phone' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500' }}">
                <x-icon name="o-phone" class="w-4 h-4 inline" /> Phone Numbers
            </button>
        </div>

        {{-- FILTERS --}}
        <div class="grid grid-cols-1 md:grid-cols-6 gap-2 mb-4">
            <x-input placeholder="Search name, reg #, email, phone" wire:model.live.debounce.400ms="search" class="md:col-span-2" />
            <x-select placeholder="Compliance" wire:model.live="compliance" :options="$complianceOptions" option-label="name" option-value="id" />
            <x-select placeholder="Province" wire:model.live="province_id" :options="$provinces" option-label="name" option-value="id" />
            <x-select placeholder="Profession" wire:model.live="profession_id" :options="$professions" option-label="name" option-value="id" />
            <x-select placeholder="Register Type" wire:model.live="registertype_id" :options="$registertypes" option-label="name" option-value="id" />
        </div>

        {{-- ACTION BAR --}}
        <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
            <div class="text-sm text-gray-500">
                {{ count($selected) }} selected
                @if(count($selected) > 0)
                    <button type="button" wire:click="$set('selected', [])" class="text-blue-600 underline ml-1">clear</button>
                @endif
                <span class="ml-2">— exports/sends apply to selected rows, or all filtered contacts when none are selected.</span>
            </div>
            <div class="flex gap-2">
                <x-button label="CSV" icon="o-document-text" class="btn-sm btn-outline" wire:click="exportCsv" spinner />
                <x-button label="Excel" icon="o-table-cells" class="btn-sm btn-outline" wire:click="exportExcel" spinner />
                <x-button label="PDF" icon="o-document" class="btn-sm btn-outline" wire:click="exportPdf" spinner />
                <x-button label="Clear filters" icon="o-x-mark" class="btn-sm btn-ghost" wire:click="clearFilters" spinner />
            </div>
        </div>

        {{-- TABLE --}}
        <x-table :headers="$headers" :rows="$contacts" with-pagination>
            @scope('cell_selection', $contact)
            <input type="checkbox" value="{{ $contact->id }}" wire:model.live="selected" class="checkbox checkbox-sm" />
            @endscope

            @scope('cell_fullname', $contact)
            {{ trim($contact->name.' '.$contact->surname) }}
            @endscope

            @scope('cell_province', $contact)
            {{ $contact->province?->name ?? 'N/A' }}
            @endscope

            @scope('cell_compliance', $contact)
            @if($contact->is_compliant)
                <x-badge value="Compliant" class="badge-success badge-soft" />
            @else
                <x-badge value="Non-compliant" class="badge-error badge-soft" />
            @endif
            @endscope

            <x-slot:empty>
                <x-alert class="alert-warning" title="No contacts found for the selected filters." />
            </x-slot:empty>
        </x-table>
    </x-card>
    @else
    <x-alert class="alert-error mt-3" title="You do not have permission to view customer contacts." />
    @endcan

    {{-- EMAIL COMPOSER --}}
    <x-modal wire:model="emailModal" title="Send Email" subtitle="Sends to all contacts matching the current filters (or selected rows)." box-class="max-w-2xl">
        <x-form wire:submit="sendEmail">
            <x-input label="Subject" wire:model="emailSubject" />
            <x-textarea label="Message" wire:model="emailBody" rows="8"
                hint="You can use placeholders: {name}, {surname}, {fullname}, {regnumber}" />
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.emailModal = false" wire:loading.attr="disabled" wire:target="sendEmail" />
                <x-button label="Send Email" type="submit" class="btn-primary" spinner="sendEmail" wire:loading.attr="disabled" wire:target="sendEmail" />
            </x-slot:actions>
        </x-form>
    </x-modal>

    {{-- SMS COMPOSER --}}
    <x-modal wire:model="smsModal" title="Send SMS" subtitle="Sends to all contacts matching the current filters (or selected rows)." box-class="max-w-2xl">
        <x-form wire:submit="sendSms">
            <x-textarea label="Message" wire:model.live="smsBody" rows="5"
                hint="You can use placeholders: {name}, {surname}, {fullname}, {regnumber}" />
            <p class="text-xs text-gray-500">{{ strlen($smsBody ?? '') }} characters</p>
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.smsModal = false" wire:loading.attr="disabled" wire:target="sendSms" />
                <x-button label="Send SMS" type="submit" class="btn-primary" spinner="sendSms" wire:loading.attr="disabled" wire:target="sendSms" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
