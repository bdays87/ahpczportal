<div>
    <x-breadcrumbs :items="$breadcrumbs"    class="bg-base-300 p-3 rounded-box mt-2" />
    @can('configurations.access')

    <x-card title="Configurations" separator class="mt-5 border-2 border-gray-200">
    <div class="grid lg:grid-cols-3 gap-2 mt-5">

       <livewire:admin.components.customertypes />
       <livewire:admin.components.documents />
       <livewire:admin.components.employmentlocations />
       <livewire:admin.components.employmentstatuslist />
       <livewire:admin.components.qualificationcategories />
       <livewire:admin.components.qualificationlevels />
       <livewire:admin.components.registertypes />
       <livewire:admin.components.nationalities />
       <livewire:admin.components.provinces />
       <livewire:admin.components.cities />
       <livewire:admin.components.certificatetypes />
       <livewire:admin.components.applicanttypes />

     
      
    </div>
    </x-card>        
    @else
    <x-alert class="alert-error mt-3" title="You do not have permission to view configurations." />
    @endcan
</div>
