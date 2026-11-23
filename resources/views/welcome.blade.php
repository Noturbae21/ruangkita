<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RuangKita — Pemesanan Ruang Meeting Coworking Space</title>

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
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(14px);
        }
        .custom-scrollbar::-webkit-scrollbar {
            height: 6px;
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 9999px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col antialiased selection:bg-indigo-600 selection:text-white">

    <!-- Top Ambient Gradient Bar -->
    <div class="h-1.5 w-full bg-gradient-to-r from-indigo-600 via-violet-600 to-emerald-500"></div>

    <!-- Header / Navbar (Portal Pemesan) -->
    <header class="sticky top-0 z-30 glass-panel border-b border-slate-200/80 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-indigo-600 to-violet-600 flex items-center justify-center text-white shadow-md shadow-indigo-500/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xl font-extrabold tracking-tight bg-gradient-to-r from-slate-900 via-indigo-950 to-indigo-700 bg-clip-text text-transparent">RuangKita</span>
                        <span class="text-[11px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200/60">Portal Pemesan</span>
                    </div>
                    <p class="text-xs text-slate-500 hidden sm:block">Pemesanan Ruang Rapat Coworking Space</p>
                </div>
            </div>

            <!-- Live Status, Clock & Admin Portal Link -->
            <div class="flex items-center gap-2.5">
                <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 border border-slate-200/80 text-xs font-medium text-slate-700">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span id="wib-clock" class="font-mono-code">Memuat waktu WIB...</span>
                </div>

                <button onclick="checkHealth()" id="health-badge" title="Status Server & Database (GET /health)"
                    class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold transition hover:bg-emerald-100 cursor-pointer">
                    <span id="health-dot" class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span id="health-text">Sistem Online</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Hero Banner (Khusus Pengguna / Pemesan) -->
    <section class="relative overflow-hidden bg-slate-900 text-white border-b border-slate-800">
        <div class="absolute inset-0 opacity-25 bg-[radial-gradient(#6366f1_1px,transparent_1px)] [background-size:20px_20px]"></div>
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-indigo-600/20 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 rounded-full bg-emerald-500/15 blur-3xl pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                <div class="lg:col-span-7 space-y-3">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/15 border border-indigo-400/30 text-indigo-300 text-xs font-semibold">
                        <span>Jam Operasional: 08.00 – 21.00 WIB</span>
                        <span>•</span>
                        <span>Durasi Booking: 1 – 8 Jam</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight leading-tight">
                        Cek Ketersediaan &amp; Pesan Ruang Rapat Tim Anda <span class="text-indigo-400">dalam Hitungan Detik.</span>
                    </h1>
                    <p class="text-slate-300 text-sm sm:text-base max-w-2xl leading-relaxed">
                        Pilih ruang sesuai kapasitas tim, klik slot waktu yang kosong pada peta ketersediaan, dan dapatkan kepastian jadwal secara instan tanpa risiko <em>double booking</em>.
                    </p>
                </div>

                <!-- Summary Stats for Booker -->
                <div class="lg:col-span-5 grid grid-cols-2 gap-3">
                    <div class="p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-xs">
                        <p class="text-xs text-slate-400 font-medium">Ruang Tersedia</p>
                        <p id="stat-active-rooms" class="text-2xl font-extrabold text-white mt-1">-</p>
                        <p class="text-[11px] text-emerald-400 mt-0.5">Ruang meeting aktif</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-xs">
                        <p class="text-xs text-slate-400 font-medium">Jam Operasional</p>
                        <p class="text-xl font-extrabold text-white mt-1">08:00 – 21:00</p>
                        <p class="text-[11px] text-indigo-300 mt-0.5">Zona Waktu WIB</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-xs">
                        <p class="text-xs text-slate-400 font-medium">Batas Durasi</p>
                        <p class="text-lg font-extrabold text-white mt-1">Min 1 Jam · Maks 8 Jam</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Harga dihitung otomatis</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-xs">
                        <p class="text-xs text-slate-400 font-medium">Aturan Pembatalan</p>
                        <p class="text-lg font-extrabold text-amber-400 mt-1">&ge; 2 Jam Gratis</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">&lt; 2 jam sebelum mulai: denda 50%</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content Container (3 Tab Khusus Pemesan) -->
    <main class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1 space-y-6">

        <!-- Alert / Notification Banner -->
        <div id="alert-box" class="hidden rounded-2xl p-4 border shadow-sm transition-all"></div>

        <!-- Navigation Tabs (Khusus Pengguna / Pemesan) -->
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-200 pb-4">
            <nav class="flex flex-wrap gap-2" aria-label="Tabs">
                <button onclick="switchTab('booking')" id="tab-btn-booking"
                    class="tab-btn px-4 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-2 bg-indigo-600 text-white shadow-sm shadow-indigo-500/20 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>1. Buat Booking &amp; Cek Slot Kosong</span>
                </button>

                <button onclick="switchTab('calendar')" id="tab-btn-calendar"
                    class="tab-btn px-4 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-2 bg-white text-slate-700 border border-slate-200 hover:bg-slate-100 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>2. Kalender Pemakaian Ruang</span>
                </button>

                <button onclick="switchTab('bookings-list')" id="tab-btn-bookings-list"
                    class="tab-btn px-4 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-2 bg-white text-slate-700 border border-slate-200 hover:bg-slate-100 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span>3. Riwayat &amp; Pembatalan Booking</span>
                </button>
            </nav>

            <button onclick="refreshAllData()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-200/70 hover:bg-slate-200 text-slate-700 transition cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span>Segarkan Jadwal</span>
            </button>
        </div>

        <!-- TAB 1: BOOKING & CEK KETERSEDIAAN -->
        <section id="tab-booking" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <!-- Left Column: Form Booking (5 cols) -->
            <div class="lg:col-span-5 bg-white rounded-3xl border border-slate-200/90 shadow-xs p-6 space-y-5">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">Formulir Pemesan</span>
                        <h2 class="text-lg font-extrabold text-slate-900">Pesan Ruang Meeting</h2>
                    </div>
                    <span class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-semibold">Konfirmasi Instan</span>
                </div>

                <form id="booking-form" onsubmit="submitBooking(event)" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Pilih Ruang Meeting</label>
                        <select id="bf-room" required onchange="onBookingRoomOrDateChange()"
                            class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-3.5 py-2.5 text-sm font-medium text-slate-900 focus:border-indigo-600 focus:bg-white focus:outline-none">
                            <option value="">Memuat daftar ruang...</option>
                        </select>
                        <p id="bf-room-meta" class="text-xs text-slate-500 mt-1.5"></p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="sm:col-span-1">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal (WIB)</label>
                            <input type="date" id="bf-date" required onchange="onBookingRoomOrDateChange()"
                                class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-3 py-2.5 text-sm font-medium text-slate-900 focus:border-indigo-600 focus:bg-white focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Jam Mulai</label>
                            <select id="bf-start" required onchange="updatePricePreview()"
                                class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-3 py-2.5 text-sm font-mono-code font-medium text-slate-900 focus:border-indigo-600 focus:bg-white focus:outline-none">
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Jam Selesai</label>
                            <select id="bf-end" required onchange="updatePricePreview()"
                                class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-3 py-2.5 text-sm font-mono-code font-medium text-slate-900 focus:border-indigo-600 focus:bg-white focus:outline-none">
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Jumlah Peserta</label>
                            <input type="number" id="bf-participants" min="1" value="4" required oninput="updatePricePreview()"
                                placeholder="Contoh: 6"
                                class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-3.5 py-2.5 text-sm font-medium text-slate-900 focus:border-indigo-600 focus:bg-white focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Pemesan</label>
                            <input type="text" id="bf-name" required placeholder="Nama lengkap / tim"
                                class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-3.5 py-2.5 text-sm font-medium text-slate-900 focus:border-indigo-600 focus:bg-white focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kontak Pemesan (No. WhatsApp / Email)</label>
                        <input type="text" id="bf-contact" required placeholder="0812xxxx atau email@perusahaan.com"
                            class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-3.5 py-2.5 text-sm font-medium text-slate-900 focus:border-indigo-600 focus:bg-white focus:outline-none">
                    </div>

                    <!-- Pilihan Fasilitas Tambahan (Add-ons) -->
                    <div class="space-y-2 pt-1">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Fasilitas Tambahan (Opsional)</label>
                            <span id="bf-addons-count-badge" class="text-[11px] font-semibold text-indigo-600">0 dipilih</span>
                        </div>
                        <div id="bf-addons-list" class="space-y-2 max-h-60 overflow-y-auto custom-scrollbar pr-1">
                            <p class="text-xs text-slate-400">Memuat daftar fasilitas tambahan...</p>
                        </div>
                    </div>

                    <!-- Server Price Estimation Box -->
                    <div class="rounded-2xl bg-gradient-to-br from-indigo-50/90 to-slate-50 p-4 border border-indigo-100 space-y-2">
                        <div class="flex items-center justify-between text-xs text-slate-600">
                            <span>Durasi Rapat:</span>
                            <span id="bf-duration-label" class="font-bold text-slate-800">2 Jam</span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-slate-600">
                            <span>Biaya Sewa Ruang:</span>
                            <span id="bf-room-price-preview" class="font-bold text-slate-800 font-mono-code">Rp 0</span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-slate-600">
                            <span>Biaya Fasilitas Tambahan:</span>
                            <span id="bf-addons-price-preview" class="font-bold text-indigo-700 font-mono-code">Rp 0</span>
                        </div>
                        <div class="flex items-center justify-between pt-1.5 border-t border-indigo-200/60">
                            <span class="text-xs font-bold uppercase tracking-wider text-indigo-900">Estimasi Total Biaya:</span>
                            <span id="bf-price-preview" class="text-lg font-extrabold text-indigo-700 font-mono-code">Rp 0</span>
                        </div>
                        <p id="bf-rule-hint" class="text-[11px] text-slate-500 leading-tight">
                            * Total harga dihitung otomatis oleh sistem berdasarkan sewa ruang + fasilitas tambahan.
                        </p>
                    </div>

                    <button type="submit" id="bf-submit-btn"
                        class="w-full py-3 px-5 rounded-xl bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-bold text-sm shadow-md shadow-indigo-600/20 transition flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                        <span>Lanjut Pembayaran QRIS Sekarang</span>
                    </button>
                </form>
            </div>

            <!-- Right Column: Cek Ketersediaan Ruang & Katalog Ruang (7 cols) -->
            <div class="lg:col-span-7 space-y-6">
                <!-- Availability Inspector Card -->
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs p-6 space-y-5">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-4">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600">Cek Ketersediaan Per Tanggal</span>
                            <h2 class="text-lg font-extrabold text-slate-900">Slot Ketersediaan Ruang (08:00 – 21:00 WIB)</h2>
                        </div>
                        <span id="avail-date-badge" class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-mono-code font-semibold"></span>
                    </div>

                    <!-- Continuous Free Ranges & Booked Summary -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-200/70">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-800 mb-2 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span>Rentang Waktu Kosong (Tersedia)</span>
                            </h3>
                            <div id="avail-free-ranges" class="space-y-1.5 text-xs text-emerald-950">
                                <p class="text-slate-400">Pilih ruang dan tanggal...</p>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-rose-50/60 border border-rose-200/70">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-rose-800 mb-2 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                <span>Slot Sudah Dipesan (Terisi)</span>
                            </h3>
                            <div id="avail-booked-ranges" class="space-y-1.5 text-xs text-rose-950">
                                <p class="text-slate-400">Belum ada booking pada tanggal ini.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Interactive 30-Min Grid -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-xs font-bold text-slate-700 uppercase tracking-wider">Pilih Cepat Jam Mulai (Klik slot hijau di bawah)</p>
                            <div class="flex items-center gap-3 text-xs">
                                <span class="inline-flex items-center gap-1 text-emerald-700 font-medium"><span class="w-2.5 h-2.5 rounded-xs bg-emerald-500 inline-block"></span> Kosong</span>
                                <span class="inline-flex items-center gap-1 text-rose-700 font-medium"><span class="w-2.5 h-2.5 rounded-xs bg-rose-500 inline-block"></span> Terisi</span>
                            </div>
                        </div>
                        <div id="avail-slots-grid" class="grid grid-cols-3 sm:grid-cols-6 md:grid-cols-7 gap-2">
                        </div>
                    </div>
                </div>

                <!-- Room Cards Grid -->
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs p-6 space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">Katalog Ruang Rapat</span>
                            <h2 class="text-lg font-extrabold text-slate-900">Pilihan Ruang &amp; Filter Kapasitas</h2>
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="text-xs font-semibold text-slate-600">Min. Kapasitas:</label>
                            <input type="number" id="filter-min-capacity" min="1" placeholder="Semua" oninput="loadRooms()"
                                class="w-24 rounded-xl border border-slate-300 px-3 py-1.5 text-xs font-medium focus:border-indigo-600 focus:outline-none">
                        </div>
                    </div>

                    <div id="rooms-catalog-grid" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    </div>
                </div>
            </div>
        </section>

        <!-- TAB 2: KALENDER PEMAKAIAN (FR-11) -->
        <section id="tab-calendar" class="hidden space-y-6">
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs p-6 space-y-5">
                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-4">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">Jadwal Pemakaian Ruang</span>
                        <h2 class="text-xl font-extrabold text-slate-900">Kalender Pemakaian Ruang (Harian &amp; Mingguan)</h2>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <select id="cal-room-filter" onchange="loadCalendar()"
                            class="rounded-xl border border-slate-300 bg-slate-50 px-3.5 py-2 text-xs font-semibold text-slate-800 focus:border-indigo-600 focus:outline-none">
                            <option value="">Semua Ruang</option>
                        </select>

                        <input type="date" id="cal-date" onchange="loadCalendar()"
                            class="rounded-xl border border-slate-300 bg-slate-50 px-3.5 py-2 text-xs font-semibold text-slate-800 focus:border-indigo-600 focus:outline-none">

                        <div class="inline-flex rounded-xl bg-slate-100 p-1 border border-slate-200">
                            <button type="button" onclick="setCalendarView('day')" id="cal-view-day"
                                class="px-3 py-1.5 rounded-lg text-xs font-bold bg-white text-indigo-700 shadow-2xs cursor-pointer">Harian</button>
                            <button type="button" onclick="setCalendarView('week')" id="cal-view-week"
                                class="px-3 py-1.5 rounded-lg text-xs font-bold text-slate-600 hover:text-slate-900 cursor-pointer">Mingguan (7 Hari)</button>
                        </div>
                    </div>
                </div>

                <div id="calendar-container" class="space-y-6">
                </div>
            </div>
        </section>

        <!-- TAB 3: RIWAYAT BOOKING PEMESAN & PEMBATALAN -->
        <section id="tab-bookings-list" class="hidden space-y-6">
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs p-6 space-y-5">
                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-4">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">Kelola Pesanan Anda</span>
                        <h2 class="text-xl font-extrabold text-slate-900">Riwayat Booking &amp; Pembatalan</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Pembatalan &lt; 2 jam sebelum jam mulai dikenai denda 50%. Pembatalan &ge; 2 jam sebelum mulai bebas denda (Rp 0).</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2.5">
                        <select id="bl-payment-filter" onchange="loadBookingsList()"
                            class="rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700">
                            <option value="">Semua Pembayaran</option>
                            <option value="PAID">QRIS LUNAS (PAID)</option>
                            <option value="UNPAID">BELUM BAYAR (UNPAID)</option>
                        </select>

                        <select id="bl-status-filter" onchange="loadBookingsList()"
                            class="rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700">
                            <option value="">Semua Status</option>
                            <option value="CONFIRMED">CONFIRMED</option>
                            <option value="CANCELLED">CANCELLED</option>
                        </select>

                        <select id="bl-room-filter" onchange="loadBookingsList()"
                            class="rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700">
                            <option value="">Semua Ruang</option>
                        </select>

                        <input type="text" id="bl-search" oninput="loadBookingsList()" placeholder="Cari nama pemesan..."
                            class="rounded-xl border border-slate-300 bg-slate-50 px-3.5 py-2 text-xs font-medium text-slate-800 focus:border-indigo-600 focus:outline-none">
                    </div>
                </div>

                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-500 bg-slate-50/70">
                                <th class="py-3.5 px-4">Kode Booking</th>
                                <th class="py-3.5 px-4">Ruang &amp; Add-on</th>
                                <th class="py-3.5 px-4">Pemesan &amp; Kontak</th>
                                <th class="py-3.5 px-4">Jadwal (WIB)</th>
                                <th class="py-3.5 px-4">Peserta</th>
                                <th class="py-3.5 px-4">Total &amp; QRIS</th>
                                <th class="py-3.5 px-4">Status &amp; Denda</th>
                                <th class="py-3.5 px-4 text-right">Aksi Pemesan</th>
                            </tr>
                        </thead>
                        <tbody id="bookings-table-body" class="divide-y divide-slate-200/70 text-sm">
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

    </main>

    <!-- QRIS Payment Gateway & Invoice Modal -->
    <div id="qris-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 backdrop-blur-xs p-4 overflow-y-auto">
        <div class="bg-white rounded-3xl max-w-md w-full border border-slate-200 shadow-2xl overflow-hidden my-8">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-indigo-600 via-violet-600 to-indigo-700 px-6 py-4 text-white flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded-md bg-white/20 text-[10px] font-extrabold uppercase tracking-wider">QRIS Payment Gateway</span>
                        <span id="qm-status-badge" class="px-2 py-0.5 rounded-md bg-amber-400 text-slate-950 text-[10px] font-extrabold uppercase">MENUNGGU PEMBAYARAN</span>
                    </div>
                    <h3 class="text-base font-extrabold mt-1" id="qm-booking-code">RK-20261125-0001</h3>
                </div>
                <button type="button" onclick="closeQrisModal()" class="w-8 h-8 rounded-full bg-white/15 hover:bg-white/25 flex items-center justify-center text-white font-bold cursor-pointer">✕</button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-4">
                <!-- QRIS Card Frame -->
                <div class="rounded-2xl border-2 border-slate-200 bg-slate-50/70 p-4 text-center space-y-3 relative overflow-hidden">
                    <div class="flex items-center justify-between border-b border-slate-200/80 pb-2.5">
                        <div class="text-left">
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-rose-600 block">QRIS · Standar Pembayaran Nasional</span>
                            <p class="text-xs font-extrabold text-slate-900">RUANGKITA COWORKING SPACE</p>
                            <p class="text-[10px] font-mono-code text-slate-500">NMID: ID1026000914300</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg bg-rose-600 text-white text-xs font-extrabold tracking-wider">QRIS</span>
                    </div>

                    <!-- Dynamic Generated SVG QR Code -->
                    <div id="qm-qr-container" class="mx-auto w-52 h-52 bg-white p-3 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-center">
                    </div>

                    <div>
                        <p class="text-[11px] text-slate-500">Total Tagihan Pembayaran</p>
                        <p id="qm-total-amount" class="text-2xl font-extrabold text-indigo-700 font-mono-code">Rp 0</p>
                        <p id="qm-reference" class="text-[10px] font-mono-code text-slate-400 mt-0.5">Ref: -</p>
                    </div>

                    <!-- Paid Stamp Overlay -->
                    <div id="qm-paid-banner" class="hidden rounded-xl bg-emerald-50 border border-emerald-200 p-2.5 text-emerald-800 text-xs font-bold">
                        ✓ PEMBAYARAN QRIS LUNAS DIVERIFIKASI
                    </div>
                </div>

                <!-- Booking & Add-ons Invoice Breakdown -->
                <div class="rounded-2xl bg-slate-50 border border-slate-200/80 p-3.5 text-xs space-y-2">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Ruang &amp; Peserta:</span>
                        <span id="qm-room-info" class="font-bold text-slate-900 text-right">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Jadwal Rapat (WIB):</span>
                        <span id="qm-schedule-info" class="font-mono-code font-bold text-indigo-700 text-right">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Pemesan:</span>
                        <span id="qm-booker-info" class="font-semibold text-slate-800 text-right">-</span>
                    </div>
                    <div class="border-t border-slate-200/80 pt-2 space-y-1">
                        <div class="flex justify-between">
                            <span class="text-slate-600">Sewa Ruang:</span>
                            <span id="qm-room-price" class="font-mono-code font-semibold text-slate-900">Rp 0</span>
                        </div>
                        <div id="qm-addons-breakdown" class="space-y-1"></div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-2 pt-1">
                    <button type="button" id="qm-pay-btn" onclick="confirmQrisPaymentFromModal()"
                        class="w-full py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md shadow-emerald-600/20 transition flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Simulasikan Scan &amp; Bayar QRIS Sekarang</span>
                    </button>

                    <div class="flex items-center gap-2">
                        <button type="button" onclick="window.print()"
                            class="flex-1 py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer">
                            Cetak Bukti / Invoice
                        </button>
                        <button type="button" onclick="closeQrisModal()"
                            class="flex-1 py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200/80 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <div class="flex items-center gap-2">
                <span class="font-bold text-slate-800">RuangKita Coworking Space</span>
                <span>•</span>
                <span>Sistem Pemesanan Ruang Rapat</span>
            </div>
            <div class="text-slate-400">
                Jam Operasional: 08.00 – 21.00 WIB
            </div>
        </div>
    </footer>

    <!-- Interactive Frontend Controller Script (Portal Pemesan) -->
    <script>
        let state = {
            rooms: [],
            additionalFacilities: [],
            selectedAddons: {}, // { [facilityId]: quantity }
            bookings: [],
            calendarView: 'day',
        };

        function formatPricingTypeLabel(type) {
            if (type === 'PER_HOUR') return '/ jam';
            if (type === 'PER_PARTICIPANT') return '/ peserta';
            return '/ booking';
        }

        function getTodayWibString() {
            const now = new Date();
            return new Intl.DateTimeFormat('en-CA', {
                timeZone: 'Asia/Jakarta',
                year: 'numeric',
                month: '2-digit',
                day: '2-digit'
            }).format(now);
        }

        function formatRupiah(amount) {
            return 'Rp ' + Number(amount || 0).toLocaleString('id-ID');
        }

        function updateWibClock() {
            const now = new Date();
            const formatted = new Intl.DateTimeFormat('id-ID', {
                timeZone: 'Asia/Jakarta',
                weekday: 'short',
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            }).format(now);
            document.getElementById('wib-clock').textContent = formatted + ' WIB';
        }

        function showAlert(type, title, message, httpStatus = null) {
            const box = document.getElementById('alert-box');
            const styles = {
                success: 'bg-emerald-50 border-emerald-300 text-emerald-950',
                error: 'bg-rose-50 border-rose-300 text-rose-950',
                warning: 'bg-amber-50 border-amber-300 text-amber-950',
                info: 'bg-indigo-50 border-indigo-300 text-indigo-950',
            };
            const badgeStyles = {
                success: 'bg-emerald-600 text-white',
                error: 'bg-rose-600 text-white',
                warning: 'bg-amber-600 text-white',
                info: 'bg-indigo-600 text-white',
            };

            box.className = `rounded-2xl p-4 border shadow-xs flex items-start justify-between gap-4 ${styles[type] || styles.info}`;
            box.innerHTML = `
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        ${httpStatus ? `<span class="px-2 py-0.5 rounded-md text-[11px] font-mono-code font-bold ${badgeStyles[type]}">HTTP ${httpStatus}</span>` : ''}
                        <span class="font-extrabold text-sm">${title}</span>
                    </div>
                    <p class="text-xs leading-relaxed">${message}</p>
                </div>
                <button onclick="document.getElementById('alert-box').classList.add('hidden')" class="text-xs font-bold opacity-70 hover:opacity-100 cursor-pointer">✕</button>
            `;
            box.classList.remove('hidden');
        }

        function switchTab(tabName) {
            const tabs = ['booking', 'calendar', 'bookings-list'];
            tabs.forEach(t => {
                const section = document.getElementById(`tab-${t}`);
                const btn = document.getElementById(`tab-btn-${t}`);
                if (t === tabName) {
                    section.classList.remove('hidden');
                    btn.className = 'tab-btn px-4 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-2 bg-indigo-600 text-white shadow-sm shadow-indigo-500/20 cursor-pointer';
                } else {
                    section.classList.add('hidden');
                    btn.className = 'tab-btn px-4 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-2 bg-white text-slate-700 border border-slate-200 hover:bg-slate-100 cursor-pointer';
                }
            });

            if (tabName === 'calendar') loadCalendar();
            if (tabName === 'bookings-list') loadBookingsList();
        }

        function populateTimeDropdowns() {
            const startSelect = document.getElementById('bf-start');
            const endSelect = document.getElementById('bf-end');
            startSelect.innerHTML = '';
            endSelect.innerHTML = '';

            const times = [];
            for (let h = 8; h <= 21; h++) {
                const hh = String(h).padStart(2, '0');
                times.push(`${hh}:00`);
                if (h < 21) {
                    times.push(`${hh}:30`);
                }
            }

            times.forEach(t => {
                if (t !== '21:00') {
                    const optStart = document.createElement('option');
                    optStart.value = t;
                    optStart.textContent = t + ' WIB';
                    if (t === '09:00') optStart.selected = true;
                    startSelect.appendChild(optStart);
                }
                if (t !== '08:00') {
                    const optEnd = document.createElement('option');
                    optEnd.value = t;
                    optEnd.textContent = t + ' WIB';
                    if (t === '11:00') optEnd.selected = true;
                    endSelect.appendChild(optEnd);
                }
            });
        }

        async function checkHealth() {
            const badge = document.getElementById('health-badge');
            const dot = document.getElementById('health-dot');
            const text = document.getElementById('health-text');
            try {
                const res = await fetch('/health');
                const data = await res.json();
                if (res.ok && data.status === 'ok') {
                    badge.className = 'hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold transition hover:bg-emerald-100 cursor-pointer';
                    dot.className = 'w-2 h-2 rounded-full bg-emerald-500 animate-pulse';
                    text.textContent = 'Sistem Online';
                } else {
                    throw new Error(data.message || 'Gangguan Sistem');
                }
            } catch (e) {
                badge.className = 'hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold transition cursor-pointer';
                dot.className = 'w-2 h-2 rounded-full bg-rose-500';
                text.textContent = 'Gangguan DB';
            }
        }

        async function loadRooms() {
            const minCap = document.getElementById('filter-min-capacity')?.value || '';
            const url = minCap ? `/api/rooms?min_capacity=${encodeURIComponent(minCap)}` : '/api/rooms';

            const res = await fetch(url);
            const json = await res.json();
            state.rooms = json.data || [];

            const activeRooms = state.rooms.filter(r => r.is_active);
            document.getElementById('stat-active-rooms').textContent = `${activeRooms.length} Ruang`;

            const bfRoom = document.getElementById('bf-room');
            const currentRoomId = bfRoom.value;
            bfRoom.innerHTML = '';

            const calRoom = document.getElementById('cal-room-filter');
            const currentCalRoom = calRoom.value;
            calRoom.innerHTML = '<option value="">Semua Ruang</option>';

            const blRoom = document.getElementById('bl-room-filter');
            const currentBlRoom = blRoom.value;
            blRoom.innerHTML = '<option value="">Semua Ruang</option>';

            state.rooms.forEach(room => {
                const opt = document.createElement('option');
                opt.value = room.id;
                opt.textContent = `${room.name} (Kapasitas ${room.capacity} org · ${formatRupiah(room.price_per_hour)}/jam)${room.is_active ? '' : ' [Tidak Tersedia]'}`;
                bfRoom.appendChild(opt);

                const optCal = document.createElement('option');
                optCal.value = room.id;
                optCal.textContent = room.name;
                calRoom.appendChild(optCal);

                const optBl = document.createElement('option');
                optBl.value = room.id;
                optBl.textContent = room.name;
                blRoom.appendChild(optBl);
            });

            if (currentRoomId && state.rooms.some(r => String(r.id) === String(currentRoomId))) {
                bfRoom.value = currentRoomId;
            }
            calRoom.value = currentCalRoom;
            blRoom.value = currentBlRoom;

            renderRoomsCatalog();
            updatePricePreview();
            loadRoomAvailability();
        }

        async function loadAdditionalFacilities() {
            try {
                const res = await fetch('/api/additional-facilities?is_active=1');
                const json = await res.json();
                state.additionalFacilities = json.data || [];
                renderBookingAddonsList();
                updatePricePreview();
            } catch (e) {
                console.error(e);
            }
        }

        function renderBookingAddonsList() {
            const container = document.getElementById('bf-addons-list');
            if (!container) return;

            if (!state.additionalFacilities.length) {
                container.innerHTML = '<p class="text-xs text-slate-400 italic">Belum ada fasilitas tambahan yang tersedia.</p>';
                return;
            }

            container.innerHTML = state.additionalFacilities.map(addon => {
                const isSelected = Object.prototype.hasOwnProperty.call(state.selectedAddons, addon.id);
                const qty = isSelected ? state.selectedAddons[addon.id] : 1;
                const showQtyInput = addon.pricing_type !== 'PER_PARTICIPANT';

                return `
                    <div class="p-2.5 rounded-xl border ${isSelected ? 'border-indigo-400 bg-indigo-50/40' : 'border-slate-200 bg-slate-50/50'} transition flex items-center justify-between gap-2.5">
                        <label class="flex items-start gap-2.5 cursor-pointer flex-1 min-w-0">
                            <input type="checkbox"
                                ${isSelected ? 'checked' : ''}
                                onchange="toggleBookingAddon(${addon.id}, this.checked)"
                                class="mt-0.5 w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500">
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-bold text-slate-900 truncate">${addon.name}</p>
                                ${addon.description ? `<p class="text-[11px] text-slate-500 line-clamp-1">${addon.description}</p>` : ''}
                                <p class="text-[11px] font-mono-code font-bold text-indigo-700 mt-0.5">
                                    ${formatRupiah(addon.price)} <span class="font-sans font-normal text-slate-500">${formatPricingTypeLabel(addon.pricing_type)}</span>
                                </p>
                            </div>
                        </label>
                        ${isSelected && showQtyInput ? `
                            <div class="flex items-center gap-1">
                                <span class="text-[10px] font-bold text-slate-500 uppercase">Jml:</span>
                                <input type="number" min="1" max="50" value="${qty}"
                                    oninput="updateBookingAddonQty(${addon.id}, this.value)"
                                    class="w-14 rounded-lg border border-indigo-300 bg-white px-2 py-1 text-xs font-bold text-center text-slate-900 focus:border-indigo-600 focus:outline-none">
                            </div>
                        ` : ''}
                    </div>
                `;
            }).join('');
        }

        function toggleBookingAddon(addonId, checked) {
            if (checked) {
                state.selectedAddons[addonId] = 1;
            } else {
                delete state.selectedAddons[addonId];
            }
            renderBookingAddonsList();
            updatePricePreview();
        }

        function updateBookingAddonQty(addonId, val) {
            const qty = Math.max(1, parseInt(val || '1', 10));
            state.selectedAddons[addonId] = qty;
            updatePricePreview();
        }

        function calculateSelectedAddonsTotal(durationMinutes, participants) {
            const hours = Math.max(0, durationMinutes / 60);
            let total = 0;
            let count = 0;

            Object.entries(state.selectedAddons).forEach(([idStr, qty]) => {
                const addon = state.additionalFacilities.find(a => String(a.id) === String(idStr));
                if (!addon) return;
                count++;
                const q = Math.max(1, Number(qty || 1));
                if (addon.pricing_type === 'PER_HOUR') {
                    total += Math.round(addon.price * hours * q);
                } else if (addon.pricing_type === 'PER_PARTICIPANT') {
                    total += Math.round(addon.price * Math.max(0, participants) * q);
                } else {
                    total += Math.round(addon.price * q);
                }
            });

            const badge = document.getElementById('bf-addons-count-badge');
            if (badge) {
                badge.textContent = `${count} dipilih`;
            }

            return total;
        }

        function renderRoomsCatalog() {
            const grid = document.getElementById('rooms-catalog-grid');
            if (!state.rooms.length) {
                grid.innerHTML = '<p class="text-sm text-slate-500 col-span-2">Tidak ada ruang yang sesuai dengan filter kapasitas.</p>';
                return;
            }

            grid.innerHTML = state.rooms.map(room => {
                const facilitiesHtml = (room.facilities || []).map(f =>
                    `<span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[11px] font-medium">${f}</span>`
                ).join('');

                return `
                    <div class="rounded-2xl border ${room.is_active ? 'border-slate-200 bg-white' : 'border-slate-200 bg-slate-50 opacity-75'} p-4 flex flex-col justify-between gap-3 hover:border-indigo-300 transition">
                        <div class="space-y-2">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <h3 class="font-extrabold text-slate-900 text-base">${room.name}</h3>
                                    <p class="text-xs text-slate-500 font-medium">Kapasitas maks. <strong class="text-slate-800">${room.capacity} peserta</strong></p>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold ${room.is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'}">
                                    ${room.is_active ? 'Tersedia' : 'Nonaktif'}
                                </span>
                            </div>
                            <div class="flex flex-wrap gap-1.5 pt-1">${facilitiesHtml}</div>
                        </div>

                        <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                            <div>
                                <span class="text-[11px] text-slate-400 block">Tarif Sewa</span>
                                <span class="text-sm font-extrabold text-indigo-700 font-mono-code">${formatRupiah(room.price_per_hour)}<span class="text-xs font-normal text-slate-500">/jam</span></span>
                            </div>
                            ${room.is_active ? `
                                <button type="button" onclick="selectRoomForBooking(${room.id})"
                                    class="px-3 py-1.5 rounded-xl text-xs font-bold bg-indigo-50 hover:bg-indigo-600 text-indigo-700 hover:text-white transition cursor-pointer">
                                    Pilih Ruang Ini
                                </button>
                            ` : `<span class="text-xs text-slate-400 font-semibold">Tidak dapat dipesan</span>`}
                        </div>
                    </div>
                `;
            }).join('');
        }

        function selectRoomForBooking(roomId) {
            const bfRoom = document.getElementById('bf-room');
            bfRoom.value = roomId;
            onBookingRoomOrDateChange();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function onBookingRoomOrDateChange() {
            updatePricePreview();
            loadRoomAvailability();
        }

        function timeToMinutes(t) {
            const [h, m] = (t || '00:00').split(':').map(Number);
            return h * 60 + m;
        }

        function updatePricePreview() {
            const roomId = document.getElementById('bf-room').value;
            const room = state.rooms.find(r => String(r.id) === String(roomId));
            const start = document.getElementById('bf-start').value;
            const end = document.getElementById('bf-end').value;
            const participants = Number(document.getElementById('bf-participants').value || 0);

            const metaEl = document.getElementById('bf-room-meta');
            const durationEl = document.getElementById('bf-duration-label');
            const roomPriceEl = document.getElementById('bf-room-price-preview');
            const addonsPriceEl = document.getElementById('bf-addons-price-preview');
            const priceEl = document.getElementById('bf-price-preview');
            const hintEl = document.getElementById('bf-rule-hint');

            if (!room) {
                metaEl.textContent = '';
                return;
            }

            metaEl.textContent = `Kapasitas: maks. ${room.capacity} orang • Fasilitas Bawaan: ${(room.facilities || []).join(', ')}`;

            const diffMinutes = timeToMinutes(end) - timeToMinutes(start);
            const hours = diffMinutes / 60;

            if (diffMinutes <= 0) {
                durationEl.textContent = 'Jam selesai harus setelah jam mulai';
                durationEl.className = 'font-bold text-rose-600';
                if (roomPriceEl) roomPriceEl.textContent = 'Rp 0';
                if (addonsPriceEl) addonsPriceEl.textContent = 'Rp 0';
                priceEl.textContent = 'Rp 0';
                hintEl.textContent = '⚠ Jam selesai harus lebih besar dari jam mulai.';
                hintEl.className = 'text-[11px] text-rose-600 font-semibold';
                return;
            }

            durationEl.textContent = `${hours} Jam (${diffMinutes} menit)`;

            if (diffMinutes < 60 || diffMinutes > 480) {
                durationEl.className = 'font-bold text-rose-600';
                hintEl.textContent = '⚠ Durasi booking minimal 1 jam (60 menit) dan maksimal 8 jam (480 menit).';
                hintEl.className = 'text-[11px] text-rose-600 font-semibold';
            } else if (participants <= 0 || participants > room.capacity) {
                durationEl.className = 'font-bold text-amber-600';
                hintEl.textContent = `⚠ Jumlah peserta (${participants} orang) melebihi kapasitas ${room.name} (${room.capacity} orang).`;
                hintEl.className = 'text-[11px] text-rose-600 font-semibold';
            } else {
                durationEl.className = 'font-bold text-slate-800';
                hintEl.textContent = '✓ Jadwal memenuhi aturan durasi (1–8 jam), jam operasional (08:00–21:00 WIB), dan kapasitas ruang.';
                hintEl.className = 'text-[11px] text-emerald-700 font-medium';
            }

            const roomTotal = Math.max(0, Math.round(hours * room.price_per_hour));
            const addonsTotal = Math.max(0, calculateSelectedAddonsTotal(diffMinutes, participants));
            const total = roomTotal + addonsTotal;

            if (roomPriceEl) roomPriceEl.textContent = formatRupiah(roomTotal);
            if (addonsPriceEl) addonsPriceEl.textContent = formatRupiah(addonsTotal);
            priceEl.textContent = formatRupiah(total);
        }

        async function loadRoomAvailability() {
            const roomId = document.getElementById('bf-room').value;
            const date = document.getElementById('bf-date').value;
            if (!roomId || !date) return;

            const room = state.rooms.find(r => String(r.id) === String(roomId));
            document.getElementById('avail-date-badge').textContent = `${room ? room.name : ''} • ${date}`;

            try {
                const res = await fetch(`/api/rooms/${roomId}/availability?date=${encodeURIComponent(date)}`);
                const json = await res.json();
                if (!res.ok) return;

                const data = json.data;
                const freeContainer = document.getElementById('avail-free-ranges');
                const bookedContainer = document.getElementById('avail-booked-ranges');
                const slotsGrid = document.getElementById('avail-slots-grid');

                if (!data.available_slots || !data.available_slots.length) {
                    freeContainer.innerHTML = '<p class="text-rose-700 font-semibold">Tidak ada slot kosong pada tanggal ini.</p>';
                } else {
                    freeContainer.innerHTML = data.available_slots.map(s => `
                        <div class="flex items-center justify-between bg-white/80 px-2.5 py-1.5 rounded-lg border border-emerald-200/80">
                            <span class="font-mono-code font-bold text-emerald-900">${s.start_time} – ${s.end_time} WIB</span>
                            <span class="text-[11px] text-emerald-700 font-semibold">${(s.duration_minutes / 60)} jam tersedia</span>
                        </div>
                    `).join('');
                }

                if (!data.booked_slots || !data.booked_slots.length) {
                    bookedContainer.innerHTML = '<p class="text-slate-500">Seluruh jam operasional 08:00–21:00 WIB masih kosong.</p>';
                } else {
                    bookedContainer.innerHTML = data.booked_slots.map(b => `
                        <div class="flex items-center justify-between bg-white/80 px-2.5 py-1.5 rounded-lg border border-rose-200/80">
                            <span class="font-mono-code font-bold text-rose-900">${b.start_time} – ${b.end_time} WIB</span>
                            <span class="text-[11px] text-rose-700 font-semibold truncate max-w-[140px]">${b.booker_name}</span>
                        </div>
                    `).join('');
                }

                slotsGrid.innerHTML = (data.slots || []).map(slot => {
                    if (slot.is_available) {
                        return `
                            <button type="button" onclick="pickSlotStartTime('${slot.start_time}')"
                                title="Klik untuk memilih jam mulai ${slot.start_time} WIB"
                                class="px-2 py-2 rounded-xl border border-emerald-200 bg-emerald-50/70 hover:bg-emerald-600 text-emerald-900 hover:text-white text-center transition cursor-pointer">
                                <span class="block font-mono-code text-xs font-bold">${slot.start_time}</span>
                                <span class="block text-[10px] opacity-75">Kosong</span>
                            </button>
                        `;
                    } else {
                        return `
                            <div title="Sudah terisi pada ${slot.start_time} - ${slot.end_time} WIB"
                                class="px-2 py-2 rounded-xl border border-rose-200 bg-rose-100/80 text-rose-800 text-center opacity-85 cursor-not-allowed">
                                <span class="block font-mono-code text-xs font-bold line-through">${slot.start_time}</span>
                                <span class="block text-[10px] font-semibold">Terisi</span>
                            </div>
                        `;
                    }
                }).join('');
            } catch (e) {
                console.error(e);
            }
        }

        function pickSlotStartTime(startTime) {
            const startSelect = document.getElementById('bf-start');
            const endSelect = document.getElementById('bf-end');
            startSelect.value = startTime;

            const startMins = timeToMinutes(startTime);
            const targetEndMins = Math.min(21 * 60, startMins + 120);
            const eh = String(Math.floor(targetEndMins / 60)).padStart(2, '0');
            const em = String(targetEndMins % 60).padStart(2, '0');
            endSelect.value = `${eh}:${em}`;

            updatePricePreview();
        }

        async function submitBooking(e) {
            e.preventDefault();
            const additional_facilities = Object.entries(state.selectedAddons).map(([id, quantity]) => ({
                id: Number(id),
                quantity: Number(quantity),
            }));

            const payload = {
                room_id: Number(document.getElementById('bf-room').value),
                date: document.getElementById('bf-date').value,
                start_time: document.getElementById('bf-start').value,
                end_time: document.getElementById('bf-end').value,
                participants: Number(document.getElementById('bf-participants').value),
                booker_name: document.getElementById('bf-name').value,
                booker_contact: document.getElementById('bf-contact').value,
                additional_facilities,
            };

            try {
                const res = await fetch('/api/bookings', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(payload),
                });
                const json = await res.json();

                if (res.status === 201) {
                    const b = json.data;
                    const addonsSummary = (b.additional_facilities && b.additional_facilities.length)
                        ? ` Fasilitas tambahan: ${b.additional_facilities.map(a => `${a.name} (x${a.quantity})`).join(', ')}.`
                        : '';
                    showAlert(
                        'success',
                        `Booking Berhasil Dibuat! (${b.booking_code || '#' + b.id})`,
                        `Ruang berhasil dipesan atas nama ${b.booker_name} pada ${b.date} pukul ${b.start_hour}–${b.end_hour} WIB.${addonsSummary} Total tagihan QRIS: ${formatRupiah(b.total_price)}. Silakan selesaikan pembayaran QRIS pada jendela yang muncul.`,
                        201
                    );
                    document.getElementById('bf-name').value = '';
                    document.getElementById('bf-contact').value = '';
                    state.selectedAddons = {};
                    renderBookingAddonsList();
                    await refreshAllData();
                    openQrisModal(b.id);
                } else {
                    showAlert(
                        'error',
                        `Booking Tidak Dapat Diproses`,
                        json.message || 'Terjadi kesalahan validasi aturan bisnis.',
                        res.status
                    );
                }
            } catch (err) {
                showAlert('error', 'Kesalahan Jaringan', err.message);
            }
        }

        function setCalendarView(view) {
            state.calendarView = view;
            const btnDay = document.getElementById('cal-view-day');
            const btnWeek = document.getElementById('cal-view-week');
            if (view === 'day') {
                btnDay.className = 'px-3 py-1.5 rounded-lg text-xs font-bold bg-white text-indigo-700 shadow-2xs cursor-pointer';
                btnWeek.className = 'px-3 py-1.5 rounded-lg text-xs font-bold text-slate-600 hover:text-slate-900 cursor-pointer';
            } else {
                btnWeek.className = 'px-3 py-1.5 rounded-lg text-xs font-bold bg-white text-indigo-700 shadow-2xs cursor-pointer';
                btnDay.className = 'px-3 py-1.5 rounded-lg text-xs font-bold text-slate-600 hover:text-slate-900 cursor-pointer';
            }
            loadCalendar();
        }

        async function loadCalendar() {
            const date = document.getElementById('cal-date').value || getTodayWibString();
            const roomId = document.getElementById('cal-room-filter').value;
            const params = new URLSearchParams({ date, view: state.calendarView });
            if (roomId) params.set('room_id', roomId);

            const res = await fetch(`/api/calendar?${params.toString()}`);
            const json = await res.json();
            const container = document.getElementById('calendar-container');

            if (!res.ok || !json.data) {
                container.innerHTML = '<p class="text-sm text-rose-600">Gagal memuat kalender pemakaian.</p>';
                return;
            }

            const days = json.data.days || [];
            container.innerHTML = days.map(dayObj => {
                const roomsHtml = (dayObj.rooms || []).map(r => {
                    const bookingsHtml = (r.bookings || []).length
                        ? r.bookings.map(b => `
                            <div class="p-3 rounded-xl bg-indigo-50/90 border border-indigo-200 flex items-center justify-between gap-2">
                                <div>
                                    <span class="font-mono-code text-xs font-extrabold text-indigo-800">${b.start_hour} – ${b.end_hour} WIB</span>
                                    <p class="text-xs font-bold text-slate-900 mt-0.5">${b.booker_name} <span class="font-normal text-slate-500">(${b.participants} org)</span></p>
                                </div>
                                <span class="px-2 py-0.5 rounded-md bg-indigo-100 text-indigo-800 text-[10px] font-bold">TERISI</span>
                            </div>
                        `).join('')
                        : `<p class="text-xs text-emerald-700 font-medium py-2">✓ Tersedia sepanjang jam operasional (08:00 – 21:00 WIB)</p>`;

                    return `
                        <div class="p-4 rounded-2xl border border-slate-200/80 bg-slate-50/50 space-y-2.5">
                            <div class="flex items-center justify-between">
                                <h4 class="font-extrabold text-sm text-slate-900">${r.room_name}</h4>
                                <span class="text-[11px] font-semibold text-slate-500">Kapasitas ${r.capacity} org</span>
                            </div>
                            <div class="space-y-2">${bookingsHtml}</div>
                        </div>
                    `;
                }).join('');

                return `
                    <div class="space-y-3">
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 rounded-full bg-slate-900 text-white font-mono-code text-xs font-bold">${dayObj.date}</span>
                            <span class="text-xs text-slate-500">Jam Operasional 08:00 – 21:00 WIB</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">${roomsHtml}</div>
                    </div>
                `;
            }).join('');
        }

        async function loadBookingsList() {
            const status = document.getElementById('bl-status-filter').value;
            const paymentStatus = document.getElementById('bl-payment-filter')?.value || '';
            const roomId = document.getElementById('bl-room-filter').value;
            const search = document.getElementById('bl-search').value;

            const params = new URLSearchParams();
            if (status) params.set('status', status);
            if (paymentStatus) params.set('payment_status', paymentStatus);
            if (roomId) params.set('room_id', roomId);
            if (search) params.set('booker_name', search);

            const res = await fetch(`/api/bookings?${params.toString()}`);
            const json = await res.json();
            state.bookings = json.data || [];

            const tbody = document.getElementById('bookings-table-body');
            if (!state.bookings.length) {
                tbody.innerHTML = `<tr><td colspan="8" class="py-8 text-center text-slate-400 text-sm">Belum ada riwayat booking yang ditemukan.</td></tr>`;
                return;
            }

            tbody.innerHTML = state.bookings.map(b => {
                const roomName = b.room ? b.room.name : `Ruang #${b.room_id}`;
                const isConfirmed = b.status === 'CONFIRMED';
                const isPaid = b.payment_status === 'PAID';
                const addonsList = b.additional_facilities || [];
                const addonsBadges = addonsList.length
                    ? `<div class="flex flex-wrap gap-1 mt-1.5">${addonsList.map(a => `<span class="px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 border border-indigo-200/70 text-[10px] font-semibold">+ ${a.name} (x${a.quantity}) · ${formatRupiah(a.subtotal)}</span>`).join('')}</div>`
                    : `<span class="text-[11px] text-slate-400 block mt-0.5">Tanpa fasilitas tambahan</span>`;

                const paymentBadge = isPaid
                    ? `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-extrabold mt-1">QRIS · LUNAS</span>`
                    : `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200 text-[10px] font-extrabold mt-1">QRIS · BELUM BAYAR</span>`;

                return `
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="py-3.5 px-4 font-mono-code text-xs">
                            <span class="font-extrabold text-indigo-700 block">${b.booking_code || '#' + b.id}</span>
                            <span class="text-[10px] text-slate-400">ID #${b.id}</span>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="font-bold text-slate-900 block">${roomName}</span>
                            ${addonsBadges}
                        </td>
                        <td class="py-3.5 px-4">
                            <p class="font-bold text-slate-900">${b.booker_name}</p>
                            <p class="text-xs text-slate-500">${b.booker_contact}</p>
                        </td>
                        <td class="py-3.5 px-4 font-mono-code text-xs">
                            <span class="font-bold text-slate-800 block">${b.date}</span>
                            <span class="text-indigo-600 font-semibold">${b.start_hour} – ${b.end_hour} WIB (${b.duration_hours} jam)</span>
                        </td>
                        <td class="py-3.5 px-4 text-xs font-semibold text-slate-700">${b.participants} orang</td>
                        <td class="py-3.5 px-4 font-mono-code text-xs">
                            <span class="font-bold text-slate-900 block">${formatRupiah(b.total_price)}</span>
                            ${b.additional_facilities_price > 0 ? `<span class="text-[10px] text-slate-500 block">Ruang: ${formatRupiah(b.room_price)} + Add-on: ${formatRupiah(b.additional_facilities_price)}</span>` : ''}
                            ${paymentBadge}
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold ${isConfirmed ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'}">
                                ${b.status}
                            </span>
                            ${b.penalty_amount > 0 ? `<p class="text-[11px] font-mono-code font-bold text-amber-700 mt-1">Denda 50%: ${formatRupiah(b.penalty_amount)}</p>` : (b.status === 'CANCELLED' ? `<p class="text-[11px] text-slate-500 mt-1">Bebas Denda (Rp 0)</p>` : '')}
                        </td>
                        <td class="py-3.5 px-4 text-right space-y-1.5">
                            <button onclick="openQrisModal(${b.id})"
                                class="w-full px-3 py-1.5 rounded-xl ${isPaid ? 'bg-slate-100 hover:bg-slate-200 text-slate-700' : 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-xs'} text-xs font-bold transition cursor-pointer">
                                ${isPaid ? 'Lihat Bukti QRIS' : 'Bayar QRIS'}
                            </button>
                            ${isConfirmed ? `
                                <button onclick="cancelBooking(${b.id})"
                                    class="w-full px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white border border-rose-200 text-xs font-bold transition cursor-pointer">
                                    Batalkan Pesanan
                                </button>
                            ` : `<span class="block text-center text-xs text-slate-400">Dibatalkan</span>`}
                        </td>
                    </tr>
                `;
            }).join('');
        }

        let currentQrisBookingId = null;

        function generateQrisSvg(payloadStr) {
            const size = 25;
            const matrix = Array.from({ length: size }, () => Array(size).fill(0));
            const reserved = Array.from({ length: size }, () => Array(size).fill(false));

            function drawFinder(r0, c0) {
                for (let r = -1; r <= 7; r++) {
                    for (let c = -1; c <= 7; c++) {
                        const rr = r0 + r, cc = c0 + c;
                        if (rr >= 0 && rr < size && cc >= 0 && cc < size) {
                            reserved[rr][cc] = true;
                            if (r >= 0 && r <= 6 && c >= 0 && c <= 6) {
                                if (r === 0 || r === 6 || c === 0 || c === 6 || (r >= 2 && r <= 4 && c >= 2 && c <= 4)) {
                                    matrix[rr][cc] = 1;
                                }
                            }
                        }
                    }
                }
            }

            drawFinder(0, 0);
            drawFinder(0, size - 7);
            drawFinder(size - 7, 0);

            // Alignment pattern at bottom-right
            const ar = size - 9, ac = size - 9;
            for (let r = -2; r <= 2; r++) {
                for (let c = -2; c <= 2; c++) {
                    reserved[ar + r][ac + c] = true;
                    if (Math.abs(r) === 2 || Math.abs(c) === 2 || (r === 0 && c === 0)) {
                        matrix[ar + r][ac + c] = 1;
                    }
                }
            }

            // Timing patterns
            for (let i = 8; i < size - 8; i++) {
                reserved[6][i] = true;
                reserved[i][6] = true;
                matrix[6][i] = i % 2 === 0 ? 1 : 0;
                matrix[i][6] = i % 2 === 0 ? 1 : 0;
            }

            // Deterministic hash fill from QRIS EMVCo payload
            let hash = 2166136261;
            const str = String(payloadStr || 'RUANGKITA-QRIS');
            for (let i = 0; i < str.length; i++) {
                hash ^= str.charCodeAt(i);
                hash = Math.imul(hash, 16777619);
            }

            let idx = 0;
            for (let r = 0; r < size; r++) {
                for (let c = 0; c < size; c++) {
                    if (!reserved[r][c]) {
                        const charCode = str.charCodeAt(idx % str.length);
                        const bit = ((hash >>> (idx % 24)) ^ charCode ^ (r * 31 + c * 17)) & 1;
                        matrix[r][c] = bit;
                        idx++;
                    }
                }
            }

            let rects = '';
            for (let r = 0; r < size; r++) {
                for (let c = 0; c < size; c++) {
                    if (matrix[r][c]) {
                        rects += `<rect x="${c}" y="${r}" width="1" height="1" fill="#0f172a"/>`;
                    }
                }
            }

            // Center QRIS badge inside SVG
            return `
                <svg viewBox="0 0 ${size} ${size}" class="w-full h-full" shape-rendering="crispEdges">
                    <rect width="${size}" height="${size}" fill="#ffffff"/>
                    ${rects}
                    <rect x="9.5" y="10.5" width="6" height="4" rx="0.6" fill="#e11d48" stroke="#ffffff" stroke-width="0.5"/>
                    <text x="12.5" y="13.2" text-anchor="middle" fill="#ffffff" font-size="2.1" font-weight="bold" font-family="monospace">QRIS</text>
                </svg>
            `;
        }

        async function openQrisModal(bookingId) {
            currentQrisBookingId = bookingId;
            try {
                const res = await fetch(`/api/bookings/${bookingId}/qris`);
                const json = await res.json();
                if (!res.ok || !json.data) {
                    showAlert('error', 'Gagal Memuat QRIS', json.message || 'Tidak dapat memuat detail pembayaran QRIS.');
                    return;
                }

                const q = json.data;
                const b = q.booking;
                const isPaid = q.payment_status === 'PAID';
                const isCancelled = b.status === 'CANCELLED';

                document.getElementById('qm-booking-code').textContent = `${q.booking_code} (#${q.booking_id})`;
                document.getElementById('qm-total-amount').textContent = formatRupiah(q.total_price);
                document.getElementById('qm-reference').textContent = `Ref: ${q.payment_reference} • Metode: QRIS`;
                document.getElementById('qm-qr-container').innerHTML = generateQrisSvg(q.qris_payload);

                document.getElementById('qm-room-info').textContent = `${b.room ? b.room.name : 'Ruang #' + b.room_id} (${b.participants} peserta)`;
                document.getElementById('qm-schedule-info').textContent = `${b.date} • ${b.start_hour}–${b.end_hour} WIB (${b.duration_hours} jam)`;
                document.getElementById('qm-booker-info').textContent = `${b.booker_name} (${b.booker_contact})`;
                document.getElementById('qm-room-price').textContent = formatRupiah(q.room_price);

                const addonsContainer = document.getElementById('qm-addons-breakdown');
                if (b.additional_facilities && b.additional_facilities.length) {
                    addonsContainer.innerHTML = b.additional_facilities.map(a => `
                        <div class="flex justify-between text-indigo-700">
                            <span>+ ${a.name} (x${a.quantity})</span>
                            <span class="font-mono-code font-semibold">${formatRupiah(a.subtotal)}</span>
                        </div>
                    `).join('');
                } else {
                    addonsContainer.innerHTML = '';
                }

                const statusBadge = document.getElementById('qm-status-badge');
                const paidBanner = document.getElementById('qm-paid-banner');
                const payBtn = document.getElementById('qm-pay-btn');

                if (isPaid) {
                    statusBadge.textContent = 'QRIS LUNAS (PAID)';
                    statusBadge.className = 'px-2 py-0.5 rounded-md bg-emerald-400 text-slate-950 text-[10px] font-extrabold uppercase';
                    paidBanner.classList.remove('hidden');
                    payBtn.classList.add('hidden');
                } else if (isCancelled) {
                    statusBadge.textContent = 'DIBATALKAN';
                    statusBadge.className = 'px-2 py-0.5 rounded-md bg-rose-400 text-white text-[10px] font-extrabold uppercase';
                    paidBanner.classList.add('hidden');
                    payBtn.classList.add('hidden');
                } else {
                    statusBadge.textContent = 'MENUNGGU PEMBAYARAN';
                    statusBadge.className = 'px-2 py-0.5 rounded-md bg-amber-400 text-slate-950 text-[10px] font-extrabold uppercase';
                    paidBanner.classList.add('hidden');
                    payBtn.classList.remove('hidden');
                }

                document.getElementById('qris-modal').classList.remove('hidden');
            } catch (e) {
                showAlert('error', 'Kesalahan Jaringan', e.message);
            }
        }

        function closeQrisModal() {
            document.getElementById('qris-modal').classList.add('hidden');
        }

        async function confirmQrisPaymentFromModal() {
            if (!currentQrisBookingId) return;
            try {
                const res = await fetch(`/api/bookings/${currentQrisBookingId}/pay`, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' },
                });
                const json = await res.json();
                if (res.ok) {
                    const b = json.data;
                    showAlert(
                        'success',
                        `Pembayaran QRIS Berhasil! (${b.booking_code})`,
                        `Pembayaran sejumlah ${formatRupiah(b.total_price)} telah diterima melalui QRIS (Ref: ${b.payment_reference}). Status pembayaran kini LUNAS (PAID).`,
                        200
                    );
                    await refreshAllData();
                    await openQrisModal(currentQrisBookingId);
                } else {
                    showAlert('error', 'Gagal Memproses Pembayaran QRIS', json.message || 'Terjadi kesalahan.', res.status);
                }
            } catch (e) {
                showAlert('error', 'Kesalahan Jaringan', e.message);
            }
        }

        async function cancelBooking(bookingId) {
            const booking = state.bookings.find(b => b.id === bookingId);
            if (!booking) return;

            const confirmMsg = `Batalkan Booking #${bookingId} (${booking.booker_name} — ${booking.date} ${booking.start_hour} WIB)?\n\nKetentuan Pembatalan:\n• Kurang dari 2 jam sebelum mulai: denda 50% (${formatRupiah(booking.total_price * 0.5)}).\n• 2 jam atau lebih sebelum mulai: bebas denda (Rp 0).`;
            if (!confirm(confirmMsg)) return;

            try {
                const res = await fetch(`/api/bookings/${bookingId}/cancel`, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' },
                });
                const json = await res.json();

                if (res.ok) {
                    const b = json.data;
                    showAlert(
                        b.penalty_amount > 0 ? 'warning' : 'success',
                        `Booking #${b.id} Berhasil Dibatalkan`,
                        `Status: ${b.status}. Denda pembatalan: ${formatRupiah(b.penalty_amount)}. Slot waktu kini telah dibuka kembali.`,
                        res.status
                    );
                    refreshAllData();
                } else {
                    showAlert('error', `Gagal Membatalkan Booking`, json.message, res.status);
                }
            } catch (e) {
                showAlert('error', 'Kesalahan Jaringan', e.message);
            }
        }

        async function refreshAllData() {
            await Promise.all([
                checkHealth(),
                loadRooms(),
                loadAdditionalFacilities(),
                loadBookingsList(),
            ]);
            if (!document.getElementById('tab-calendar').classList.contains('hidden')) {
                await loadCalendar();
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const today = getTodayWibString();
            document.getElementById('bf-date').value = today;
            document.getElementById('cal-date').value = today;

            populateTimeDropdowns();
            updateWibClock();
            setInterval(updateWibClock, 1000);

            refreshAllData();
        });
    </script>
</body>
</html>
