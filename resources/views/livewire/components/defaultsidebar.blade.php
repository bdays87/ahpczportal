<div>
    <x-slot:sidebar drawer="main-drawer" collapsible class="{{ config('app.color') }} text-gray-500 border border-r-gray-200">
        {{-- Logo Section --}}
        <div class="flex justify-center items-center bg-white backdrop-blur-sm py-6 border-b border-white ">
            <img src="{{ asset(config('app.logo')) }}" alt="Logo" class="lg:w-28 lg:h-28 w-20 h-20 rounded-lg  ring-4 ring-white/30 transition-transform hover:scale-105 duration-300">
        </div>

        {{-- Welcome Section --}}
        <div class="px-4 py-4 border-b border-white/10">
            <p class="text-xs text-green-300 uppercase tracking-wider font-semibold mb-1">Welcome back</p>
            <p class="text-sm font-bold  truncate">{{ auth()->user()->name ?? 'Practitioner' }} {{ auth()->user()->surname ?? 'Practitioner' }}</p>
        </div>

        {{-- MENU --}}
        <x-menu activate-by-route active-class="bg-base-200 font-semibold shadow-lg border-l-4 " class="px-2 py-4">
            
            <x-menu-item 
                title="Dashboard" 
                icon="o-home" 
                link="{{ route('dashboard') }}" 
                class=" rounded-lg transition-all duration-200 mb-2  hover:text-green-300 hover:translate-x-1"
            />
            <x-menu-separator />
            
            <x-menu-item 
                title="Statements" 
                icon="o-document-currency-dollar" 
                link="{{ route('mystatements.index') }}" 
                class="hover:bg-white/10 rounded-lg transition-all duration-200 mb-2  hover:text-green-300 hover:translate-x-1"
            />
            <x-menu-separator />
            <x-menu-item 
                title="Online Payments" 
                icon="o-banknotes" 
                link="{{ route('myonlinepayments.index') }}" 
                class="hover:bg-white rounded-lg transition-all duration-200 mb-2  hover:text-green-300 hover:translate-x-1"
            />
            <x-menu-separator />
            
            <x-menu-item 
                title="Manual Payments" 
                icon="o-banknotes" 
                link="{{ route('mymanualpayments.index') }}" 
                class="hover:bg-white rounded-lg transition-all duration-200 mb-2  hover:text-green-300 hover:translate-x-1"
            />
            <x-menu-separator />
            <x-menu-item 
                title="My Activities" 
                icon="o-user" 
                link="{{ route('customer.activities') }}" 
                class="hover:bg-white rounded-lg transition-all duration-200 mb-2  hover:text-green-300 hover:translate-x-1"
            />
            <x-menu-separator />
            <x-menu-item 
                title="Elections & Voting" 
                icon="o-hand-raised" 
                link="{{ route('voting.elections') }}" 
                class="hover:bg-white rounded-lg transition-all duration-200 mb-2  hover:text-green-300 hover:translate-x-1"
            />
            <x-menu-separator />
            <x-menu-item 
            title="Journals" 
            icon="o-book-open" 
            link="{{ route('customer.journals') }}" 
            class="hover:bg-white rounded-lg transition-all duration-200 mb-2  hover:text-green-300 hover:translate-x-1"
        />
        <x-menu-separator />
        <x-menu-item 
        title="Newsletters" 
        icon="o-newspaper" 
        link="{{ route('customer.newsletters') }}" 
        class="hover:bg-white rounded-lg transition-all duration-200 mb-2  hover:text-green-300 hover:translate-x-1"
    />
    <x-menu-separator />
    <x-menu-item 
        title="Resources" 
        icon="o-folder-open" 
        link="{{ route('customer.resources.index') }}" 
        class="hover:bg-white rounded-lg transition-all duration-200 mb-2  hover:text-green-300 hover:translate-x-1"
    />
    <x-menu-separator />
        </x-menu>

     
    </x-slot:sidebar>
</div>
    