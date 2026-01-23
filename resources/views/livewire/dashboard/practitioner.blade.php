<div>
   <livewire:components.header/>
   <livewire:components.checkcustomer/>

   @if(auth()->user()->customer)
   <div class="grid grid-cols-1 gap-5 mt-5">
       
           <x-card  separator class="border-2  border-gray-200">
           <x-tabs wire:model="selectedTab" active-class="bg-primary rounded pt-4 pb-2 !text-white" label-class="font-semibold" label-div-class="bg-primary/5 rounded pl-2 pt-2 pb-2">
               <x-tab name="profession-tab" label="Professions" icon="o-academic-cap">
                  <livewire:myprofessions/>
                  <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mt-5">
                    <livewire:components.latestjournals />
                    <livewire:components.latestnewsletters />
                    <livewire:components.latestresources />
                </div>
              </x-tab>
              <x-tab name="contact-tab" label="Next of Kin" icon="o-users">
                 <livewire:admin.components.contactdetails :customer="auth()->user()->customer->customer" />
              </x-tab>
              <x-tab name="employment-tab" label="Employment details" icon="o-sparkles">
                  <livewire:admin.components.employmentdetails :customer="auth()->user()->customer->customer" />
              </x-tab>
          </x-tabs>
          </x-card>
      
     
   </div>
  
   @else
     
   @endif
</div>
