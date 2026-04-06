<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <!-- Left Side -->
            <div class="flex">

                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="/">
                        <span class="text-lg font-bold text-slate-900">
                            SIMOBOT
                        </span>
                    </a>
                </div>

                <div class="hidden sm:flex sm:items-center sm:ms-8 sm:space-x-2">
                    @if(auth()->user()->role === 'ADMIN' || auth()->user()->role === 'PEMINJAM')
                        <x-nav-link :href="route('peminjaman.index')" :active="request()->routeIs('peminjaman.*')">
                            Peminjaman
                        </x-nav-link>
                    @endif

                    @if(auth()->user()->role === 'ADMIN' || auth()->user()->role === 'PENANGGUNG_JAWAB')
                        <x-nav-link :href="route('pengembalian.index')" :active="request()->routeIs('pengembalian.*')">
                            Pengembalian
                        </x-nav-link>

                        <x-nav-link :href="route('histori.index')" :active="request()->routeIs('histori.*')">
                            Histori
                        </x-nav-link>
                    @endif

                    @if(auth()->user()->role === 'ADMIN' || auth()->user()->role === 'ANALIS')
                        <x-nav-link :href="route('checkbot.index')" :active="request()->routeIs('checkbot.*')">
                            Checkbot 5%
                        </x-nav-link>
                    @endif

                    @if(auth()->user()->role === 'ADMIN')
                        <x-nav-link :href="route('admin.bottles.index')" :active="request()->routeIs('admin.bottles.*')">
                            Manajemen Botol
                        </x-nav-link>
                    @endif
                </div>

            </div>

            <!-- Right Side -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">

                <!-- Dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 
                                        111.414 1.414l-4 4a1 1 0 
                                        01-1.414 0l-4-4a1 1 0 
                                        010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Profile
                        </x-dropdown-link>

                        <div class="border-t border-gray-200 my-1"></div>

                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                        this.closest('form').submit();">
                                Log Out
                            </x-dropdown-link>
                        </form>

                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Mobile Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none"
                        viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }"
                            class="inline-flex"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }"
                            class="hidden"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if(auth()->user()->role === 'ADMIN' || auth()->user()->role === 'PEMINJAM')
                <x-responsive-nav-link :href="route('peminjaman.index')" :active="request()->routeIs('peminjaman.*')">
                    Peminjaman
                </x-responsive-nav-link>
            @endif

            @if(auth()->user()->role === 'ADMIN' || auth()->user()->role === 'PENANGGUNG_JAWAB')
                <x-responsive-nav-link :href="route('pengembalian.index')" :active="request()->routeIs('pengembalian.*')">
                    Pengembalian
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('histori.index')" :active="request()->routeIs('histori.*')">
                    Histori
                </x-responsive-nav-link>
            @endif

            @if(auth()->user()->role === 'ADMIN' || auth()->user()->role === 'ANALIS')
                <x-responsive-nav-link :href="route('checkbot.index')" :active="request()->routeIs('checkbot.*')">
                    Checkbot 5%
                </x-responsive-nav-link>
            @endif

            @if(auth()->user()->role === 'ADMIN')
                <x-responsive-nav-link :href="route('admin.bottles.index')" :active="request()->routeIs('admin.bottles.*')">
                    Manajemen Botol
                </x-responsive-nav-link>
            @endif
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">
                    Profile
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        Log Out
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
