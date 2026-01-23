<div>
    <div class="flex min-h-screen">
        <div class="flex flex-col justify-center flex-1 px-4 sm:px-6 lg:px-8">
            <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
                <div class="bg-white border border-gray-200 py-8 px-4 shadow sm:rounded-lg sm:px-10">
                    <div class="text-center mb-4">
                        <h2 class="mt-6 text-3xl font-bold tracking-tight text-gray-900">Forgot Password</h2>
                        <p class="mt-2 text-sm text-gray-600">Enter your email address and we'll send you a link to reset your password.</p>
                    </div>
                    
                    @if ($status === 'success')
                        <div class="alert alert-success mb-4">
                            {{ $message }}
                        </div>
                    @elseif ($status === 'error')
                        <div class="alert alert-error mb-4">
                            {{ $message }}
                        </div>
                    @endif

                    <x-form class="space-y-3" wire:submit.prevent="sendResetLink">
                        <div class="grid gap-4">
                            <x-input 
                                id="email" 
                                placeholder="Email address" 
                                name="email" 
                                type="email" 
                                wire:model="email" 
                                autocomplete="email"
                            />
                        </div>

                        <div>
                            <x-button label="Send Reset Link" type="submit" class="w-full btn-primary" spinner="sendResetLink"/>
                        </div>
                    </x-form>
                    
                    <div class="mt-4 text-center">
                        <x-button label="Back to Login" type="button" class="w-full btn-link" link="{{ route('login') }}"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
