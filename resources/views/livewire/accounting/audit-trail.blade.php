<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />

    <x-card title="Accounting Audit Trail" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            <x-input placeholder="Search…" wire:model.live="search" icon="o-magnifying-glass" />
            <x-select wire:model.live="filterAction" placeholder="All Actions"
                :options="[
                    ['id'=>'CREATE','name'=>'Create'],
                    ['id'=>'UPDATE','name'=>'Update'],
                    ['id'=>'DELETE','name'=>'Delete'],
                    ['id'=>'POST','name'=>'Post'],
                    ['id'=>'REVERSE','name'=>'Reverse'],
                    ['id'=>'APPROVE','name'=>'Approve'],
                ]" option-label="name" option-value="id" />
            <x-select wire:model.live="filterEntity" placeholder="All Entities"
                :options="[
                    ['id'=>'JournalEntry','name'=>'Journal Entry'],
                    ['id'=>'APInvoice','name'=>'AP Invoice'],
                    ['id'=>'ARInvoice','name'=>'AR Invoice'],
                    ['id'=>'Payment','name'=>'Payment'],
                    ['id'=>'Receipt','name'=>'Receipt'],
                ]" option-label="name" option-value="id" />
            <x-input wire:model.live="filterDateFrom" type="date" placeholder="From Date" />
            <x-input wire:model.live="filterDateTo" type="date" placeholder="To Date" />
        </x-slot:menu>

        <x-table :headers="$headers" :rows="$logs" with-pagination>
            @scope('cell_action', $log)
                @php
                    $cls = match($log->action) {
                        'CREATE'  => 'badge-success',
                        'UPDATE'  => 'badge-info',
                        'DELETE'  => 'badge-error',
                        'POST'    => 'badge-primary',
                        'REVERSE' => 'badge-warning',
                        default   => 'badge-ghost',
                    };
                @endphp
                <span class="badge {{ $cls }}">{{ $log->action }}</span>
            @endscope

            @scope('cell_created_at', $log)
                {{ $log->created_at->format('d M Y H:i:s') }}
            @endscope

            @scope('action_btn', $log)
                <x-button icon="o-eye" class="btn-xs btn-info btn-outline"
                    wire:click="viewLog({{ $log->id }})" spinner />
            @endscope

            <x-slot:empty>
                <x-alert class="alert-info" title="No audit entries found." />
            </x-slot:empty>
        </x-table>
    </x-card>

    {{-- View Audit Modal --}}
    <x-modal wire:model="viewModal" title="Audit Trail Details" box-class="max-w-3xl">
        @if($selectedLog)
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div><strong>Module:</strong> <span class="badge">{{ $selectedLog->module }}</span></div>
                <div><strong>Action:</strong> <span class="badge">{{ $selectedLog->action }}</span></div>
                <div><strong>Record Type:</strong> {{ $selectedLog->record_type }}</div>
                <div><strong>Record ID:</strong> {{ $selectedLog->record_id }}</div>
                <div><strong>Reference:</strong> {{ $selectedLog->reference }}</div>
                <div><strong>User:</strong> {{ $selectedLog->user?->name ?? 'System' }}</div>
                <div><strong>IP Address:</strong> {{ $selectedLog->ip_address }}</div>
                <div><strong>Timestamp:</strong> {{ $selectedLog->created_at->format('d M Y H:i:s') }}</div>
            </div>

            @if($selectedLog->old_values)
                <div class="divider">Old Values</div>
                <div class="bg-base-200 p-3 rounded-lg">
                    <pre class="text-xs">{{ json_encode(json_decode($selectedLog->old_values, true), JSON_PRETTY_PRINT) }}</pre>
                </div>
            @endif

            @if($selectedLog->new_values)
                <div class="divider">New Values</div>
                <div class="bg-base-200 p-3 rounded-lg">
                    <pre class="text-xs">{{ json_encode(json_decode($selectedLog->new_values, true), JSON_PRETTY_PRINT) }}</pre>
                </div>
            @endif
        @endif
        <x-slot:actions>
            <x-button label="Close" @click="$wire.viewModal=false" />
        </x-slot:actions>
    </x-modal>
</div>
