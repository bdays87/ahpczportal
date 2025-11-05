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
                <img src="./logo/logo.jpg" alt="Logo" class="h-8 sm:h-12 md:h-16 lg:h-20 w-auto">
                {{-- Brand --}}
                <div class="hidden sm:block xs:block text-sm md:text-base lg:text-lg font-medium">
                    <div class="hidden lg:block">{{ config('app.name') }}</div>
                    <div class="lg:hidden">{{ config('app.name') }}</div>
                </div>
            </div>
        </x-slot:brand>
 
        {{-- Right side actions --}}
        <x-slot:actions>
            {{-- Mobile menu dropdown --}}
            <x-dropdown class="lg:hidden">
                <x-slot:trigger>
                    <x-button icon="o-bars-3" class="btn-ghost btn-sm" />
                </x-slot:trigger>
                <x-menu-item title="Home" link="{{ route('welcome') }}" icon="o-home" />
                <x-menu-item title="Login" link="{{ route('login') }}" icon="o-arrow-right-on-rectangle" />
                <x-menu-item title="Register" link="{{ route('register') }}" icon="o-user-plus" />
                <x-menu-item title="Forget Password" link="{{ route('forget') }}" icon="o-key" />
            </x-dropdown>
            
            {{-- Desktop navigation buttons --}}
            <div class="hidden lg:flex items-center gap-2">
                <x-button label="Home" link="{{ route('welcome') }}" class="btn-ghost btn-sm" />
                <x-button label="Login" link="{{ route('login') }}" class="btn-ghost btn-sm" />
                <x-button label="Register" link="{{ route('register') }}" class="btn-ghost btn-sm" />
            </div>
        </x-slot:actions>
    </x-nav>

    
    {{-- MAIN --}}
    <x-main>
       
       
        {{-- The `$slot` goes here --}}
        <x-slot:content>
            {{ $slot }}
        </x-slot:content>
    </x-main>

    {{--  TOAST area --}}
    <x-toast />
    {!! PwaKit::scripts() !!}
</body>
</html>
