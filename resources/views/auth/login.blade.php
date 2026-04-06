<x-guest-layout>
    <div class="min-h-screen bg-gradient-to-b from-slate-50 to-white flex items-center justify-center p-6">
        <div class="w-full max-w-md">

            {{-- Header --}}
            <div class="text-center mb-6">
                <div class="mx-auto mb-4 h-14 w-14 rounded-2xl bg-slate-900 text-white grid place-items-center shadow-sm">
    <svg viewBox="0 0 24 24" class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <!-- tutup botol -->
        <path d="M10 3h4" />
        <path d="M9 5h6" />
        <!-- leher botol -->
        <path d="M10 5v3c0 .6-.3 1.1-.8 1.4l-.7.4c-.9.5-1.5 1.5-1.5 2.6V19a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-6.6c0-1.1-.6-2.1-1.5-2.6l-.7-.4c-.5-.3-.8-.8-.8-1.4V5" />
        <!-- garis isi -->
        <path d="M8 15h8" />
        <path d="M8 17h8" />
    </svg>
</div>

                <h1 class="text-2xl font-bold text-slate-900">
                    SIMOBOT
                </h1>
                <p class="mt-1 text-sm text-slate-600">
                    Sistem Informasi Monitoring Botol
                </p>
            </div>

            {{-- Card --}}
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="mb-5">
                    <h2 class="text-base font-semibold text-slate-900">Masuk ke Sistem</h2>
                    <p class="mt-1 text-sm text-slate-600">
                        Silakan login menggunakan akun yang telah diberikan.
                    </p>
                </div>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label for="email" class="text-sm font-medium text-slate-700">
                            Email
                        </label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="contoh: peminjam@local.test"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-slate-900 outline-none focus:border-slate-400 focus:ring-4 focus:ring-slate-100"
                        />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    {{-- Password --}}
                    <div>
                        <div class="flex items-center justify-between">
                            <label for="password" class="text-sm font-medium text-slate-700">
                                Password
                            </label>

                            @if (Route::has('password.request'))
                                <a class="text-xs font-semibold text-slate-700 hover:text-slate-900"
                                   href="{{ route('password.request') }}">
                                    Lupa password?
                                </a>
                            @endif
                        </div>

                        <div class="mt-1 flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 focus-within:border-slate-400 focus-within:ring-4 focus-within:ring-slate-100">
                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                                class="w-full bg-transparent text-slate-900 outline-none"
                            />
                            <button
                                type="button"
                                id="togglePwd"
                                class="text-xs font-semibold text-slate-600 hover:text-slate-900"
                            >
                                Lihat
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    {{-- Remember --}}
                    <div class="flex items-center gap-2">
                        <input id="remember_me" type="checkbox"
                               class="rounded border-slate-300 text-slate-900 shadow-sm focus:ring-slate-500"
                               name="remember">
                        <label for="remember_me" class="text-sm text-slate-600">
                            Ingat saya
                        </label>
                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                        class="w-full rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800 transition">
                        Masuk
                    </button>

                    <div class="pt-2 text-center text-xs text-slate-500">
                        Akses hanya untuk pengguna terdaftar.
                    </div>
                </form>
            </div>

            {{-- Footer --}}
            <div class="mt-5 text-center text-xs text-slate-500">
                © {{ date('Y') }} SIMOBOT • Sistem Informasi Monitoring Botol
            </div>
        </div>
    </div>

    <script>
        (function () {
            const btn = document.getElementById('togglePwd');
            const input = document.getElementById('password');
            if (!btn || !input) return;

            btn.addEventListener('click', () => {
                const isHidden = input.type === 'password';
                input.type = isHidden ? 'text' : 'password';
                btn.textContent = isHidden ? 'Sembunyikan' : 'Lihat';
            });
        })();
    </script>
</x-guest-layout>