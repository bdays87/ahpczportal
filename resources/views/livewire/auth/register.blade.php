<div>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <div class="flex min-h-screen">
        <div class="flex flex-col justify-center flex-1 px-4 sm:px-6 lg:px-8">
          
            <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-3xl ">
                <div class="bg-white border border-gray-200 py-8 px-4 shadow sm:rounded-lg sm:px-10">
                    <div class="text-center mb-4">
                        <h2 class="mt-6 text-3xl font-bold tracking-tight text-gray-900">Sign Up</h2>
                        <p class="mt-2 text-sm text-gray-600">Sign up for an account</p>
                    </div>
    <x-form wire:submit="register">
        <div class="grid md:grid-cols-2 lg:grid-cols-2 gap-2">
        <x-input label="Name" wire:model.blur="name" />
        <x-input label="Surname" wire:model.blur="surname" />  
         <x-input label="Email" wire:model.blur="email" type="email" />
        <x-input label="Phone" wire:model.blur="phone" type="tel" placeholder="0771234567 or +263771234567" />
              </div>
        @error('email')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
        @error('phone')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
           
         
        <div class="grid grid-cols-2 gap-2">
          
        <div x-data="{ show: false }" x-id="['password-input']">
            <label :for="$id('password-input')" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <div class="relative">
                <input 
                    :id="$id('password-input')"
                    :type="show ? 'text' : 'password'"
                    wire:model="password"
                    class="input input-bordered w-full pr-10 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                    placeholder="Enter password"
                />
                <button 
                    type="button" 
                    @click="show = !show"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none transition-colors"
                    tabindex="-1"
                    aria-label="Toggle password visibility">
                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.906 5.69m0 0L21 21"></path>
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div x-data="{ show: false }" x-id="['password-confirmation-input']">
            <label :for="$id('password-confirmation-input')" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
            <div class="relative">
                <input 
                    :id="$id('password-confirmation-input')"
                    :type="show ? 'text' : 'password'"
                    wire:model="password_confirmation"
                    class="input input-bordered w-full pr-10 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                    placeholder="Confirm password"
                />
                <button 
                    type="button" 
                    @click="show = !show"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none transition-colors"
                    tabindex="-1"
                    aria-label="Toggle password confirmation visibility">
                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.906 5.69m0 0L21 21"></path>
                    </svg>
                </button>
            </div>
            @error('password_confirmation')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        </div>
        <div class="grid  gap-2">
            <x-select label="Account Type" wire:model="accounttype_id" :options="$accounttypes" option-label="name" option-value="id" placeholder="Select Account Type" />
 
            
        </div>
       
        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.modal = false" />
            <x-button label="Submit" type="submit" class="btn-primary" spinner="register" />
        </x-slot:actions>
    </x-form>
</div>
</div>
</div>
</div>
</div>
 