<div>
   
   
    <div class="flex min-h-screen">
        <div class="flex flex-col justify-center flex-1 px-4 sm:px-6 lg:px-8">
          
            <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
                <div class="bg-white border border-gray-200 py-8 px-4 shadow sm:rounded-lg sm:px-10">
                    <div class="text-center mb-4">
                        <h2 class="mt-6 text-3xl font-bold tracking-tight text-gray-900">Login</h2>
                        <p class="mt-2 text-sm text-gray-600">Sign in to your account</p>
                    </div>
                    <x-form class="space-y-3" action="#" wire:submit.prevent="login">
                        @if ($error)
                            <div class="alert alert-error mb-4">
                                {{ $error }}
                            </div>
                        @endif
                                   <div class="grid gap-4">
                                <x-input id="email" placeholder="Email address" name="email" type="email" wire:model="email" autocomplete="email"/>
                                 <x-input id="password" placeholder="Password" name="password" type="password"  wire:model="password" />
                                   </div>
                                   <div>
                                    <x-checkbox wire:model="remember" id="remember" name="remember" label="Remember me"/>
                                   </div>
                        
                        <div>
                            <x-button label="Login" type="submit" class="w-full btn-primary" spinner="login"/>
                        </div>
                    </x-form>
                        <x-button label="Register" type="button" class="w-full btn-link" link="{{ route('register') }}"/>
                        <x-button label="Forgot Password" type="button" class="w-full btn-link" link="{{ route('forget') }}"/>
                   
                </div>
            </div>
        </div>
    </div>
</div>
