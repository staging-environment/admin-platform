<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        @php
            $isImpersonating = session()->has('impersonated_by') || (class_exists(\STS\FilamentImpersonate\Facades\Impersonation::class) && \STS\FilamentImpersonate\Facades\Impersonation::isImpersonating());
            $impersonatorUser = $isImpersonating && session('impersonated_by') ? \App\Models\User::find(session('impersonated_by')) : null;
        @endphp

        @if($isImpersonating)
            <div id="custom-impersonate-banner" style="background-color: #0f172a; color: #f8fafc; border-bottom: 2px solid #f59e0b; position: sticky; top: 0; z-index: 99999; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);" class="px-4 py-2 flex items-center justify-between text-xs sm:text-sm font-medium">
                <div class="flex items-center gap-2 sm:gap-3">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-black bg-amber-500 text-slate-950 uppercase tracking-wider shadow-sm">
                        Modo Enmascarado
                    </span>
                    <span class="text-slate-200">
                        Estás enmascarado/a como <strong class="text-amber-400 font-bold underline">{{ auth()->user()?->name ?? 'este usuario' }}</strong>
                        @if($impersonatorUser)
                            <span class="text-slate-400 text-xs hidden sm:inline">(Administrador: <strong>{{ $impersonatorUser->name }}</strong>)</span>
                        @endif
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('filament-impersonate.leave') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-black rounded-lg transition-all transform hover:scale-105 shadow text-xs">
                        <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Salir de la suplantación</span>
                    </a>
                </div>
            </div>
        @else
            <x-filament-impersonate::banner/>
        @endif
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        @livewireScripts
    </body>
</html>
