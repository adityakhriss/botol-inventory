@php
    $sidebarLinkClass = static fn (bool $active): string => $active
        ? 'flex items-center rounded-xl bg-slate-900 px-3 py-2 text-sm font-semibold text-white'
        : 'flex items-center rounded-xl px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100';
@endphp

<aside class="hidden lg:fixed lg:inset-y-0 lg:flex lg:w-64 lg:flex-col lg:border-r lg:border-slate-200 lg:bg-white">
    <div class="flex h-16 items-center border-b border-slate-200 px-5">
        <a href="/" class="text-lg font-semibold tracking-wide text-slate-900">SIMOBOT</a>
    </div>

    <div class="flex flex-1 flex-col justify-between px-4 py-5">
        <nav class="space-y-1">
            @if(auth()->user()->role === 'ADMIN' || auth()->user()->role === 'PEMINJAM')
                <a href="{{ route('peminjaman.index') }}" class="{{ $sidebarLinkClass(request()->routeIs('peminjaman.*')) }}">
                    Peminjaman
                </a>
            @endif

            @if(auth()->user()->role === 'ADMIN' || auth()->user()->role === 'PENANGGUNG_JAWAB')
                <a href="{{ route('pengembalian.index') }}" class="{{ $sidebarLinkClass(request()->routeIs('pengembalian.*')) }}">
                    Pengembalian
                </a>

                <a href="{{ route('histori.index') }}" class="{{ $sidebarLinkClass(request()->routeIs('histori.*')) }}">
                    Histori
                </a>
            @endif

            @if(auth()->user()->role === 'ADMIN' || auth()->user()->role === 'ANALIS')
                <a href="{{ route('checkbot.index') }}" class="{{ $sidebarLinkClass(request()->routeIs('checkbot.*')) }}">
                    Checkbot 5%
                </a>
            @endif

            @if(auth()->user()->role === 'ADMIN')
                <a href="{{ route('admin.bottles.index') }}" class="{{ $sidebarLinkClass(request()->routeIs('admin.bottles.*')) }}">
                    Manajemen Botol
                </a>
            @endif
        </nav>

    </div>
</aside>

<div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-slate-900/40 lg:hidden" @click="sidebarOpen = false"></div>

<aside
    x-show="sidebarOpen"
    x-transition:enter="transform transition ease-out duration-200"
    x-transition:enter-start="-translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transform transition ease-in duration-150"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="-translate-x-full"
    class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col border-r border-slate-200 bg-white lg:hidden"
>
    <div class="flex h-16 items-center justify-between border-b border-slate-200 px-5">
        <a href="/" class="text-lg font-semibold tracking-wide text-slate-900">SIMOBOT</a>

        <button type="button" class="rounded-lg border border-slate-200 p-2 text-slate-600 hover:bg-slate-100" @click="sidebarOpen = false">
            <span class="sr-only">Tutup menu</span>
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div class="flex flex-1 flex-col justify-between px-4 py-5">
        <nav class="space-y-1">
            @if(auth()->user()->role === 'ADMIN' || auth()->user()->role === 'PEMINJAM')
                <a href="{{ route('peminjaman.index') }}" class="{{ $sidebarLinkClass(request()->routeIs('peminjaman.*')) }}" @click="sidebarOpen = false">
                    Peminjaman
                </a>
            @endif

            @if(auth()->user()->role === 'ADMIN' || auth()->user()->role === 'PENANGGUNG_JAWAB')
                <a href="{{ route('pengembalian.index') }}" class="{{ $sidebarLinkClass(request()->routeIs('pengembalian.*')) }}" @click="sidebarOpen = false">
                    Pengembalian
                </a>

                <a href="{{ route('histori.index') }}" class="{{ $sidebarLinkClass(request()->routeIs('histori.*')) }}" @click="sidebarOpen = false">
                    Histori
                </a>
            @endif

            @if(auth()->user()->role === 'ADMIN' || auth()->user()->role === 'ANALIS')
                <a href="{{ route('checkbot.index') }}" class="{{ $sidebarLinkClass(request()->routeIs('checkbot.*')) }}" @click="sidebarOpen = false">
                    Checkbot 5%
                </a>
            @endif

            @if(auth()->user()->role === 'ADMIN')
                <a href="{{ route('admin.bottles.index') }}" class="{{ $sidebarLinkClass(request()->routeIs('admin.bottles.*')) }}" @click="sidebarOpen = false">
                    Manajemen Botol
                </a>
            @endif
        </nav>

    </div>
</aside>
