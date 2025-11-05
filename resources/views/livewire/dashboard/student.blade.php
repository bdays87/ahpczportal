<div>
      <livewire:components.header/>
      <livewire:components.checkcustomer/>
      <livewire:myprofessions/>
      @if(auth()->user()->customer)
      <livewire:admin.components.contactdetails :customer="auth()->user()->customer->customer" />
      @endif
</div>
