<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Akun Penyewa — RuangKita Coworking Space</title>

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
            <a href="{{ route('customer.login') }}" class="px-4 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 text-xs font-bold transition">
                Sudah Punya Akun? Masuk
            </a>
            <a href="{{ url('/') }}" class="px-4 py-2 rounded-xl bg-white hover:bg-slate-100 border border-slate-200 text-xs font-bold text-slate-700 transition">
                &larr; Kembali ke Beranda
            </a>
        </div>
    </header>

    <!-- Register Card -->
    <main class="flex-1 flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-md bg-white border border-slate-200/90 rounded-3xl p-8 shadow-xl space-y-6">
            <div class="space-y-2 text-center">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold uppercase tracking-wider">
                    Registrasi Penyewa Baru
                </span>
                <h1 class="text-2xl font-extrabold text-slate-900">Buat Akun RuangKita</h1>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Daftar gratis dalam 30 detik untuk memesan ruang meeting lebih cepat dan mengelola seluruh riwayat reservasi tim Anda.
                </p>
            </div>

            @if ($errors->any())
                <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold space-y-1">
                    @foreach ($errors->all() as $error)
                        <p>• {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('customer.register.submit') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Nama Lengkap / Nama Tim</label>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus
                        placeholder="Contoh: Rina Kartika / Tim Kreatif"
                        class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-2.5 text-sm font-medium text-slate-900 focus:border-indigo-600 focus:bg-white focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Alamat Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        placeholder="rina@perusahaan.com"
                        class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-2.5 text-sm font-medium text-slate-900 focus:border-indigo-600 focus:bg-white focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">No. WhatsApp / Telepon Aktif</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required
                        placeholder="081234567890"
                        class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-2.5 text-sm font-medium text-slate-900 focus:border-indigo-600 focus:bg-white focus:outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Password</label>
                        <input type="password" name="password" required
                            placeholder="Min. 6 karakter"
                            class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-3.5 py-2.5 text-sm font-medium text-slate-900 focus:border-indigo-600 focus:bg-white focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Ulangi Password</label>
                        <input type="password" name="password_confirmation" required
                            placeholder="Ketik ulang"
                            class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-3.5 py-2.5 text-sm font-medium text-slate-900 focus:border-indigo-600 focus:bg-white focus:outline-none">
                    </div>
                </div>

                <button type="submit"
                    class="w-full py-3.5 px-5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-sm shadow-md shadow-indigo-600/20 transition cursor-pointer">
                    Daftar &amp; Masuk Sekarang
                </button>
            </form>

            <div class="pt-3 border-t border-slate-100 text-center text-xs text-slate-600">
                Sudah memiliki akun penyewa?
                <a href="{{ route('customer.login') }}" class="font-extrabold text-indigo-600 hover:underline">Masuk di sini &rarr;</a>
            </div>
        </div>
    </main>

    <footer class="text-center py-5 text-xs text-slate-400">
        RuangKita Coworking Space • Sistem Pemesanan Ruang Rapat
    </footer>
</body>
</html>
