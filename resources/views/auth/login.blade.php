<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk Akun Penyewa — RuangKita Coworking Space</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800|jetbrains-mono:400,500,600" rel="stylesheet" />

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    <style>
        body { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }
        .font-mono-code { font-family: 'JetBrains Mono', ui-monospace, SFMono-Regular, monospace; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col justify-between relative overflow-x-hidden antialiased">

    <div class="h-1.5 w-full bg-gradient-to-r from-indigo-600 via-violet-600 to-emerald-500"></div>

    <!-- Top Bar -->
    <header class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-5 flex items-center justify-between">
        <a href="{{ url('/') }}" class="flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-indigo-600 to-violet-600 flex items-center justify-center text-white shadow-md">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div>
                <span class="text-lg font-extrabold tracking-tight text-slate-900">RuangKita</span>
                <span class="block text-xs text-slate-500">Portal Pemesan Ruang Meeting</span>
            </div>
        </a>

        <div class="flex items-center gap-2">
            <a href="{{ route('customer.register') }}" class="px-4 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 text-xs font-bold transition">
                Daftar Akun Baru
            </a>
            <a href="{{ url('/') }}" class="px-4 py-2 rounded-xl bg-white hover:bg-slate-100 border border-slate-200 text-xs font-bold text-slate-700 transition">
                &larr; Kembali ke Beranda
            </a>
        </div>
    </header>

    <!-- Login Card -->
    <main class="flex-1 flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-md bg-white border border-slate-200/90 rounded-3xl p-8 shadow-xl space-y-6">
            <div class="space-y-2 text-center">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-50 border border-indigo-200/80 text-indigo-700 text-xs font-bold uppercase tracking-wider">
                    Pintu Masuk Terpadu • Penyewa &amp; Admin
                </span>
                <h1 class="text-2xl font-extrabold text-slate-900">Masuk ke Akun RuangKita</h1>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Sistem otomatis mendeteksi akun Anda: <strong>Penyewa</strong> langsung diarahkan ke <em>Halaman Sewa (/sewa)</em> dan <strong>Admin</strong> langsung diarahkan ke <em>Dashboard Admin (/admin)</em>.
                </p>
            </div>

            @if (session('auth_notice'))
                <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs font-semibold flex items-start gap-2">
                    <span>🔒</span>
                    <span>{{ session('auth_notice') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('customer.login.submit') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Alamat Email</label>
                    <input type="email" id="cust-email" name="email" value="{{ old('email', 'budi@ruangkita.id') }}" required autofocus
                        placeholder="nama@perusahaan.com"
                        class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-3 text-sm font-medium text-slate-900 focus:border-indigo-600 focus:bg-white focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Password</label>
                    <input type="password" id="cust-password" name="password" value="password" required
                        placeholder="Masukkan password"
                        class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-3 text-sm font-medium text-slate-900 focus:border-indigo-600 focus:bg-white focus:outline-none">
                </div>

                <button type="submit"
                    class="w-full py-3.5 px-5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-sm shadow-md shadow-indigo-600/20 transition cursor-pointer">
                    Masuk Sekarang
                </button>
            </form>

            <!-- Demo Customer & Admin Credential Helper -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/90 text-xs text-slate-700 space-y-3">
                <p class="font-extrabold text-slate-800 text-[11px] uppercase tracking-wider">Pilih Cepat Akun Demo (1-Klik):</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <button type="button" onclick="document.getElementById('cust-email').value='budi@ruangkita.id'; document.getElementById('cust-password').value='password';"
                        class="p-2.5 rounded-xl bg-indigo-50 hover:bg-indigo-600 text-indigo-900 hover:text-white border border-indigo-200 text-left transition cursor-pointer group">
                        <span class="block text-[10px] font-extrabold uppercase text-indigo-600 group-hover:text-indigo-200">1. Akun Penyewa</span>
                        <span class="block font-bold text-xs mt-0.5">Budi Santoso</span>
                        <span class="block font-mono-code text-[10px] opacity-80">budi@ruangkita.id</span>
                    </button>

                    <button type="button" onclick="document.getElementById('cust-email').value='admin@ruangkita.id'; document.getElementById('cust-password').value='password';"
                        class="p-2.5 rounded-xl bg-amber-50 hover:bg-amber-600 text-amber-950 hover:text-white border border-amber-200 text-left transition cursor-pointer group">
                        <span class="block text-[10px] font-extrabold uppercase text-amber-700 group-hover:text-amber-100">2. Akun Admin</span>
                        <span class="block font-bold text-xs mt-0.5">Admin Coworking</span>
                        <span class="block font-mono-code text-[10px] opacity-80">admin@ruangkita.id</span>
                    </button>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 text-center space-y-2 text-xs">
                <p class="text-slate-600">
                    Belum punya akun penyewa?
                    <a href="{{ route('customer.register') }}" class="font-extrabold text-indigo-600 hover:underline">Daftar Akun Gratis &rarr;</a>
                </p>
            </div>
        </div>
    </main>

    <footer class="text-center py-5 text-xs text-slate-400">
        RuangKita Coworking Space • Sistem Pemesanan Ruang Rapat
    </footer>
</body>
</html>
