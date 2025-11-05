<div>
    <x-breadcrumbs :items="$breadcrumbs"    class="bg-base-300 p-3 rounded-box mt-2" />
    @can('customers.access')
    <x-card title="{{ $customerprofile->name }} {{ $customerprofile->surname }}" subtitle="{{ $customerprofile->email }} || {{ $customerprofile->phone }}" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
           
        </x-slot:menu>
        <livewire:admin.components.walletbalances :customer="$customerprofile" />
        <x-tabs
    wire:model="tabSelected"
    class="tabs-lift"
    active-class="bg-primary rounded pt-4 pb-2 !text-white"
    label-class="font-semibold"
    label-div-class="bg-info rounded  w-full pt-2 pb-2" 
>
    <x-tab name="profile-tab" icon="o-user" label="Personal">
        <livewire:admin.components.personaldetails :customer="$customerprofile" />
    </x-tab>
    <x-tab name="employment-tab" icon="o-briefcase" label="Employment">
        <livewire:admin.components.employmentdetails :customer="$customerprofile" />
    </x-tab>
    <x-tab name="contact-tab" icon="o-phone" label="Contacts">
        <livewire:admin.components.contactdetails :customer="$customerprofile" />
    </x-tab>
    <x-tab name="statement-tab" icon="o-document" label="Statements">
        <livewire:admin.components.customerstatements :customer="$customerprofile" />
    </x-tab>
    <x-tab name="onlinepayment-tab" icon="o-credit-card" label="Paynow ">
        <livewire:admin.components.customeronlinepayments :customer="$customerprofile" />
    </x-tab>
    <x-tab name="manualpayment-tab" icon="o-banknotes" label="Payments">
        <livewire:admin.components.manualpayments :customer="$customerprofile" />
    </x-tab>
</x-tabs>

      
    </x-card>
  
  @else
    <x-alert class="alert-error mt-3" title="You do not have permission to view customers." />
    @endcan
</div>
