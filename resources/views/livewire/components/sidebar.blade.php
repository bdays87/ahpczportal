<div>
    <x-slot:sidebar drawer="main-drawer" collapsible class="bg-base-200 text-gray-500 border border-r-gray-200">
       <div class="flex justify-center bg-white items-center">
        <img src="{{ asset('logo/logo.jpg') }}" alt="Logo" class="lg:w-30 lg:h-30 w-20 h-20">
       
       </div>
     
    
    {{-- MENU --}}
    <x-menu activate-by-route active-class="bg-blue-300">
    
        <x-menu-separator />
        <x-menu-item title="Dashboard" icon="o-home" link="{{ route('dashboard') }}"  />
        <x-menu-separator />
     
        @forelse ($menulist as $module)
                @if(in_array($module->default_permission, $permissions))
                <x-menu-sub title="{{ $module->name }}" icon="{{ $module->icon }}" class="text-gray-500" >
                    @forelse ($module->submodules as $submodule)
                        @if(in_array($submodule->default_permission, $permissions))
                            <x-menu-item title="{{ $submodule->name }}" icon="{{ $submodule->icon }}" link="{{route($submodule->url)}}" class="text-gray-500" />
                        @endif
                    @empty
                    @endforelse
                </x-menu-sub>
                <x-menu-separator />
                @endif
             
              
    
        @empty
            <x-menu-item title="No Modules" class="text-red-500" />
        @endforelse

    
    
    </x-menu>
    </x-slot:sidebar>
    </div>
    