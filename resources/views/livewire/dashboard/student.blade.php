<div>
      <livewire:components.header/>
      <livewire:components.checkcustomer/>
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mt-5">
          <div class="lg:col-span-2">
              <livewire:myprofessions/>
              @if(auth()->user()->customer)
              <livewire:admin.components.contactdetails :customer="auth()->user()->customer->customer" />
              @endif
          </div>
          <div class="space-y-5">
              <livewire:components.latestjournals />
              <livewire:components.latestnewsletters />
          </div>
      </div>
</div>
