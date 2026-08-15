<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CekDulu') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center py-10 px-4 bg-gradient-to-br from-brand-900 via-brand-800 to-brand-700">
            <a href="/" wire:navigate class="flex items-center gap-2 text-white">
                <x-application-logo class="w-10 h-10 text-accent-400" />
                <span class="text-xl font-bold tracking-tight">CekDulu</span>
            </a>

            <div class="w-full sm:max-w-md mt-8 px-6 py-8 bg-white shadow-xl shadow-brand-950/20 overflow-hidden rounded-2xl border border-brand-100">
                {{ $slot }}
            </div>

            <a href="/" wire:navigate class="mt-6 text-sm text-brand-100 hover:text-white transition">
                &larr; Kembali ke beranda
            </a>
        </div>
    </body>
</html>
