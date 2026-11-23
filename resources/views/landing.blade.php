<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RuangKita Coworking Space — Solusi Sewa Ruang Meeting Modern</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800|jetbrains-mono:400,500,600" rel="stylesheet" />

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    <style>
        body {
            font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
        }
        .font-mono-code {
            font-family: 'JetBrains Mono', ui-monospace, SFMono-Regular, monospace;
        }
        .glass-panel {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(14px);
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col antialiased selection:bg-indigo-600 selection:text-white">

    <!-- Top Gradient Accent -->
    <div class="h-1.5 w-full bg-gradient-to-r from-indigo-600 via-violet-600 to-emerald-500"></div>

    <!-- Navbar Landing Page -->
    <header class="sticky top-0 z-30 glass-panel border-b border-slate-200/80 shadow-2xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-indigo-600 to-violet-600 flex items-center justify-center text-white shadow-md shadow-indigo-500/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xl font-extrabold tracking-tight bg-gradient-to-r from-slate-900 via-indigo-950 to-indigo-700 bg-clip-text text-transparent">RuangKita</span>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200/60">Coworking Space</span>
                    </div>
                    <p class="text-xs text-slate-500 hidden sm:block">Reservasi Ruang Rapat &amp; Konferensi Instan</p>
                </div>
            </a>

            <!-- Anchor Links -->
            <nav class="hidden md:flex items-center gap-6 text-xs font-bold text-slate-600">
                <a href="#katalog-ruangan" class="hover:text-indigo-600 transition">Katalog Ruangan</a>
                <a href="#alur-sewa" class="hover:text-indigo-600 transition">Cara Sewa</a>
                <a href="#fasilitas-tambahan" class="hover:text-indigo-600 transition">Opsi Tambahan</a>
                <a href="#kebijakan" class="hover:text-indigo-600 transition">Aturan &amp; Kebijakan</a>
            </nav>

            <!-- Auth & CTA Buttons -->
            <div class="flex items-center gap-2">
                @if (session()->get('is_admin') === true)
                    <a href="{{ route('admin.dashboard') }}"
                        class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-extrabold shadow-sm transition flex items-center gap-1.5">
                        <span>Dashboard Admin</span>
                        <span>&rarr;</span>
                    </a>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit"
                            class="px-3 py-2 rounded-xl bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white border border-rose-200 text-xs font-bold transition cursor-pointer">
                            Keluar
                        </button>
                    </form>
                @elseif (session()->has('customer_id'))
                    <a href="{{ route('booking.portal') }}"
                        class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-extrabold shadow-sm shadow-indigo-600/20 transition flex items-center gap-1.5">
                        <span>Buka Halaman Sewa</span>
                        <span>&rarr;</span>
                    </a>
                    <form method="POST" action="{{ route('customer.logout') }}">
                        @csrf
                        <button type="submit"
                            class="px-3 py-2 rounded-xl bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white border border-rose-200 text-xs font-bold transition cursor-pointer">
                            Keluar
                        </button>
                    </form>
                @else
                    <a href="{{ route('customer.login') }}"
                        class="px-3.5 py-2 rounded-xl bg-white hover:bg-slate-100 border border-slate-300 text-slate-700 text-xs font-bold transition">
                        Masuk
                    </a>
                    <a href="{{ route('customer.register') }}"
                        class="hidden sm:inline-flex px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition">
                        Daftar Akun
                    </a>
                    <a href="{{ route('booking.portal') }}"
                        class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-extrabold shadow-sm shadow-indigo-600/20 transition flex items-center gap-1.5">
                        <span>Sewa Ruang</span>
                        <span>&rarr;</span>
                    </a>
                @endif
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-slate-900 text-white border-b border-slate-800">
        <div class="absolute inset-0 opacity-25 bg-[radial-gradient(#6366f1_1px,transparent_1px)] [background-size:20px_20px]"></div>
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-indigo-600/25 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 rounded-full bg-emerald-500/15 blur-3xl pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 lg:py-20">
            @if (session('auth_success'))
                <div class="mb-6 p-4 rounded-2xl bg-emerald-500/20 border border-emerald-400/40 text-emerald-200 text-xs font-bold flex items-center justify-between">
                    <span>✓ {{ session('auth_success') }}</span>
                    <button onclick="this.parentElement.remove()" class="text-emerald-200 hover:text-white cursor-pointer">✕</button>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                <div class="lg:col-span-7 space-y-6">
                    <div class="inline-flex flex-wrap items-center gap-2 px-3.5 py-1.5 rounded-full bg-indigo-500/15 border border-indigo-400/30 text-indigo-300 text-xs font-semibold">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Jam Operasional: 08.00 – 21.00 WIB</span>
                        <span>•</span>
                        <span>Konfirmasi QRIS Instan</span>
                    </div>

                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight leading-tight">
                        Ruang Rapat Modern &amp; Produktif untuk <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-emerald-400">Kolaborasi Terbaik Tim Anda.</span>
                    </h1>

                    <p class="text-slate-300 text-sm sm:text-base max-w-2xl leading-relaxed">
                        Dari diskusi tim kecil 4 orang hingga <em>workshop</em> 20 peserta. Pilih ruangan lengkap dengan simulasi denah kursi interaktif, cek ketersediaan slot secara <em>real-time</em> tanpa risiko jadwal bentrok (<em>no double-booking</em>), dan bayar praktis via QRIS.
                    </p>

                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <a href="{{ route('booking.portal') }}"
                            class="px-6 py-3.5 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-extrabold text-sm shadow-lg shadow-indigo-600/30 transition flex items-center gap-2">
                            <span>Mulai Sewa Ruang Sekarang</span>
                            <span>&rarr;</span>
                        </a>
                        <a href="#katalog-ruangan"
                            class="px-5 py-3.5 rounded-2xl bg-white/10 hover:bg-white/15 border border-white/15 text-white font-bold text-sm transition">
                            Lihat Katalog &amp; Tarif Ruangan
                        </a>
                    </div>

                    @if (! session()->has('customer_id'))
                        <p class="text-xs text-slate-400">
                            🔒 Untuk menjaga keamanan &amp; privasi jadwal, Anda akan diminta <a href="{{ route('customer.login') }}" class="text-indigo-300 font-bold underline">Masuk</a> atau <a href="{{ route('customer.register') }}" class="text-indigo-300 font-bold underline">Daftar Akun</a> sebelum melakukan pemesanan.
                        </p>
                    @else
                        <p class="text-xs text-emerald-300 font-semibold">
                            ✓ Anda sudah masuk sebagai <strong>{{ session('customer_name') }}</strong> ({{ session('customer_email') }}). Klik tombol di atas untuk langsung menyewa ruang!
                        </p>
                    @endif
                </div>

                <!-- Hero Right Feature Cards -->
                <div class="lg:col-span-5 grid grid-cols-2 gap-3.5">
                    <div class="p-5 rounded-3xl bg-white/5 border border-white/10 backdrop-blur-xs space-y-1.5">
                        <span class="text-xs font-semibold text-slate-400">Pilihan Ruang Aktif</span>
                        <p id="lp-stat-rooms" class="text-3xl font-extrabold text-white">4 Ruang</p>
                        <p class="text-xs text-emerald-400 font-medium">Kapasitas 6 s/d 20 Kursi</p>
                    </div>
                    <div class="p-5 rounded-3xl bg-white/5 border border-white/10 backdrop-blur-xs space-y-1.5">
                        <span class="text-xs font-semibold text-slate-400">Jam Operasional</span>
                        <p class="text-2xl font-extrabold text-white font-mono-code">08:00 – 21:00</p>
                        <p class="text-xs text-indigo-300 font-medium">Buka Setiap Hari (WIB)</p>
                    </div>
                    <div class="p-5 rounded-3xl bg-white/5 border border-white/10 backdrop-blur-xs space-y-1.5">
                        <span class="text-xs font-semibold text-slate-400">Durasi Fleksibel</span>
                        <p class="text-xl font-extrabold text-white">1 – 8 Jam</p>
                        <p class="text-xs text-slate-300">Tarif transparan per jam</p>
                    </div>
                    <div class="p-5 rounded-3xl bg-white/5 border border-white/10 backdrop-blur-xs space-y-1.5">
                        <span class="text-xs font-semibold text-slate-400">Garansi Pembatalan</span>
                        <p class="text-xl font-extrabold text-amber-400">&ge; 2 Jam Gratis</p>
                        <p class="text-xs text-slate-300">Bebas denda Rp 0 (&ge; 2 jam)</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 1: 5 Langkah Mudah Menyewa Ruang -->
    <section id="alur-sewa" class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8">
        <div class="text-center max-w-2xl mx-auto space-y-2">
            <span class="text-xs font-extrabold uppercase tracking-widest text-indigo-600">Alur Pemesanan Terstruktur</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">5 Langkah Mudah Menyewa Ruang Rapat</h2>
            <p class="text-xs sm:text-sm text-slate-500">Proses pemesanan dirancang bertahap (*step-by-step wizard*) agar cepat, akurat, dan bebas kesalahan jadwal.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-2xs space-y-2.5">
                <span class="w-9 h-9 rounded-2xl bg-indigo-600 text-white font-extrabold text-sm flex items-center justify-center shadow-sm">1</span>
                <h3 class="font-extrabold text-sm text-slate-900">Pilih Kategori &amp; Ruang</h3>
                <p class="text-xs text-slate-500 leading-relaxed">Tentukan jumlah peserta tim Anda, pilih kategori ruang (*Small Pod*, *Boardroom*, atau *Conference Hall*), dan lihat simulasi denah kursinya.</p>
            </div>

            <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-2xs space-y-2.5">
                <span class="w-9 h-9 rounded-2xl bg-indigo-600 text-white font-extrabold text-sm flex items-center justify-center shadow-sm">2</span>
                <h3 class="font-extrabold text-sm text-slate-900">Pilih Tanggal &amp; Jam</h3>
                <p class="text-xs text-slate-500 leading-relaxed">Lihat peta slot ketersediaan harian (08.00–21.00 WIB) dan pilih rentang waktu yang kosong dengan durasi 1 hingga 8 jam.</p>
            </div>

            <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-2xs space-y-2.5">
                <span class="w-9 h-9 rounded-2xl bg-indigo-600 text-white font-extrabold text-sm flex items-center justify-center shadow-sm">3</span>
                <h3 class="font-extrabold text-sm text-slate-900">Pilih Opsi Tambahan</h3>
                <p class="text-xs text-slate-500 leading-relaxed">Tambahkan paket *Coffee Break*, makan siang bento, proyektor ekstra, atau kamera konferensi 360° sesuai kebutuhan rapat.</p>
            </div>

            <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-2xs space-y-2.5">
                <span class="w-9 h-9 rounded-2xl bg-indigo-600 text-white font-extrabold text-sm flex items-center justify-center shadow-sm">4</span>
                <h3 class="font-extrabold text-sm text-slate-900">Data Diri Otomatis</h3>
                <p class="text-xs text-slate-500 leading-relaxed">Karena Anda sudah login dengan akun penyewa, nama lengkap dan nomor kontak Anda langsung terisi secara otomatis.</p>
            </div>

            <div class="bg-white rounded-3xl p-5 border border-emerald-200 bg-emerald-50/40 shadow-2xs space-y-2.5">
                <span class="w-9 h-9 rounded-2xl bg-emerald-600 text-white font-extrabold text-sm flex items-center justify-center shadow-sm">5</span>
                <h3 class="font-extrabold text-sm text-emerald-950">Bayar QRIS &amp; Cetak Nota</h3>
                <p class="text-xs text-emerald-800 leading-relaxed">Selesaikan pembayaran melalui kode QRIS Nasional dan cetak Nota / Invoice Resmi perusahaan dalam format PDF.</p>
            </div>
        </div>
    </section>

    <!-- Section 2: Katalog Ruangan Live -->
    <section id="katalog-ruangan" class="bg-white border-y border-slate-200/80 py-14">
        <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div class="space-y-1.5">
                    <span class="text-xs font-extrabold uppercase tracking-widest text-indigo-600">Katalog &amp; Cek Ketersediaan Ruang</span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Cek Jadwal Kosong &amp; Pilih Ruangan</h2>
                    <p class="text-xs sm:text-sm text-slate-500">Cek ketersediaan ruangan pada tanggal &amp; jam rapat Anda terlebih dahulu, lalu klik <strong>"Sewa Ruang Ini"</strong>.</p>
                </div>

                <!-- Category Filter Pills -->
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" onclick="filterLandingRooms('ALL')" data-lp-cat="ALL"
                        class="lp-cat-btn px-3.5 py-2 rounded-xl text-xs font-extrabold bg-indigo-600 text-white shadow-2xs transition cursor-pointer">
                        Semua Ruang
                    </button>
                    <button type="button" onclick="filterLandingRooms('SMALL')" data-lp-cat="SMALL"
                        class="lp-cat-btn px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition cursor-pointer">
                        Small / Pod (1–6 Org)
                    </button>
                    <button type="button" onclick="filterLandingRooms('MEDIUM')" data-lp-cat="MEDIUM"
                        class="lp-cat-btn px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition cursor-pointer">
                        Medium / Boardroom (7–12 Org)
                    </button>
                    <button type="button" onclick="filterLandingRooms('LARGE')" data-lp-cat="LARGE"
                        class="lp-cat-btn px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition cursor-pointer">
                        Large / Conference (&gt;12 Org)
                    </button>
                </div>
            </div>

            <!-- Interactive Quick Availability Checker Bar on Landing Page -->
            <div class="p-4 sm:p-5 rounded-3xl bg-slate-900 text-white shadow-lg border border-slate-800">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5 items-end">
                    <div class="lg:col-span-3">
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-indigo-300 mb-1">Tanggal Rapat (WIB)</label>
                        <input type="date" id="lp-date" onchange="checkLandingAvailability()"
                            class="w-full rounded-xl border border-slate-700 bg-slate-800 px-3.5 py-2.5 text-xs font-bold text-white focus:border-indigo-400 focus:outline-none">
                    </div>
                    <div class="lg:col-span-2">
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-indigo-300 mb-1">Jam Mulai</label>
                        <select id="lp-start" onchange="checkLandingAvailability()"
                            class="w-full rounded-xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-xs font-mono-code font-bold text-white focus:border-indigo-400 focus:outline-none">
                        </select>
                    </div>
                    <div class="lg:col-span-2">
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-indigo-300 mb-1">Jam Selesai</label>
                        <select id="lp-end" onchange="checkLandingAvailability()"
                            class="w-full rounded-xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-xs font-mono-code font-bold text-white focus:border-indigo-400 focus:outline-none">
                        </select>
                    </div>
                    <div class="lg:col-span-2">
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-indigo-300 mb-1">Jumlah Peserta</label>
                        <input type="number" id="lp-participants" min="1" max="50" value="4" oninput="renderLandingRooms()"
                            class="w-full rounded-xl border border-slate-700 bg-slate-800 px-3.5 py-2.5 text-xs font-extrabold text-white focus:border-indigo-400 focus:outline-none">
                    </div>
                    <div class="lg:col-span-3 flex items-center gap-2">
                        <button type="button" onclick="checkLandingAvailability()"
                            class="w-full py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-extrabold text-xs shadow-sm transition cursor-pointer">
                            Cek Slot Tersedia
                        </button>
                    </div>
                </div>
                <p id="lp-avail-status-text" class="text-[11px] text-slate-300 mt-2.5">
                    Menampilkan ketersediaan ruang pada jam operasional 08:00 – 21:00 WIB. Pilihan Anda di atas akan otomatis diteruskan saat Anda menekan tombol <strong>"Sewa Ruang Ini"</strong>.
                </p>
            </div>

            <div id="lp-rooms-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                <p class="text-sm text-slate-400 col-span-4 py-8 text-center">Memuat katalog ruangan...</p>
            </div>
        </div>
    </section>

    <!-- Section 3: Fasilitas Tambahan (Add-ons) & Kebijakan Transparan -->
    <section id="fasilitas-tambahan" class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-14 space-y-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- Left: Master Add-ons Showcase (7 cols) -->
            <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-2xs space-y-5">
                <div>
                    <span class="text-xs font-extrabold uppercase tracking-widest text-indigo-600">Layanan Pendukung Rapat</span>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 mt-1">Opsi Tambahan Kebutuhan (*Add-ons*)</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Dapat dipilih pada Langkah 3 saat menyewa ruangan. Biaya dihitung otomatis sesuai durasi jam atau jumlah peserta yang Anda isi.</p>
                </div>

                <div id="lp-addons-list" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <p class="text-xs text-slate-400">Memuat daftar fasilitas tambahan...</p>
                </div>
            </div>

            <!-- Right: Aturan Bisnis & Kebijakan Sewa (5 cols) -->
            <div id="kebijakan" class="lg:col-span-5 bg-slate-900 text-white rounded-3xl p-6 sm:p-8 shadow-xl space-y-5">
                <div>
                    <span class="text-xs font-extrabold uppercase tracking-widest text-emerald-400">Transparan &amp; Terjamin</span>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-white mt-1">Ketentuan &amp; Aturan Operasional</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Sistem memvalidasi setiap aturan secara otomatis untuk kenyamanan bersama.</p>
                </div>

                <ul class="space-y-3 text-xs text-slate-300">
                    <li class="flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-indigo-500/20 border border-indigo-400/40 text-indigo-300 font-bold flex items-center justify-center shrink-0 mt-0.5">✓</span>
                        <div>
                            <strong class="text-white block">Jam Operasional 08.00 – 21.00 WIB</strong>
                            <span>Seluruh jadwal pemesanan dimulai paling cepat pukul 08.00 WIB dan berakhir paling lambat pukul 21.00 WIB.</span>
                        </div>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-indigo-500/20 border border-indigo-400/40 text-indigo-300 font-bold flex items-center justify-center shrink-0 mt-0.5">✓</span>
                        <div>
                            <strong class="text-white block">Durasi Sewa Minimal 1 Jam &amp; Maksimal 8 Jam</strong>
                            <span>Mendukung rapat singkat maupun sesi workshop seharian penuh.</span>
                        </div>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-indigo-500/20 border border-indigo-400/40 text-indigo-300 font-bold flex items-center justify-center shrink-0 mt-0.5">✓</span>
                        <div>
                            <strong class="text-white block">Jaminan Anti-Bentrok (*No Overlap*)</strong>
                            <span>Sistem mengunci slot waktu secara *real-time*. Jadwal bersambung tepat di titik jam (misal selesai 10.00 &amp; mulai 10.00) tetap diperbolehkan.</span>
                        </div>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-emerald-500/20 border border-emerald-400/40 text-emerald-300 font-bold flex items-center justify-center shrink-0 mt-0.5">✓</span>
                        <div>
                            <strong class="text-white block">Kebijakan Pembatalan Adil</strong>
                            <span>Pembatalan &ge; 2 jam sebelum jam mulai <strong>Bebas Denda (Rp 0)</strong>. Pembatalan &lt; 2 jam sebelum mulai dikenakan denda 50%, dan slot ruang langsung dibuka kembali.</span>
                        </div>
                    </li>
                </ul>

                <div class="pt-3 border-t border-slate-800">
                    <a href="{{ route('booking.portal') }}"
                        class="w-full py-3.5 px-5 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-extrabold text-xs shadow-md transition flex items-center justify-center gap-2">
                        <span>Masuk ke Halaman Sewa Ruang Sekarang</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200/80 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <div class="flex items-center gap-2">
                <span class="font-extrabold text-slate-900">RuangKita Coworking Space</span>
                <span>•</span>
                <span>Sistem Pemesanan Ruang Rapat</span>
            </div>
            <div class="flex flex-wrap items-center gap-4">
                <a href="{{ route('booking.portal') }}" class="font-bold text-indigo-600 hover:underline">Halaman Sewa Ruang</a>
                <a href="{{ route('customer.login') }}" class="hover:text-slate-800">Login Penyewa</a>
                <a href="{{ route('customer.register') }}" class="hover:text-slate-800">Daftar Akun</a>
                <a href="{{ route('admin.login') }}" class="hover:text-slate-800">Portal Admin</a>
            </div>
        </div>
    </footer>

    <script>
        let lpRooms = [];
        let lpAvailableRoomIds = null; // Set of room IDs available in the selected time slot
        let lpCategory = 'ALL';

        function getTodayWibString() {
            return new Intl.DateTimeFormat('en-CA', {
                timeZone: 'Asia/Jakarta',
                year: 'numeric',
                month: '2-digit',
                day: '2-digit'
            }).format(new Date());
        }

        function populateLandingTimeDropdowns() {
            const startSelect = document.getElementById('lp-start');
            const endSelect = document.getElementById('lp-end');
            if (!startSelect || !endSelect) return;
            startSelect.innerHTML = '';
            endSelect.innerHTML = '';

            const times = [];
            for (let h = 8; h <= 21; h++) {
                const hh = String(h).padStart(2, '0');
                times.push(`${hh}:00`);
                if (h < 21) times.push(`${hh}:30`);
            }

            times.forEach(t => {
                if (t !== '21:00') {
                    const optS = document.createElement('option');
                    optS.value = t;
                    optS.textContent = t + ' WIB';
                    if (t === '09:00') optS.selected = true;
                    startSelect.appendChild(optS);
                }
                if (t !== '08:00') {
                    const optE = document.createElement('option');
                    optE.value = t;
                    optE.textContent = t + ' WIB';
                    if (t === '11:00') optE.selected = true;
                    endSelect.appendChild(optE);
                }
            });
        }

        async function checkLandingAvailability() {
            const date = document.getElementById('lp-date')?.value || getTodayWibString();
            const start = document.getElementById('lp-start')?.value || '09:00';
            const end = document.getElementById('lp-end')?.value || '11:00';
            const statusText = document.getElementById('lp-avail-status-text');

            const [sh, sm] = start.split(':').map(Number);
            const [eh, em] = end.split(':').map(Number);
            const diffMins = (eh * 60 + em) - (sh * 60 + sm);

            if (diffMins < 60 || diffMins > 480) {
                if (statusText) {
                    statusText.innerHTML = `<span class="text-rose-400 font-bold">⚠ Durasi rapat harus antara 1 jam hingga 8 jam (jam selesai harus setelah jam mulai).</span>`;
                }
                lpAvailableRoomIds = null;
                renderLandingRooms();
                return;
            }

            try {
                const res = await fetch(`/api/rooms?date=${encodeURIComponent(date)}&start_time=${encodeURIComponent(start)}&end_time=${encodeURIComponent(end)}`);
                const json = await res.json();
                if (res.ok && Array.isArray(json.data)) {
                    lpAvailableRoomIds = new Set(json.data.map(r => r.id));
                    if (statusText) {
                        statusText.innerHTML = `<span class="text-emerald-400 font-bold">✓ Hasil Cek Jadwal (${date} • ${start}–${end} WIB):</span> Ditemukan <strong>${lpAvailableRoomIds.size} ruang tersedia</strong> tanpa bentrok jadwal.`;
                    }
                }
            } catch (e) {
                console.error(e);
            }
            renderLandingRooms();
        }

        function formatRupiah(amount) {
            return 'Rp ' + Number(amount || 0).toLocaleString('id-ID');
        }

        function getCategoryInfo(room) {
            const cap = Number(room.capacity || 0);
            const layout = String(room.layout_type || '').toUpperCase();
            if (cap <= 6 || layout === 'POD') {
                return { code: 'SMALL', label: 'Small / Huddle Pod', badge: 'bg-sky-50 text-sky-700 border-sky-200' };
            }
            if (cap <= 12) {
                return { code: 'MEDIUM', label: 'Medium / Boardroom', badge: 'bg-indigo-50 text-indigo-700 border-indigo-200' };
            }
            return { code: 'LARGE', label: 'Large / Conference', badge: 'bg-violet-50 text-violet-700 border-violet-200' };
        }

        function filterLandingRooms(cat) {
            lpCategory = cat || 'ALL';
            document.querySelectorAll('.lp-cat-btn').forEach(btn => {
                if (btn.getAttribute('data-lp-cat') === lpCategory) {
                    btn.className = 'lp-cat-btn px-3.5 py-2 rounded-xl text-xs font-extrabold bg-indigo-600 text-white shadow-2xs transition cursor-pointer';
                } else {
                    btn.className = 'lp-cat-btn px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition cursor-pointer';
                }
            });
            renderLandingRooms();
        }

        function renderLandingRooms() {
            const grid = document.getElementById('lp-rooms-grid');
            const filtered = lpRooms.filter(r => lpCategory === 'ALL' || getCategoryInfo(r).code === lpCategory);

            if (!filtered.length) {
                grid.innerHTML = '<p class="text-sm text-slate-400 col-span-4 py-8 text-center">Tidak ada ruangan pada kategori ini.</p>';
                return;
            }

            const date = document.getElementById('lp-date')?.value || getTodayWibString();
            const start = document.getElementById('lp-start')?.value || '09:00';
            const end = document.getElementById('lp-end')?.value || '11:00';
            const participants = Math.max(1, Number(document.getElementById('lp-participants')?.value || 4));

            grid.innerHTML = filtered.map(room => {
                const cat = getCategoryInfo(room);
                const dimText = room.dimensions_label || `${room.length_m || 6}m × ${room.width_m || 4}m`;
                const areaText = room.area_sqm || Math.round((room.length_m || 6) * (room.width_m || 4) * 10) / 10;
                const facilitiesHtml = (room.facilities || []).map(f =>
                    `<span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[11px] font-medium">${f}</span>`
                ).join('');

                const isSlotAvailable = lpAvailableRoomIds === null ? true : lpAvailableRoomIds.has(room.id);
                const fitsParticipants = room.capacity >= participants;
                const sewaUrl = `/sewa?room_id=${room.id}&date=${encodeURIComponent(date)}&start_time=${encodeURIComponent(start)}&end_time=${encodeURIComponent(end)}&participants=${encodeURIComponent(Math.min(participants, room.capacity))}`;

                let statusBadgeHtml = '';
                if (!room.is_active) {
                    statusBadgeHtml = `<span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">Nonaktif</span>`;
                } else if (!isSlotAvailable) {
                    statusBadgeHtml = `<span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">Terisi (${start}–${end})</span>`;
                } else if (!fitsParticipants) {
                    statusBadgeHtml = `<span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">Maks. ${room.capacity} Org</span>`;
                } else {
                    statusBadgeHtml = `<span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">✓ Kosong &amp; Muat</span>`;
                }

                return `
                    <div class="rounded-3xl border ${room.is_active && isSlotAvailable && fitsParticipants ? 'border-slate-200/90 bg-white' : 'border-slate-200 bg-slate-50/70 opacity-85'} p-5 flex flex-col justify-between gap-4 shadow-2xs hover:shadow-md hover:border-indigo-300 transition">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between gap-2">
                                <span class="px-2.5 py-0.5 rounded-lg border text-[10px] font-extrabold uppercase tracking-wider ${cat.badge}">${cat.label}</span>
                                ${statusBadgeHtml}
                            </div>

                            <div>
                                <h3 class="text-lg font-extrabold text-slate-900">${room.name}</h3>
                                <p class="text-xs text-slate-500 mt-0.5">${room.layout_label || room.layout_type || 'Boardroom'}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/70">
                                    <span class="text-[10px] text-slate-400 font-bold uppercase block">Kapasitas</span>
                                    <span class="font-extrabold text-slate-800">Maks. ${room.capacity} Kursi</span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/70">
                                    <span class="text-[10px] text-slate-400 font-bold uppercase block">Ukuran Ruang</span>
                                    <span class="font-mono-code font-bold text-indigo-700">${dimText} (${areaText}m²)</span>
                                </div>
                            </div>

                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Fasilitas Bawaan:</span>
                                <div class="flex flex-wrap gap-1">${facilitiesHtml}</div>
                            </div>
                        </div>

                        <div class="pt-3.5 border-t border-slate-100 space-y-3">
                            <div class="flex items-baseline justify-between">
                                <span class="text-xs text-slate-400">Tarif Sewa</span>
                                <span class="text-base font-extrabold text-indigo-700 font-mono-code">${formatRupiah(room.price_per_hour)}<span class="text-xs font-normal text-slate-500">/jam</span></span>
                            </div>

                            ${room.is_active ? `
                                <a href="${sewaUrl}"
                                    class="w-full py-2.5 px-4 rounded-xl ${isSlotAvailable ? 'bg-indigo-600 hover:bg-indigo-700 text-white' : 'bg-slate-200 hover:bg-slate-300 text-slate-700'} font-bold text-xs shadow-xs transition flex items-center justify-center gap-1.5">
                                    <span>${isSlotAvailable ? 'Sewa Ruang Ini' : 'Pilih Jam Lain di Portal Sewa'}</span>
                                    <span>&rarr;</span>
                                </a>
                            ` : `
                                <span class="block w-full py-2.5 px-4 rounded-xl bg-slate-100 text-slate-400 font-bold text-xs text-center">Sedang Nonaktif</span>
                            `}
                        </div>
                    </div>
                `;
            }).join('');
        }

        async function loadLandingData() {
            try {
                const [roomsRes, addonsRes] = await Promise.all([
                    fetch('/api/rooms'),
                    fetch('/api/additional-facilities?is_active=1')
                ]);
                const roomsJson = await roomsRes.json();
                const addonsJson = await addonsRes.json();

                lpRooms = roomsJson.data || [];
                const activeCount = lpRooms.filter(r => r.is_active).length;
                const statEl = document.getElementById('lp-stat-rooms');
                if (statEl) statEl.textContent = `${activeCount} Ruang`;

                renderLandingRooms();

                const addons = addonsJson.data || [];
                const addonsContainer = document.getElementById('lp-addons-list');
                if (addonsContainer) {
                    addonsContainer.innerHTML = addons.map(a => {
                        const unitLabel = a.pricing_type === 'PER_HOUR' ? '/ jam' : (a.pricing_type === 'PER_PARTICIPANT' ? '/ peserta' : '/ booking');
                        return `
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col justify-between gap-2">
                                <div>
                                    <p class="text-xs font-extrabold text-slate-900">${a.name}</p>
                                    <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">${a.description || ''}</p>
                                </div>
                                <div class="pt-2 border-t border-slate-200/60 flex items-center justify-between">
                                    <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded bg-indigo-50 text-indigo-700">${a.pricing_label || a.pricing_type}</span>
                                    <span class="font-mono-code text-xs font-extrabold text-indigo-700">${formatRupiah(a.price)} <span class="font-sans font-normal text-slate-500">${unitLabel}</span></span>
                                </div>
                            </div>
                        `;
                    }).join('');
                }
            } catch (e) {
                console.error(e);
            }
        }

        document.addEventListener('DOMContentLoaded', async () => {
            const dateInput = document.getElementById('lp-date');
            if (dateInput) dateInput.value = getTodayWibString();
            populateLandingTimeDropdowns();
            await loadLandingData();
            await checkLandingAvailability();
        });
    </script>
</body>
</html>
