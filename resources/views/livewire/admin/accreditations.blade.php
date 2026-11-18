<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />

    <x-card title="Accreditations" subtitle="Accreditations an institution can hold (e.g. National Certification Programme, SADCAS) and the levels of each." separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            <x-input placeholder="Search" wire:model.live.debounce.300ms="search" icon="o-magnifying-glass" class="input-sm" />
            <x-button label="New accreditation" icon="o-plus" class="btn-sm btn-primary" wire:click="openmodal" />
        </x-slot:menu>

        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>Accreditation</th>
                        <th>Levels</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($accreditations as $a)
                        <tr wire:key="acc-{{ $a->id }}">
                            <td>
                                <div class="font-semibold">{{ $a->name }}</div>
                                @if($a->description)<div class="text-xs text-gray-400">{{ $a->description }}</div>@endif
                            </td>
                            <td>
                                @forelse($a->levels as $level)
                                    <x-badge value="{{ $level->name }}" class="badge-info badge-soft mr-1" />
                                @empty
                                    <span class="text-xs text-gray-400">No levels yet</span>
                                @endforelse
                            </td>
                            <td class="whitespace-nowrap">
                                <x-button label="Levels" icon="o-list-bullet" class="btn-xs btn-outline" wire:click="openlevelmodal({{ $a->id }})" />
                                <x-button icon="o-pencil" class="btn-xs btn-outline" wire:click="openmodal({{ $a->id }})" />
                                <x-button icon="o-trash" class="btn-xs btn-outline btn-error" wire:click="delete({{ $a->id }})"
                                    wire:confirm="Delete this accreditation and its levels?" spinner />
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-gray-400">No accreditations yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <x-modal wire:model="modal" title="{{ $accreditation_id ? 'Edit accreditation' : 'New accreditation' }}" separator>
        <div class="space-y-3">
            <x-input label="Name" wire:model="name" placeholder="e.g. SADCAS" />
            <x-input label="Description (optional)" wire:model="description" />
        </div>
        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.modal = false" />
            <x-button label="Save" class="btn-primary" wire:click="save" spinner="save" />
        </x-slot:actions>
    </x-modal>

    <x-modal wire:model="levelmodal" title="Levels" subtitle="{{ $level_accreditation_name }}" separator>
        <div class="flex items-end gap-2">
            <x-input label="New level" wire:model="levelname" placeholder="e.g. Level 2" class="flex-1" wire:keydown.enter="savelevel" />
            <x-button label="Add" icon="o-plus" class="btn-primary" wire:click="savelevel" spinner="savelevel" />
        </div>
        <div class="mt-4 border border-gray-200 rounded-lg divide-y">
            @forelse($levelsfor as $level)
                <div class="flex items-center justify-between px-3 py-2" wire:key="lvl-{{ $level->id }}">
                    <span>{{ $level->name }}</span>
                    <x-button icon="o-trash" class="btn-xs btn-ghost text-error" wire:click="deletelevel({{ $level->id }})"
                        wire:confirm="Delete this level?" />
                </div>
            @empty
                <div class="px-3 py-2 text-sm text-gray-400">No levels yet.</div>
            @endforelse
        </div>
        <x-slot:actions>
            <x-button label="Close" @click="$wire.levelmodal = false" />
        </x-slot:actions>
    </x-modal>
</div>
