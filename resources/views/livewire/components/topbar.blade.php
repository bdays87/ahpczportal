<div>
    <x-nav sticky class="rounded-md border  bg-base-200 text-gray-500 ">
        <x-slot:brand>
            @if(auth()->user()->accounttype_id == 1)
            <livewire:admin.components.searchcustomer />
            @else
            <div>My Portal</div>
            @endif
        </x-slot:brand>
        <x-slot:actions>
            {{-- Mobile drawer toggle --}}
            <label for="main-drawer" class="lg:hidden me-2">
                <x-icon name="o-bars-3" class="cursor-pointer text-lg" />
            </label>
            
            {{-- Notifications - hidden on mobile to save space --}}
       <livewire:components.notifications/>
         
            
            {{-- User info - responsive display --}}
            <div class="hidden gap-2 items-center lg:flex">
                <div class="text-right">
                    <div class="text-sm font-medium text-gray-500">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-gray-500">{{ auth()->user()->email }}</div>
                </div>
                <x-avatar src="{{ auth()->user()->avatar ?? '' }}" class="w-8 h-8" />
            </div>
            
            {{-- User dropdown menu --}}
            <x-dropdown>
                <x-slot:trigger>
                    <x-button class="btn-ghost btn-sm" icon="o-chevron-down" />
                </x-slot:trigger>
                {{-- Mobile user info --}}
                <div class="lg:hidden px-4 py-2 border-b border-gray-200">
                    <div class="text-sm font-medium text-gray-500">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-gray-500">{{ auth()->user()->email }}</div>
                </div>
                <x-menu-item title="Profile Settings" class="text-gray-500" icon="o-cog-6-tooth" link="{{ auth()->user()->accounttype_id == 1 ? route('admin.profile-settings') : route('customer.profile-settings') }}" />
                <x-menu-item 
                    title="Sign Out" 
                    icon="o-arrow-right-on-rectangle" 
                    wire:click="logout"
                    class="text-red-500 hover:text-red-600" />
            </x-dropdown>

        </x-slot:actions>
        
    </x-nav>   
</div>
