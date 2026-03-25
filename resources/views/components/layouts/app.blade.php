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
<body class="min-h-screen font-sans antialiased bg-white">
   {{-- MAIN --}}
   <x-main>
   @php
       $hasPendingApproval = \App\Models\Customerhistoricaldata::where('user_id', auth()->id())
           ->where('status', 'PENDING')
           ->exists();
   @endphp
   @if(!$hasPendingApproval)
       @if(auth()->user()->accounttype_id == 1)
        <x-slot:sidebar drawer="main-drawer" collapsible class="bg-base-200 text-gray-500 border border-r-gray-200">
            <livewire:components.sidebar />
        </x-slot:sidebar>
        @else
        <x-slot:sidebar drawer="main-drawer" collapsible class="{{ config('app.color') }} text-gray-500 border border-r-gray-200">
            <livewire:components.defaultsidebar />
        </x-slot:sidebar>
        @endif
   @endif
    <x-slot:content>
        <livewire:components.topbar />
        {{ $slot }}
    </x-slot:content>
</x-main>

{{--  TOAST area --}}
<x-toast />
{!! PwaKit::scripts() !!}

</body>
</html>
