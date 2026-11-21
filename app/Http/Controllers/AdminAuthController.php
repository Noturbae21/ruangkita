<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    /**
     * Show the Admin login page.
     */
    public function showLogin(Request $request): View|RedirectResponse
    {
        if ($request->session()->get('is_admin') === true) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    /**
     * Authenticate Admin user via email/password (from seeder) or static ADMIN_TOKEN.
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $email = trim((string) $request->input('email'));
        $password = (string) $request->input('password');
        $configuredToken = env('ADMIN_TOKEN', 'admin123');

        $user = User::where('email', $email)->first();

        $isValidUser = $user && Hash::check($password, $user->password);
        $isValidFallbackAdmin = ($email === 'admin@ruangkita.id' && in_array($password, ['password', $configuredToken], true));

        if ($isValidUser || $isValidFallbackAdmin) {
            $request->session()->put('is_admin', true);
            $request->session()->put('admin_name', $user?->name ?? 'Admin RuangKita');
            $request->session()->put('admin_email', $email);

            return redirect()->route('admin.dashboard');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => 'Email atau password Admin tidak valid.',
            ]);
    }

    /**
     * Display the Admin Coworking Dashboard (/admin).
     */
    public function dashboard(Request $request): View|RedirectResponse
    {
        if ($request->session()->get('is_admin') !== true) {
            return redirect()->route('admin.login')
                ->withErrors(['email' => 'Silakan login sebagai Admin terlebih dahulu.']);
        }

        return view('admin.dashboard', [
            'adminName' => $request->session()->get('admin_name', 'Admin RuangKita'),
            'adminEmail' => $request->session()->get('admin_email', 'admin@ruangkita.id'),
        ]);
    }

    /**
     * Logout Admin session.
     */
    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget(['is_admin', 'admin_name', 'admin_email']);

        return redirect()->route('admin.login');
    }
}
