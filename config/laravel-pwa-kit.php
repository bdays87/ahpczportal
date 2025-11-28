<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enable PWA
    |--------------------------------------------------------------------------
    | Globally enable or disable Progressive Web App functionality.
    */
    'enable_pwa' => true,

    /*
    |--------------------------------------------------------------------------
    | Show Install Toast on First Load
    |--------------------------------------------------------------------------
    |
    | Determines whether the PWA install toast should be displayed when a user
    | first visits the site. Once the toast is shown or dismissed, it will not
    | reappear for that user on the same day, preventing repeated interruptions
    | and improving user experience.
    |
    | Type: `bool`
    | Default: true
    |
    */
    'install-toast-show' => true,


    /*
    |--------------------------------------------------------------------------
    | PWA Manifest Configuration
    |--------------------------------------------------------------------------
    | Defines metadata for your Progressive Web App.
    | This configuration is used to generate the manifest.json file.
    | Reference: https://developer.mozilla.org/en-US/docs/Web/Manifest
    */
    'manifest' => [
        'appName' => config('app.name'),
        'name' => config('app.name'),
        'shortName' => config('app.name'),
        'short_name' => config('app.name'),
        'startUrl' => '/',
        'start_url' => '/',
        'scope' => '/',
        'author' => 'Anixsys Pvt Ltd',
        'version' => '1.0',
        'description' => config('app.name'),
        'orientation' => 'portrait',
        'dir' => 'auto',
        'lang' => 'en',
        'display' => 'standalone',
        'themeColor' => '#30c918"',
        'theme_color' => '#30c918"',
        'backgroundColor' => '#ffffff',
        'background_color' => '#30c918"',
        'icons' => [
            [
                'src' => 'logo.png',
                'sizes' => '512x512',
                'type' => 'image/png',
            ]
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Debug Mode
    |--------------------------------------------------------------------------
    | Enables verbose logging for service worker events and cache information.
    */
    'debug' => env('LARAVEL_PWA_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Toast Content
    |--------------------------------------------------------------------------
    | Title and description text for the install prompt toast.
    */
    'title' => 'Welcome to ' . config('app.name') . '!',
    'description' => 'Click the <strong>Install Now</strong> button & enjoy it just like an app.',

    /*
    |--------------------------------------------------------------------------
    | Mobile View Position
    |--------------------------------------------------------------------------
    | Position of the PWA install toast on small devices.
    | Supported values: "top", "bottom".
    | RTL mode is supported and respects <html dir="rtl">.
    */
    'small_device_position' => 'bottom',

    /*
    |--------------------------------------------------------------------------
    | Install Now Button Text
    |--------------------------------------------------------------------------
    | Defines the text shown on the "Install Now" button inside the PWA
    | installation toast. This can be customized for localization.
    |
    | Example: 'install_now_button_text' => 'অ্যাপ ইন্সটল করুন'
    */
    'install_now_button_text' => 'Install Now',

    /*
    |--------------------------------------------------------------------------
    | Livewire Integration
    |--------------------------------------------------------------------------
    | Optimize PWA functionality for applications using Laravel Livewire.
    */
    'livewire-app' => true,
];
