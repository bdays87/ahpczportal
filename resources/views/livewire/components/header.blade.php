<div>
    <div class="bg-white border rounded-lg mt-3">
        <div class="container mx-auto px-4 py-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
                    <p class="mt-1 text-sm text-gray-500">Welcome back, {{ auth()->user()->name }} {{ auth()->user()->surname }} ({{ auth()->user()->accounttype->name }})</p>
                </div>
          
                    @if(auth()->user()->customer)
                             <livewire:admin.components.walletbalances :customer="auth()->user()->customer->customer"/>
                    @endif
                
                
            </div>
        </div>
    </div>
</div>
