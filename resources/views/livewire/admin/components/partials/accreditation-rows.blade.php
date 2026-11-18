{{-- Accreditation + level rows. $prop = Livewire property name, $rows = its value, $remove = remove method --}}
@forelse($rows as $ai => $row)
    @php
        $selected = $accreditationlist->firstWhere('id', (int) ($row['accreditation_id'] ?? 0));
        $levels = $selected ? $selected->levels : collect();
    @endphp
    <div class="flex items-center gap-2 mb-2" wire:key="{{ $prop }}-{{ $ai }}">
        <select wire:model.live="{{ $prop }}.{{ $ai }}.accreditation_id" class="select select-bordered select-sm flex-1">
            <option value="">Select accreditation</option>
            @foreach($accreditationlist as $a)
                <option value="{{ $a->id }}">{{ $a->name }}</option>
            @endforeach
        </select>
        <select wire:model="{{ $prop }}.{{ $ai }}.accreditationlevel_id" class="select select-bordered select-sm w-44" @disabled($levels->isEmpty())>
            <option value="">{{ $levels->isEmpty() ? 'No levels' : 'Select level' }}</option>
            @foreach($levels as $level)
                <option value="{{ $level->id }}">{{ $level->name }}</option>
            @endforeach
        </select>
        <x-button icon="o-trash" class="btn-xs btn-ghost text-error" wire:click="{{ $remove }}({{ $ai }})" />
    </div>
@empty
    <p class="text-xs text-gray-400">No accreditations added.</p>
@endforelse
