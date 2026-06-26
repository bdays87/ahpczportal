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
            @php $pending = $this->pendingJobs(); @endphp
            <x-button label="Process emails now {{ $pending > 0 ? '('.$pending.')' : '' }}" icon="o-bolt"
                class="btn-sm {{ $pending > 0 ? 'btn-warning' : 'btn-ghost' }}"
                wire:click="processQueue" spinner="processQueue"
                title="Sends out any queued emails/SMS now (no background worker needed)." />
            @if($pending > 0)
                <x-button label="Clear queue" icon="o-trash" class="btn-sm btn-ghost text-error"
                    wire:click="clearQueue" spinner="clearQueue"
                    wire:confirm="Delete ALL {{ $pending }} queued + failed job(s)? Unsent emails/SMS will be discarded." />
            @endif
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
                <x-button label="{{ $tab === 'email' ? 'Emails for upload' : 'Phones for upload' }}" icon="o-arrow-down-on-square"
                    class="btn-sm btn-primary btn-outline" wire:click="exportUploadList" spinner
                    title="Download a single-column CSV (just {{ $tab === 'email' ? 'emails' : 'phone numbers' }}) ready to upload as a recipient list." />
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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 items-start">
                <x-select label="Email provider" wire:model.live="emailProvider" :options="$emailProviderOptions"
                    option-label="name" option-value="id" />

                @if($emailProvider === 'nhume')
                    <div class="rounded-lg border border-gray-200 p-2 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Nhume credits left</span>
                            <x-button icon="o-arrow-path" class="btn-ghost btn-xs" wire:click="refreshNhumeCredits" spinner="refreshNhumeCredits" />
                        </div>
                        @if($nhumeError)
                            <span class="text-red-600 text-xs">{{ $nhumeError }}</span>
                        @elseif(! is_null($nhumeCredits))
                            <span class="text-2xl font-bold {{ $nhumeCredits > 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($nhumeCredits) }}</span>
                            <span class="text-xs text-gray-400">transactional</span>
                        @else
                            <span class="text-gray-400 text-xs flex items-center gap-1">
                                <span wire:loading wire:target="refreshNhumeCredits,updatedEmailProvider" class="loading loading-spinner loading-xs"></span>
                                checking…
                            </span>
                        @endif
                    </div>
                @endif
            </div>

            @if($emailProvider === 'nhume')
                <p class="text-xs text-amber-600">Nhume does not support CC or attachments — use the Default provider if you need them.</p>
            @endif

            <x-input label="Subject" wire:model="emailSubject" />
            <x-textarea label="Message" wire:model="emailBody" rows="7"
                hint="You can use placeholders: {name}, {surname}, {fullname}, {regnumber}" />

            <x-input label="CC (optional)" wire:model="emailCc" placeholder="name@example.com, another@example.com" />
            @error('emailCc') <p class="text-sm text-red-600">{{ $message }}</p> @enderror

            <div x-data="{ uploading: false }"
                 x-on:livewire-upload-start="uploading = true"
                 x-on:livewire-upload-finish="uploading = false"
                 x-on:livewire-upload-error="uploading = false">
                <x-input label="Attachments (optional)" wire:model="emailAttachments" type="file" multiple
                    accept=".pdf,.ppt,.pptx,.doc,.docx,.png,.jpg,.jpeg"
                    hint="Allowed: pdf, ppt, pptx, doc, docx, png, jpg — up to 5 MB each, max 5 files." />
                <p x-show="uploading" class="text-sm text-blue-600 mt-1 flex items-center gap-1">
                    <span class="loading loading-spinner loading-xs"></span> Uploading attachment(s)...
                </p>
                @error('emailAttachments.*') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                @if(is_array($emailAttachments) && count($emailAttachments) > 0)
                    <ul class="text-xs text-gray-500 mt-1 list-disc ml-5">
                        @foreach($emailAttachments as $att)
                            <li>{{ method_exists($att, 'getClientOriginalName') ? $att->getClientOriginalName() : 'file' }}</li>
                        @endforeach
                    </ul>
                @endif

                <div class="mt-3">
                    <x-input label="Add recipients from CSV (optional)" wire:model="recipientCsv" type="file" accept=".csv,.txt"
                        hint="Any column with valid emails is read (header ignored). These are sent in addition to the filtered/selected contacts above." />
                    @error('recipientCsv') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                    @if($recipientCsv)
                        <p class="text-xs text-green-600 mt-1">CSV attached — its emails will be added to the recipients.</p>
                    @endif
                </div>
            </div>

            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.emailModal = false" wire:loading.attr="disabled" wire:target="sendEmail" />
                <x-button label="Send Email" type="submit" class="btn-primary" spinner="sendEmail"
                    wire:loading.attr="disabled" wire:target="sendEmail" x-bind:disabled="uploading" />
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
