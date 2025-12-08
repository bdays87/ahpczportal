<div>
    <x-slot:sidebar drawer="main-drawer" collapsible class="bg-base-200 text-gray-500 border border-r-gray-200">
        {{-- Logo Section --}}
        <div class="flex justify-center items-center bg-white backdrop-blur-sm py-6 border-b border-white ">
            <img src="{{ asset('logo/logo.jpg') }}" alt="Logo" class="lg:w-28 lg:h-28 w-20 h-20 rounded-lg  ring-4 ring-white/30 transition-transform hover:scale-105 duration-300">
        </div>

        {{-- Welcome Section --}}
        <div class="px-4 py-4 border-b border-white/10">
            <p class="text-xs text-indigo-200 uppercase tracking-wider font-semibold mb-1">Welcome back</p>
            <p class="text-sm font-bold  truncate">{{ auth()->user()->name ?? 'Practitioner' }} {{ auth()->user()->surname ?? 'Practitioner' }}</p>
        </div>

        {{-- MENU --}}
        <x-menu activate-by-route active-class="bg-green-300 font-semibold shadow-lg border-l-4 " class="px-2 py-4">
            
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
        </x-menu>

        {{-- Footer Section --}}
        <div class="absolute bottom-0 left-0 right-0 p-4 bg-black/20 backdrop-blur-sm border-t border-white/10">
            <div class="flex items-center gap-2 text-xs text-indigo-200">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                </svg>
                <span>Portal v1.0</span>
            </div>
        </div>
    </x-slot:sidebar>
</div>
    