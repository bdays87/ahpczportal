<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />

    <x-card title="Audit log" subtitle="Every change made in the system: who did it, when, and what the record was before. Sign-ins are listed too." separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            <x-button label="Download CSV" icon="o-arrow-down-tray" class="btn-sm btn-outline" wire:click="export" spinner="export" />
        </x-slot:menu>

        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-2 mb-3">
            <x-input placeholder="Search record id, screen, values" wire:model.live.debounce.500ms="search" icon="o-magnifying-glass" class="lg:col-span-2" />
            <x-input placeholder="User (username or name)" wire:model.live.debounce.500ms="username" icon="o-user" />
            <x-select wire:model.live="action" placeholder="All actions" option-label="name" option-value="id"
                :options="[['id'=>'created','name'=>'Created'],['id'=>'updated','name'=>'Updated'],['id'=>'deleted','name'=>'Deleted'],['id'=>'restored','name'=>'Restored'],['id'=>'login','name'=>'Sign in'],['id'=>'logout','name'=>'Sign out'],['id'=>'login_failed','name'=>'Failed sign in']]" />
            <x-select wire:model.live="table" placeholder="All tables" :options="$tables->map(fn($t)=>['id'=>$t,'name'=>$t])->all()" option-label="name" option-value="id" />
            <x-button label="Clear" class="btn-outline" wire:click="clear" />
            <x-input type="date" label="From" wire:model.live="from" />
            <x-input type="date" label="To" wire:model.live="to" />
        </div>

        <div class="overflow-x-auto">
            <table class="table table-zebra table-sm">
                <thead>
                    <tr>
                        <th>When</th>
                        <th>Who</th>
                        <th>Action</th>
                        <th>Table</th>
                        <th>Record</th>
                        <th>Done from</th>
                        <th>IP</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        @php
                            $color = match($log->action) {
                                'created' => 'badge-success', 'updated' => 'badge-info', 'deleted' => 'badge-error',
                                'restored' => 'badge-warning', 'login_failed' => 'badge-error', default => 'badge-ghost',
                            };
                        @endphp
                        <tr wire:key="log-{{ $log->id }}">
                            <td class="whitespace-nowrap">{{ $log->created_at?->format('d M Y H:i:s') }}</td>
                            <td>
                                <div class="font-semibold">{{ $log->username ?: '-' }}</div>
                                <div class="text-xs text-gray-400">{{ $log->user_name }}</div>
                            </td>
                            <td><x-badge :value="str_replace('_', ' ', $log->action)" class="{{ $color }} badge-soft badge-sm" /></td>
                            <td>{{ $log->table_name ?: '-' }}</td>
                            <td>{{ $log->record_id ?: '-' }}</td>
                            <td class="max-w-xs truncate" title="{{ $log->operation }}">{{ $log->operation ?: '-' }}</td>
                            <td>{{ $log->ip ?: '-' }}</td>
                            <td><x-button icon="o-eye" class="btn-xs btn-outline" wire:click="view({{ $log->id }})" tooltip="See details" /></td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-gray-400">Nothing matches.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $logs->links() }}</div>
    </x-card>

    <x-modal wire:model="modal" title="Audit details" box-class="max-w-4xl" separator>
        @if($selected)
            @php
                $old = $selected->old_values ?? [];
                $new = $selected->new_values ?? [];
                $columns = array_values(array_unique(array_merge(array_keys($old), array_keys($new))));
                $show = fn ($v) => $v === null ? '—' : (is_scalar($v) ? (string) $v : json_encode($v));
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-1 text-sm mb-3">
                <div><span class="text-gray-500">When:</span> {{ $selected->created_at?->format('d M Y H:i:s') }}</div>
                <div><span class="text-gray-500">Action:</span> <strong>{{ str_replace('_', ' ', $selected->action) }}</strong></div>
                <div><span class="text-gray-500">Who:</span> <strong>{{ $selected->username ?: '-' }}</strong> {{ $selected->user_name ? '('.$selected->user_name.')' : '' }}</div>
                <div><span class="text-gray-500">User id:</span> {{ $selected->user_id ?: '-' }}</div>
                <div><span class="text-gray-500">Table / record:</span> {{ $selected->table_name ?: '-' }} {{ $selected->record_id ? '#'.$selected->record_id : '' }}</div>
                <div><span class="text-gray-500">Done from:</span> {{ $selected->operation ?: '-' }}</div>
                <div><span class="text-gray-500">IP:</span> {{ $selected->ip ?: '-' }}</div>
                <div class="md:col-span-2 truncate"><span class="text-gray-500">Page:</span> {{ $selected->http_method }} {{ $selected->url }}</div>
                <div class="md:col-span-2 truncate"><span class="text-gray-500">Browser:</span> {{ $selected->user_agent ?: '-' }}</div>
                @if($selected->notes)
                    <div class="md:col-span-2"><span class="text-gray-500">Note:</span> {{ $selected->notes }}</div>
                @endif
            </div>

            @if(count($columns))
                <div class="overflow-x-auto">
                    <table class="table table-sm table-zebra">
                        <thead>
                            <tr>
                                <th>Field</th>
                                @if($selected->old_values !== null)<th>Before</th>@endif
                                @if($selected->new_values !== null)<th>{{ $selected->old_values !== null ? 'After' : 'Value' }}</th>@endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($columns as $column)
                                <tr>
                                    <td class="font-mono text-xs">{{ $column }}</td>
                                    @if($selected->old_values !== null)<td class="break-all {{ $selected->new_values !== null ? 'text-error' : '' }}">{{ $show($old[$column] ?? null) }}</td>@endif
                                    @if($selected->new_values !== null)<td class="break-all {{ $selected->old_values !== null ? 'text-success' : '' }}">{{ $show($new[$column] ?? null) }}</td>@endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @endif
        <x-slot:actions>
            <x-button label="Close" @click="$wire.modal = false" />
        </x-slot:actions>
    </x-modal>
</div>
