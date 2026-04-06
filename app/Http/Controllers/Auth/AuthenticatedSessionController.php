<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Validasi login
        $request->authenticate();

        // Regenerate session
        $request->session()->regenerate();

        $role = Auth::user()->role;

        if ($role === User::ROLE_ADMIN) {
            return redirect()->intended('/admin/botol');
        }

        if ($role === User::ROLE_ANALIS) {
            return redirect()->intended('/checkbot');
        }

        if ($role === User::ROLE_PENANGGUNG_JAWAB) {
            return redirect()->intended('/pengembalian');
        }

        return redirect()->intended('/peminjaman');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
