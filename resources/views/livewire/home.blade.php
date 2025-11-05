<div>
    @if(auth()->user()->accounttype_id == 1)
    <livewire:dashboard.administrator/>
    @elseif(auth()->user()->accounttype_id == 2)
    <livewire:dashboard.practitioner/>
    @elseif(auth()->user()->accounttype_id == 3)
    <livewire:dashboard.student/>
    @endif
</div>
