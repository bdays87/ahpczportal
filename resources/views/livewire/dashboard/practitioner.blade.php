<div>
   <livewire:components.header/>
   <livewire:components.checkcustomer/>

   @if(auth()->user()->customer)
   <x-card  separator class="border-2  border-gray-200 mt-5">
   <x-tabs wire:model="selectedTab" active-class="bg-primary rounded pt-4 pb-2 !text-white" label-class="font-semibold" label-div-class="bg-primary/5 rounded pl-2 pt-2 pb-2">
       <x-tab name="profession-tab" label="Professions" icon="o-academic-cap">
          <livewire:myprofessions/>
      </x-tab>
      <x-tab name="contact-tab" label="Contact details" icon="o-users">
         <livewire:admin.components.contactdetails :customer="auth()->user()->customer->customer" />
      </x-tab>
      <x-tab name="employment-tab" label="Employment details" icon="o-sparkles">
          <livewire:admin.components.employmentdetails :customer="auth()->user()->customer->customer" />
      </x-tab>
      <x-tab name="musics-tab" label="My CDPs" icon="o-musical-note">
         <livewire:admin.components.mycdps />
                 
      </x-tab>
  </x-tabs>
  </x-card>
 
     
   @endif
</div>
