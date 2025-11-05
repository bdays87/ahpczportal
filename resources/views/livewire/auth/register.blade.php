<div>
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
        <x-input label="Name" wire:model="name" />
        <x-input label="Surname" wire:model="surname" />  
         <x-input label="Email" wire:model="email" />
        <x-input label="Phone" wire:model="phone" />
              </div>
        <div class="grid md:grid-cols-3 lg:grid-cols-3 gap-2">
            <x-select label="Account Type" wire:model="accounttype_id" :options="$accounttypes" option-label="name" option-value="id" placeholder="Select Account Type" />
 
        <x-input label="Password" wire:model="password" type="password" />
        <x-input label="Confirm Password" wire:model="password_confirmation" type="password" />
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
 