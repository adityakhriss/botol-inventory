<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/svg+xml" href="{{ asset('sipentol.svg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-100">
            @include('layouts.navigation')

            <div class="lg:pl-64">
                <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/95 backdrop-blur">
                    <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
                        <div class="flex items-center gap-3">
                            <button
                                type="button"
                                class="inline-flex items-center justify-center rounded-lg border border-slate-200 p-2 text-slate-600 hover:bg-slate-100 lg:hidden"
                                @click="sidebarOpen = true"
                            >
                                <span class="sr-only">Buka menu</span>
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>

                        </div>

                        <div class="flex items-center gap-3">
                            <div class="hidden text-right sm:block">
                                <p class="text-sm font-medium text-slate-800">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-slate-500">{{ Auth::user()->email }}</p>
                            </div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button
                                    type="submit"
                                    class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-100"
                                >
                                    Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                </header>

                @isset($header)
                    <header class="border-b border-slate-200 bg-white">
                        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <main>
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
