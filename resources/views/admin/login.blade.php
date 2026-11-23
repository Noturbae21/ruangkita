<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin — RuangKita Coworking Space</title>

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
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col justify-between relative overflow-hidden antialiased">

    <!-- Background Orbs -->
    <div class="absolute -top-32 -left-32 w-96 h-96 rounded-full bg-indigo-600/20 blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 rounded-full bg-emerald-500/15 blur-3xl pointer-events-none"></div>

    <!-- Top Bar -->
    <header class="relative z-10 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 flex items-center justify-between">
        <a href="{{ url('/') }}" class="flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white shadow-md">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div>
                <span class="text-lg font-extrabold tracking-tight text-white">RuangKita</span>
                <span class="block text-xs text-slate-400">Kembali ke Halaman Pemesan</span>
            </div>
        </a>

        <a href="{{ url('/') }}" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/15 border border-white/15 text-xs font-bold text-white transition">
            &larr; Halaman Booking Pemesan
        </a>
    </header>

    <!-- Login Card -->
    <main class="relative z-10 flex-1 flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-md bg-slate-900/90 border border-slate-800 rounded-3xl p-8 shadow-2xl space-y-6 backdrop-blur-md">
            <div class="space-y-2 text-center">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-500/15 border border-indigo-400/30 text-indigo-300 text-xs font-bold uppercase tracking-wider">
                    Portal Admin Coworking
                </span>
                <h1 class="text-2xl font-extrabold text-white">Masuk ke Dashboard Admin</h1>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Kelola data ruang meeting (kapasitas, fasilitas, tarif/jam), pantau kalender seluruh ruang, dan kelola pembatalan booking.
                </p>
            </div>

            @if ($errors->any())
                <div class="p-3.5 rounded-2xl bg-rose-500/15 border border-rose-500/30 text-rose-200 text-xs font-medium">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Email Admin</label>
                    <input type="email" name="email" value="{{ old('email', 'admin@ruangkita.id') }}" required autofocus
                        class="w-full rounded-xl border border-slate-700 bg-slate-800/90 px-4 py-3 text-sm text-white placeholder-slate-500 focus:border-indigo-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Password</label>
                    <input type="password" name="password" value="password" required
                        class="w-full rounded-xl border border-slate-700 bg-slate-800/90 px-4 py-3 text-sm text-white placeholder-slate-500 focus:border-indigo-500 focus:outline-none">
                </div>

                <button type="submit"
                    class="w-full py-3 px-5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm shadow-lg shadow-indigo-600/25 transition cursor-pointer">
                    Masuk sebagai Admin
                </button>
            </form>

            <!-- Demo Credential Helper Box -->
            <div class="p-4 rounded-2xl bg-slate-800/70 border border-slate-700/80 text-xs text-slate-300 space-y-2">
                <div class="flex items-center justify-between">
                    <p class="font-bold text-indigo-300">Akun Demo Seeder (FR-14):</p>
                    <button type="button" onclick="document.querySelector('input[name=email]').value='admin@ruangkita.id'; document.querySelector('input[name=password]').value='password';"
                        class="px-2.5 py-1 rounded-lg bg-indigo-500/20 hover:bg-indigo-500/30 border border-indigo-400/30 text-indigo-200 text-[11px] font-bold transition cursor-pointer">
                        Isi Otomatis
                    </button>
                </div>
                <div class="flex items-center justify-between font-mono-code text-[11px]">
                    <span>Email: <strong class="text-white">admin@ruangkita.id</strong></span>
                    <span>Password: <strong class="text-white">password</strong></span>
                </div>
            </div>
        </div>
    </main>

    <footer class="relative z-10 text-center py-5 text-xs text-slate-500">
        RuangKita Coworking Space • Kelompok 6 (Studi Kasus K10)
    </footer>
</body>
</html>
