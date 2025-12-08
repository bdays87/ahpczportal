<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="lofi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' - '.config('app.name') : config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {!! PwaKit::head() !!}
</head>
<body class="min-h-screen font-sans antialiased ">
    <x-nav sticky full-width>
 
        <x-slot:brand>
            <div class="flex items-center gap-2">
                <img src="./logo/logo.jpg" alt="Logo" class="h-8 sm:h-10 md:h-12 lg:h-16 w-auto">
                {{-- Brand --}}
                <div class="hidden sm:block text-xs sm:text-sm md:text-base lg:text-lg font-medium">
                    <div class="hidden lg:block">{{ config('app.name') }}</div>
                    <small class="text-xs sm:text-sm">{{ config('app.title') }}</small>
                </div>
            </div>
        </x-slot:brand>

        {{-- Right side actions --}}
        <x-slot:actions>
            {{-- Mobile menu dropdown - only visible on mobile (phones) --}}
            <x-dropdown class="md:hidden">
                <x-slot:trigger>
                    <x-button icon="o-bars-3" class="btn-ghost btn-sm" />
                </x-slot:trigger>
                <x-menu class="w-48">
                    <x-menu-item title="Home" icon="o-home" link="{{ route('welcome') }}" />
                    <x-menu-item title="Practitioners" icon="o-user-group" />
                    <x-menu-item title="Verify Certificate" icon="o-bookmark-square" link="{{ route('certificateverification.index') }}" />
                    <x-button label="Institutions" link="{{ route('registeredinstitutions.index') }}" class="btn-ghost btn-sm" />
                   
                    <x-menu-separator />
                    <x-menu-item title="Login" icon="o-arrow-right-on-rectangle" link="{{ route('login') }}" />
                    <x-menu-item title="Register" icon="o-user-plus" link="{{ route('register') }}" />
                </x-menu>
            </x-dropdown>
            
            {{-- Desktop navigation buttons - visible on tablet and larger screens --}}
            <div class="hidden md:flex items-center gap-2">
                <x-button label="Home" link="{{ route('welcome') }}" class="btn-ghost btn-sm" />
                <x-button label="Practitioners" link="{{ route('practitionerlist.index') }}" class="btn-ghost btn-sm" />
                <x-button label="Verify Certificates" link="{{ route('certificateverification.index') }}" class="btn-ghost btn-sm" />
                <x-button label="Institutions" link="{{ route('registeredinstitutions.index') }}" class="btn-ghost btn-sm" />
                <x-button label="Login" link="{{ route('login') }}" class="btn-ghost btn-sm" />
                <x-button label="Register" link="{{ route('register') }}" class="btn-ghost btn-sm" />
            </div>
        </x-slot:actions>
    </x-nav>

    
    {{-- MAIN --}}
    <main class="w-full">
        {{-- The `$slot` goes here --}}
        {{ $slot }}
    </main>

    {{--  TOAST area --}}
    <x-toast />
    {!! PwaKit::scripts() !!}
</body>
</html>
