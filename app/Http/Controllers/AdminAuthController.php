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
     * Show the Admin login page (redirects to unified login page /login).
     */
    public function showLogin(Request $request): View|RedirectResponse
    {
        if ($request->session()->get('is_admin') === true) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('customer.login');
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

        $hasRoleCol = \Illuminate\Support\Facades\Schema::hasColumn('users', 'role');
        $isAdminAccount = $user && ((! $hasRoleCol && $email === 'admin@ruangkita.id') || ($hasRoleCol && ($user->role === 'admin' || $email === 'admin@ruangkita.id')));

        $isValidUser = $isAdminAccount && Hash::check($password, $user->password);
        $isValidFallbackAdmin = ($email === 'admin@ruangkita.id' && in_array($password, ['password', $configuredToken], true));

        if ($user && ! $isAdminAccount) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Akun ini adalah akun Penyewa (Customer) dan tidak memiliki akses ke Dashboard Admin.',
                ]);
        }

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
     * Render an authenticated Admin view with shared admin profile data.
     */
    protected function renderAdminPage(Request $request, string $view): View|RedirectResponse
    {
        if ($request->session()->get('is_admin') !== true) {
            return redirect()->route('customer.login')
                ->with('auth_notice', 'Silakan masuk sebagai Admin terlebih dahulu.');
        }

        return view($view, [
            'adminName' => $request->session()->get('admin_name', 'Admin RuangKita'),
            'adminEmail' => $request->session()->get('admin_email', 'admin@ruangkita.id'),
        ]);
    }

    /**
     * Display the Admin Room Management page (/admin).
     */
    public function dashboard(Request $request): View|RedirectResponse
    {
        return $this->renderAdminPage($request, 'admin.rooms');
    }

    /**
     * Display the Admin Additional Facilities (Add-ons) page (/admin/fasilitas).
     */
    public function facilities(Request $request): View|RedirectResponse
    {
        return $this->renderAdminPage($request, 'admin.facilities');
    }

    /**
     * Display the Admin Bookings Management page (/admin/bookings).
     */
    public function bookings(Request $request): View|RedirectResponse
    {
        return $this->renderAdminPage($request, 'admin.bookings');
    }

    /**
     * Display the Admin Operational Calendar page (/admin/kalender).
     */
    public function calendar(Request $request): View|RedirectResponse
    {
        return $this->renderAdminPage($request, 'admin.calendar');
    }

    /**
     * Logout Admin session and redirect to Landing Page (/).
     */
    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget(['is_admin', 'admin_name', 'admin_email']);

        return redirect()->route('home');
    }
}
