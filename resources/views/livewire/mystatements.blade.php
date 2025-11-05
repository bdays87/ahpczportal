<div>
    <x-breadcrumbs :items="$breadcrumbs"    class="bg-base-300 p-3 rounded-box mt-2" />
    <livewire:admin.components.customerstatements :customer="auth()->user()->customer->customer" />
</div>
