@props([
    'customer' => null,
    'previous' => null,
    'label' => 'Back to Profile',
])

@php
    // Send admins back to the specific customer's profile so they never have
    // to search the name again; practitioners go back to their professions list.
    $profileLink = null;
    if ($customer) {
        $profileLink = auth()->user()?->accounttype_id == 1
            ? route('customers.show', $customer->uuid)
            : route('professions.index');
    }
@endphp

<div class="flex flex-wrap items-center justify-between gap-2 mt-3">
    <div class="flex items-center gap-2">
        @if($previous)
            <x-button label="Previous" icon="o-arrow-left" link="{{ $previous }}" class="btn-sm btn-outline" />
        @endif
        @if($profileLink)
            <x-button :label="$label" icon="o-arrow-uturn-left" link="{{ $profileLink }}" class="btn-sm btn-ghost" />
        @endif
    </div>

    @if($customer)
        <div class="text-sm text-gray-600">
            <span class="font-semibold">{{ trim(($customer->name ?? '').' '.($customer->surname ?? '')) }}</span>
            @if($customer->regnumber)
                <span class="text-gray-400">&bull; {{ $customer->regnumber }}</span>
            @endif
        </div>
    @endif
</div>
