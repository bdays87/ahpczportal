<div>
   
    @if($loader)
    <div class="flex justify-center items-center h-screen">
        <div class="animate-spin rounded-full h-32 w-32 border-b-2 border-gray-900"></div>
    </div>
    @endif
    @if($successmessage)
    <div class="flex min-h-screen">
        <div class="flex flex-col justify-center flex-1 px-4 sm:px-6 lg:px-8">
            <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-green-200 border border-gray-200 py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <div class="text-center mb-4">
                    <h2 class="mt-6 text-3xl font-bold tracking-tight text-green-900">Success</h2>
                    <p class="mt-2 text-sm text-green-600">{{ $successmessage }}</p>
                </div>
                <div class="mt-6">
                    <x-button label="Go to Dashboard" link="{{ route('dashboard') }}" class="w-full btn-primary" />
                </div>
            </div>
            </div>
        </div>
    </div>
    @endif
    @if($errormessage)
    <div class="flex min-h-screen">
        <div class="flex flex-col justify-center flex-1 px-4 sm:px-6 lg:px-8">
            <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-red-50 border border-gray-200 py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <div class="text-center mb-4">
                    <h2 class="mt-6 text-3xl font-bold tracking-tight text-red-900">Error</h2>
                    <p class="mt-2 text-sm text-red-600">{{ $errormessage }}</p>
                </div>
                <div class="mt-6">
                    <x-button label="Go to Dashboard" link="{{ route('dashboard') }}" class="w-full btn-primary" />
                </div>
            </div>
            </div>
        </div>
    </div>
    @endif
</div>
