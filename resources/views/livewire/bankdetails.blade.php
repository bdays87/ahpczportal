<section id="banking-details" class="py-16 bg-gray-50">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Banking Details</h2>
            <p class="text-gray-600 mt-4 max-w-2xl mx-auto">Use the following bank accounts for payments and registration fees</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse ($banks as $bank)
            <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-lg transition duration-300 border border-gray-200">
                <div class="flex items-center gap-3 mb-4">
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">{{ $bank->bank->name }}</h3>
                </div>
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-gray-500 font-medium">Account Name:</span>
                        <p class="text-gray-800 font-semibold mt-1">{{ $bank->bank->account_name }}</p>
                    </div>
                    <div class="flex justify-between items-center gap-2">
                        <span class="text-gray-500 font-medium">Branch Name:</span>
                        <p class="text-gray-800 font-semibold mt-1">{{ $bank->branch_name }}</p>
                    </div>
                    <div class="flex justify-between items-center gap-2">
                        <span class="text-gray-500 font-medium">Swift Code:</span>
                        <p class="text-gray-800 font-semibold mt-1">{{ $bank->swift_code ?? 'N/A' }}</p>
                    </div>
                    <div class="flex justify-between items-center gap-2">
                        <span class="text-gray-500 font-medium">Branch Code:</span>
                        <p class="text-gray-800 font-semibold mt-1">{{ $bank->branch_code ?? 'N/A' }}</p>
                    </div>
                    <div class="flex justify-between items-center gap-2">
                        <span class="text-gray-500 font-medium">Currency:</span>
                        <p class="text-gray-800 font-semibold mt-1 text-lg">{{ $bank->currency->name }}</p>
                    </div>
                    <div class="flex justify-between items-center gap-2">                            
                            <span class="text-gray-500 font-medium">Account Number:</span>
                            <p class="text-gray-800 font-semibold mt-1 text-lg">{{ $bank->account_number }}</p>
                     
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center">
                <p class="text-gray-500 font-medium">No banking details found</p>
            </div>
            @endforelse
            
           
        
            
          
          
        </div>
    </div>
</section>
