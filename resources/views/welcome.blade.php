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
        @media print {
            body {
                background: #ffffff !important;
                color: #0f172a !important;
            }
            header, section, main, footer, #toast-container, .no-print {
                display: none !important;
            }
            #qris-modal {
                position: static !important;
                display: block !important;
                background: transparent !important;
                padding: 0 !important;
                margin: 0 !important;
                overflow: visible !important;
            }
            #qris-modal-card {
                max-width: 100% !important;
                width: 100% !important;
                box-shadow: none !important;
                border: 1px solid #cbd5e1 !important;
                margin: 0 !important;
                border-radius: 12px !important;
            }
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
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

            <!-- Live Status, Clock & Customer Auth Controls -->
            <div class="flex items-center gap-2.5">
                <a href="{{ route('home') }}"
                    class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-200/80 text-xs font-bold text-slate-700 transition">
                    &larr; Beranda
                </a>

                <div class="hidden lg:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 border border-slate-200/80 text-xs font-medium text-slate-700">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span id="wib-clock" class="font-mono-code">Memuat waktu WIB...</span>
                </div>

                <button onclick="checkHealth()" id="health-badge" title="Status Server & Database (GET /health)"
                    class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold transition hover:bg-emerald-100 cursor-pointer">
                    <span id="health-dot" class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span id="health-text">Sistem Online</span>
                </button>

                @if (session()->has('customer_id'))
                    <div class="flex items-center gap-2 pl-1 border-l border-slate-200">
                        <button type="button" onclick="switchTab('bookings-list')"
                            class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 border border-indigo-200/80 text-xs font-bold text-indigo-900 transition cursor-pointer">
                            <span class="w-6 h-6 rounded-full bg-indigo-600 text-white text-[11px] font-extrabold flex items-center justify-center">
                                {{ strtoupper(substr((string) session('customer_name', 'U'), 0, 1)) }}
                            </span>
                            <span class="max-w-[120px] truncate">{{ session('customer_name') }}</span>
                        </button>
                        <form method="POST" action="{{ route('customer.logout') }}">
                            @csrf
                            <button type="submit"
                                class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white border border-rose-200 text-xs font-bold transition cursor-pointer">
                                Keluar
                            </button>
                        </form>
                    </div>
                @else
                    <div class="flex items-center gap-1.5 pl-1 border-l border-slate-200">
                        <a href="{{ route('customer.login') }}"
                            class="px-3.5 py-1.5 rounded-xl bg-white hover:bg-slate-100 border border-slate-300 text-slate-700 text-xs font-bold transition">
                            Masuk
                        </a>
                        <a href="{{ route('customer.register') }}"
                            class="px-3.5 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-2xs transition">
                            Daftar Akun
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </header>

    <!-- Floating Toast Notification (Tetap terlihat saat pengguna scroll) -->
    <div id="toast-container" class="fixed bottom-5 right-5 z-50 max-w-md w-full px-4 sm:px-0 pointer-events-none space-y-2"></div>

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
                    <span>1. Buat Booking Baru (Wizard 5 Langkah)</span>
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
                    <span id="tab-bookings-count-badge" class="px-2 py-0.5 rounded-full text-[11px] font-extrabold bg-slate-100 text-slate-700">0</span>
                </button>
            </nav>

            <button onclick="refreshAllData()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-200/70 hover:bg-slate-200 text-slate-700 transition cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span>Segarkan Jadwal</span>
            </button>
        </div>

        <!-- TAB 1: BOOKING WIZARD (5 LANGKAH TERPISAH) -->
        <section id="tab-booking" class="space-y-6">

            <!-- Interactive 5-Step Progress Bar -->
            <div class="bg-white rounded-3xl border border-slate-200/90 p-4 sm:p-5 shadow-2xs">
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5" id="wizard-progress-bar">
                    <button type="button" onclick="goToBookingStep(1)" id="wiz-nav-1"
                        class=" flex items-center gap-2.5 p-2.5 rounded-2xl border-2 border-indigo-600 bg-indigo-50/70 text-left transition cursor-pointer">
                        <span id="wiz-circle-1" class="w-7 h-7 rounded-full bg-indigo-600 text-white text-xs font-extrabold flex items-center justify-center shrink-0">1</span>
                        <div class="min-w-0">
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-indigo-600">Langkah 1</span>
                            <span class="block text-xs font-extrabold text-slate-900 truncate">Kategori &amp; Ruang</span>
                        </div>
                    </button>

                    <button type="button" onclick="goToBookingStep(2)" id="wiz-nav-2"
                        class="flex items-center gap-2.5 p-2.5 rounded-2xl border border-slate-200 bg-slate-50/60 hover:bg-slate-100 text-left transition cursor-pointer">
                        <span id="wiz-circle-2" class="w-7 h-7 rounded-full bg-slate-200 text-slate-700 text-xs font-extrabold flex items-center justify-center shrink-0">2</span>
                        <div class="min-w-0">
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Langkah 2</span>
                            <span class="block text-xs font-extrabold text-slate-700 truncate">Tanggal &amp; Jam</span>
                        </div>
                    </button>

                    <button type="button" onclick="goToBookingStep(3)" id="wiz-nav-3"
                        class="flex items-center gap-2.5 p-2.5 rounded-2xl border border-slate-200 bg-slate-50/60 hover:bg-slate-100 text-left transition cursor-pointer">
                        <span id="wiz-circle-3" class="w-7 h-7 rounded-full bg-slate-200 text-slate-700 text-xs font-extrabold flex items-center justify-center shrink-0">3</span>
                        <div class="min-w-0">
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Langkah 3</span>
                            <span class="block text-xs font-extrabold text-slate-700 truncate">Opsi Tambahan</span>
                        </div>
                    </button>

                    <button type="button" onclick="goToBookingStep(4)" id="wiz-nav-4"
                        class="flex items-center gap-2.5 p-2.5 rounded-2xl border border-slate-200 bg-slate-50/60 hover:bg-slate-100 text-left transition cursor-pointer">
                        <span id="wiz-circle-4" class="w-7 h-7 rounded-full bg-slate-200 text-slate-700 text-xs font-extrabold flex items-center justify-center shrink-0">4</span>
                        <div class="min-w-0">
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Langkah 4</span>
                            <span class="block text-xs font-extrabold text-slate-700 truncate">Isi Data Diri</span>
                        </div>
                    </button>

                    <button type="button" onclick="goToBookingStep(5)" id="wiz-nav-5"
                        class="col-span-2 sm:col-span-1 flex items-center gap-2.5 p-2.5 rounded-2xl border border-slate-200 bg-slate-50/60 hover:bg-slate-100 text-left transition cursor-pointer">
                        <span id="wiz-circle-5" class="w-7 h-7 rounded-full bg-slate-200 text-slate-700 text-xs font-extrabold flex items-center justify-center shrink-0">5</span>
                        <div class="min-w-0">
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Langkah 5</span>
                            <span class="block text-xs font-extrabold text-slate-700 truncate">Pembayaran QRIS</span>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Wizard Main Grid: Left Active Step Screen (8 cols) + Right Sticky Live Summary (4 cols) -->
            <form id="booking-form" onsubmit="submitBooking(event)" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                <!-- LEFT 8 COLS: ACTIVE STEP CONTAINER -->
                <div class="lg:col-span-8 space-y-6">

                    <!-- ==================== LAYAR LANGKAH 1: PILIH KATEGORI & RUANGAN ==================== -->
                    <div id="wizard-panel-1" class="bg-white rounded-3xl border border-slate-200/90 shadow-xs p-6 space-y-6">
                        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4">
                            <div>
                                <span class="text-[11px] font-extrabold uppercase tracking-wider text-indigo-600">Langkah 1 dari 5</span>
                                <h2 class="text-xl font-extrabold text-slate-900">Pilih Kategori &amp; Katalog Ruangan</h2>
                                <p class="text-xs text-slate-500 mt-0.5">Tentukan jumlah peserta tim Anda dan pilih kategori ruangan, lalu klik salah satu kartu ruangan di katalog.</p>
                            </div>
                            <span id="bf-capacity-status-badge" class="text-xs font-bold px-3 py-1 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200">Kapasitas Sesuai</span>
                        </div>

                        <!-- 1A. Baris Pengaturan Jumlah Peserta & Filter Kategori Ruangan -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                                <!-- Jumlah Peserta -->
                                <div class="md:col-span-5">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="block text-xs font-extrabold text-slate-800 uppercase tracking-wider">1. Jumlah Peserta Rapat</label>
                                        <div class="flex items-center gap-1">
                                            <button type="button" onclick="setParticipantsCount(2)" class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-white border border-slate-200 hover:border-indigo-400 cursor-pointer">2</button>
                                            <button type="button" onclick="setParticipantsCount(4)" class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-white border border-slate-200 hover:border-indigo-400 cursor-pointer">4</button>
                                            <button type="button" onclick="setParticipantsCount(8)" class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-white border border-slate-200 hover:border-indigo-400 cursor-pointer">8</button>
                                            <button type="button" onclick="setParticipantsCount(12)" class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-white border border-slate-200 hover:border-indigo-400 cursor-pointer">12</button>
                                            <button type="button" onclick="setParticipantsToRoomMax()" class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 cursor-pointer">Maks</button>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="adjustParticipants(-1)"
                                            class="w-10 h-10 rounded-xl border border-slate-300 bg-white hover:bg-slate-100 text-slate-700 font-extrabold text-base flex items-center justify-center transition cursor-pointer">−</button>
                                        <input type="number" id="bf-participants" min="1" value="4" required oninput="onParticipantsInputChange()"
                                            class="flex-1 rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-sm font-extrabold text-center text-slate-900 focus:border-indigo-600 focus:outline-none">
                                        <button type="button" onclick="adjustParticipants(1)"
                                            class="w-10 h-10 rounded-xl border border-slate-300 bg-white hover:bg-slate-100 text-slate-700 font-extrabold text-base flex items-center justify-center transition cursor-pointer">+</button>
                                    </div>
                                </div>

                                <!-- Pilihan Cepat Dropdown & Filter Hanya yang Muat -->
                                <div class="md:col-span-7 flex flex-col justify-between gap-2">
                                    <div>
                                        <div class="flex items-center justify-between mb-1.5">
                                            <label class="block text-xs font-extrabold text-slate-800 uppercase tracking-wider">Ruang Meeting Terpilih</label>
                                            <label class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 cursor-pointer">
                                                <input type="checkbox" id="filter-fit-participants" onchange="renderRoomsCatalog()" class="w-3.5 h-3.5 rounded text-indigo-600">
                                                <span>Hanya tampilkan ruang yang muat</span>
                                            </label>
                                        </div>
                                        <select id="bf-room" required onchange="onBookingRoomOrDateChange()"
                                            class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm font-bold text-slate-900 focus:border-indigo-600 focus:outline-none">
                                            <option value="">Memuat daftar ruang...</option>
                                        </select>
                                        <p id="bf-room-meta" class="text-[11px] text-slate-500 mt-1 leading-relaxed"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- 1B. Tab Kategori Ruangan -->
                            <div class="pt-2 border-t border-slate-200/70">
                                <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                                    <label class="block text-xs font-extrabold text-slate-800 uppercase tracking-wider">2. Filter Kategori Ruangan</label>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[11px] text-slate-500">Urutkan Katalog:</span>
                                        <select id="catalog-sort-by" onchange="renderRoomsCatalog()"
                                            class="rounded-lg border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 focus:border-indigo-600 focus:outline-none">
                                            <option value="recommended">Rekomendasi Kapasitas</option>
                                            <option value="price_asc">Harga Terendah</option>
                                            <option value="capacity_asc">Kapasitas Kecil &rarr; Besar</option>
                                            <option value="capacity_desc">Kapasitas Besar &rarr; Kecil</option>
                                        </select>
                                        <input type="hidden" id="filter-min-capacity" value="">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5" id="bf-category-pills">
                                    <button type="button" onclick="selectRoomCategory('ALL')" data-cat-pill="ALL"
                                        class="cat-pill-btn p-3 rounded-2xl text-left border border-indigo-600 bg-indigo-600 text-white transition cursor-pointer">
                                        <div class="flex items-center justify-between">
                                            <span class="block text-xs font-extrabold">Semua Ruang</span>
                                            <span id="cat-count-ALL" class="px-1.5 py-0.5 rounded-md bg-white/20 text-[10px] font-extrabold">0</span>
                                        </div>
                                        <span class="block text-[11px] opacity-85 mt-0.5">Seluruh kategori</span>
                                    </button>
                                    <button type="button" onclick="selectRoomCategory('SMALL')" data-cat-pill="SMALL"
                                        class="cat-pill-btn p-3 rounded-2xl text-left border border-slate-200 bg-white text-slate-700 hover:border-indigo-400 transition cursor-pointer">
                                        <div class="flex items-center justify-between">
                                            <span class="block text-xs font-extrabold">Small / Pod</span>
                                            <span id="cat-count-SMALL" class="px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[10px] font-extrabold">0</span>
                                        </div>
                                        <span class="block text-[11px] text-slate-500 mt-0.5">1 – 6 Orang · Diskusi</span>
                                    </button>
                                    <button type="button" onclick="selectRoomCategory('MEDIUM')" data-cat-pill="MEDIUM"
                                        class="cat-pill-btn p-3 rounded-2xl text-left border border-slate-200 bg-white text-slate-700 hover:border-indigo-400 transition cursor-pointer">
                                        <div class="flex items-center justify-between">
                                            <span class="block text-xs font-extrabold">Medium / Boardroom</span>
                                            <span id="cat-count-MEDIUM" class="px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[10px] font-extrabold">0</span>
                                        </div>
                                        <span class="block text-[11px] text-slate-500 mt-0.5">7 – 12 Orang · Rapat Tim</span>
                                    </button>
                                    <button type="button" onclick="selectRoomCategory('LARGE')" data-cat-pill="LARGE"
                                        class="cat-pill-btn p-3 rounded-2xl text-left border border-slate-200 bg-white text-slate-700 hover:border-indigo-400 transition cursor-pointer">
                                        <div class="flex items-center justify-between">
                                            <span class="block text-xs font-extrabold">Large / Conference</span>
                                            <span id="cat-count-LARGE" class="px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[10px] font-extrabold">0</span>
                                        </div>
                                        <span class="block text-[11px] text-slate-500 mt-0.5">&gt; 12 Orang · Workshop</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- 1C. Katalog Kartu Ruangan (Pemilih Utama) -->
                        <div class="space-y-3">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div>
                                    <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">3. Pilih Ruangan dari Katalog</h3>
                                    <p id="catalog-summary-text" class="text-xs text-slate-500">Klik kartu ruangan untuk melihat simulasi kursi atau langsung klik "Pilih &amp; Lanjut".</p>
                                </div>
                            </div>
                            <div id="rooms-catalog-grid" class="grid grid-cols-1 md:grid-cols-2 gap-4"></div>
                        </div>

                        <!-- 1D. Visualisasi Denah Interaktif untuk Ruangan yang Sedang Dipilih -->
                        <div id="bf-room-layout-card" class="rounded-2xl border border-indigo-200/80 bg-gradient-to-b from-slate-900 to-indigo-950 text-white p-4 space-y-3 shadow-sm">
                            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-white/10 pb-2.5">
                                <div>
                                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-indigo-300 block">Detail Denah &amp; Simulasi Kursi Ruangan Terpilih</span>
                                    <h3 id="bf-layout-room-title" class="text-sm font-extrabold text-white">Memuat denah ruang...</h3>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span id="bf-layout-dim-badge" class="px-2 py-0.5 rounded-md bg-indigo-500/25 border border-indigo-400/30 font-mono-code text-[11px] font-bold text-indigo-200">-</span>
                                    <span id="bf-layout-type-badge" class="px-2 py-0.5 rounded-md bg-emerald-500/20 border border-emerald-400/30 text-[10px] font-extrabold text-emerald-300">-</span>
                                </div>
                            </div>
                            <div id="bf-layout-svg-container" class="w-full bg-slate-950/60 rounded-xl border border-white/10 p-2.5 flex items-center justify-center min-h-[175px]"></div>
                            <div class="flex flex-wrap items-center justify-between gap-2 text-[11px] text-slate-300 pt-0.5">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 inline-block"></span>
                                        <span>Kursi Terisi (<strong id="bf-layout-occupied-count" class="text-white">0</strong>)</span>
                                    </span>
                                    <span class="inline-flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-slate-600 border border-slate-400 inline-block"></span>
                                        <span>Kursi Kosong (<strong id="bf-layout-empty-count" class="text-white">0</strong>)</span>
                                    </span>
                                </div>
                                <span id="bf-layout-area-label" class="font-mono-code text-[11px] text-indigo-300 font-semibold">Luas: - m²</span>
                            </div>
                        </div>

                        <!-- Step 1 Footer Navigation -->
                        <div class="flex items-center justify-end pt-4 border-t border-slate-100">
                            <button type="button" onclick="goToBookingStep(2)"
                                class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-md shadow-indigo-600/20 transition flex items-center gap-2 cursor-pointer">
                                <span>Lanjut Pilih Tanggal &amp; Jam (Langkah 2)</span>
                                <span>&rarr;</span>
                            </button>
                        </div>
                    </div>

                    <!-- ==================== LAYAR LANGKAH 2: PILIH TANGGAL & JAM RAPAT ==================== -->
                    <div id="wizard-panel-2" class="hidden bg-white rounded-3xl border border-slate-200/90 shadow-xs p-6 space-y-6">
                        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4">
                            <div>
                                <span class="text-[11px] font-extrabold uppercase tracking-wider text-indigo-600">Langkah 2 dari 5</span>
                                <h2 class="text-xl font-extrabold text-slate-900">Pilih Tanggal &amp; Jam Rapat (08:00 – 21:00 WIB)</h2>
                                <p class="text-xs text-slate-500 mt-0.5">Pilih tanggal pemakaian dan klik slot waktu berwarna hijau yang masih kosong.</p>
                            </div>
                            <span id="avail-date-badge" class="px-3 py-1 rounded-xl bg-indigo-50 text-indigo-800 border border-indigo-200/70 text-xs font-bold"></span>
                        </div>

                        <!-- 2A. Pilihan Tanggal & Jam -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-4">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Tanggal Pemakaian (WIB)</label>
                                    <span id="bf-date-human-label" class="text-xs font-bold text-indigo-700">Hari Ini</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <button type="button" onclick="setBookingQuickDate(0)" id="bf-qdate-0"
                                        class="px-3 py-1.5 rounded-lg text-xs font-bold bg-indigo-600 text-white transition cursor-pointer">Hari Ini</button>
                                    <button type="button" onclick="setBookingQuickDate(1)" id="bf-qdate-1"
                                        class="px-3 py-1.5 rounded-lg text-xs font-bold bg-white text-slate-700 border border-slate-200 hover:bg-slate-100 transition cursor-pointer">Besok</button>
                                    <button type="button" onclick="setBookingQuickDate(2)" id="bf-qdate-2"
                                        class="px-3 py-1.5 rounded-lg text-xs font-bold bg-white text-slate-700 border border-slate-200 hover:bg-slate-100 transition cursor-pointer">Lusa</button>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Pilih Tanggal</label>
                                    <input type="date" id="bf-date" required onchange="onBookingRoomOrDateChange()"
                                        class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm font-semibold text-slate-900 focus:border-indigo-600 focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jam Mulai</label>
                                    <select id="bf-start" required onchange="onTimeSelectionChange()"
                                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm font-mono-code font-bold text-slate-900 focus:border-indigo-600 focus:outline-none">
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jam Selesai</label>
                                    <select id="bf-end" required onchange="onTimeSelectionChange()"
                                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm font-mono-code font-bold text-slate-900 focus:border-indigo-600 focus:outline-none">
                                    </select>
                                </div>
                            </div>

                            <!-- Pintasan Durasi -->
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="text-xs font-bold text-slate-600">Pintasan Durasi dari Jam Mulai:</span>
                                    <span class="text-xs text-slate-400">Minimal 1 jam · Maksimal 8 jam</span>
                                </div>
                                <div class="grid grid-cols-5 gap-2">
                                    <button type="button" onclick="setBookingDurationHours(1)" data-dur-btn="60"
                                        class="dur-quick-btn py-2 px-2 rounded-xl text-xs font-bold border border-slate-200 bg-white text-slate-700 hover:border-indigo-400 transition cursor-pointer">1 Jam</button>
                                    <button type="button" onclick="setBookingDurationHours(2)" data-dur-btn="120"
                                        class="dur-quick-btn py-2 px-2 rounded-xl text-xs font-bold border border-indigo-600 bg-indigo-600 text-white transition cursor-pointer">2 Jam</button>
                                    <button type="button" onclick="setBookingDurationHours(3)" data-dur-btn="180"
                                        class="dur-quick-btn py-2 px-2 rounded-xl text-xs font-bold border border-slate-200 bg-white text-slate-700 hover:border-indigo-400 transition cursor-pointer">3 Jam</button>
                                    <button type="button" onclick="setBookingDurationHours(4)" data-dur-btn="240"
                                        class="dur-quick-btn py-2 px-2 rounded-xl text-xs font-bold border border-slate-200 bg-white text-slate-700 hover:border-indigo-400 transition cursor-pointer">4 Jam</button>
                                    <button type="button" onclick="setBookingDurationHours(8)" data-dur-btn="480"
                                        class="dur-quick-btn py-2 px-2 rounded-xl text-xs font-bold border border-slate-200 bg-white text-slate-700 hover:border-indigo-400 transition cursor-pointer">8 Jam</button>
                                </div>
                            </div>
                        </div>

                        <!-- 2B. Peta Ketersediaan Slot 30 Menit -->
                        <div class="space-y-3">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div>
                                    <p class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">Peta Slot Ketersediaan Ruang (Klik Slot untuk Memilih Jam Mulai)</p>
                                    <p class="text-[11px] text-slate-500">Slot berwarna <strong class="text-indigo-700">Biru Indigo</strong> adalah rentang waktu yang sedang Anda pilih.</p>
                                </div>
                                <div class="flex flex-wrap items-center gap-3 text-xs">
                                    <span class="inline-flex items-center gap-1.5 text-indigo-700 font-bold"><span class="w-3 h-3 rounded-sm bg-indigo-600 inline-block"></span> Dipilih</span>
                                    <span class="inline-flex items-center gap-1.5 text-emerald-700 font-semibold"><span class="w-3 h-3 rounded-sm bg-emerald-500 inline-block"></span> Kosong</span>
                                    <span class="inline-flex items-center gap-1.5 text-rose-700 font-semibold"><span class="w-3 h-3 rounded-sm bg-rose-500 inline-block"></span> Terisi</span>
                                </div>
                            </div>
                            <div id="avail-slots-grid" class="grid grid-cols-3 sm:grid-cols-6 md:grid-cols-7 gap-2"></div>
                        </div>

                        <!-- 2C. Ringkasan Rentang Kosong & Terisi -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-200/70">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-800 mb-2 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    <span>Rentang Waktu Kosong (Klik untuk Pakai)</span>
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

                        <!-- Step 2 Footer Navigation -->
                        <div class="flex flex-wrap items-center justify-between gap-3 pt-4 border-t border-slate-100">
                            <button type="button" onclick="goToBookingStep(1)"
                                class="px-5 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm transition cursor-pointer">
                                &larr; Kembali Pilih Ruang
                            </button>
                            <button type="button" onclick="goToBookingStep(3)"
                                class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-md shadow-indigo-600/20 transition flex items-center gap-2 cursor-pointer">
                                <span>Lanjut Pilih Opsi Tambahan (Langkah 3)</span>
                                <span>&rarr;</span>
                            </button>
                        </div>
                    </div>

                    <!-- ==================== LAYAR LANGKAH 3: PILIH OPSI TAMBAHAN KEBUTUHAN ==================== -->
                    <div id="wizard-panel-3" class="hidden bg-white rounded-3xl border border-slate-200/90 shadow-xs p-6 space-y-6">
                        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4">
                            <div>
                                <span class="text-[11px] font-extrabold uppercase tracking-wider text-indigo-600">Langkah 3 dari 5 · Opsional</span>
                                <h2 class="text-xl font-extrabold text-slate-900">Pilih Opsi Tambahan Kebutuhan Rapat</h2>
                                <p class="text-xs text-slate-500 mt-0.5">Tambahkan konsumsi (*coffee break* / makan siang) atau perangkat konferensi tambahan. Langkah ini opsional.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span id="bf-addons-count-badge" class="text-xs font-bold px-3 py-1 rounded-xl bg-indigo-50 text-indigo-700 border border-indigo-200/60">0 dipilih</span>
                                <button type="button" id="bf-addons-reset-btn" onclick="clearSelectedAddons()" class="hidden text-xs font-bold text-rose-600 hover:underline cursor-pointer">Reset Pilihan</button>
                            </div>
                        </div>

                        <div id="bf-addons-list" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <p class="text-xs text-slate-400">Memuat daftar fasilitas tambahan...</p>
                        </div>

                        <!-- Step 3 Footer Navigation -->
                        <div class="flex flex-wrap items-center justify-between gap-3 pt-4 border-t border-slate-100">
                            <button type="button" onclick="goToBookingStep(2)"
                                class="px-5 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm transition cursor-pointer">
                                &larr; Kembali ke Tanggal &amp; Jam
                            </button>
                            <button type="button" onclick="goToBookingStep(4)"
                                class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-md shadow-indigo-600/20 transition flex items-center gap-2 cursor-pointer">
                                <span>Lanjut Isi Data Diri (Langkah 4)</span>
                                <span>&rarr;</span>
                            </button>
                        </div>
                    </div>

                    <!-- ==================== LAYAR LANGKAH 4: ISI DATA DIRI PEMESAN ==================== -->
                    <div id="wizard-panel-4" class="hidden bg-white rounded-3xl border border-slate-200/90 shadow-xs p-6 space-y-6">
                        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4">
                            <div>
                                <span class="text-[11px] font-extrabold uppercase tracking-wider text-indigo-600">Langkah 4 dari 5</span>
                                <h2 class="text-xl font-extrabold text-slate-900">Isi Data Diri Pemesan</h2>
                                <p class="text-xs text-slate-500 mt-0.5">Masukkan nama lengkap atau nama tim beserta kontak aktif untuk penerbitan kode booking &amp; bukti pembayaran QRIS.</p>
                            </div>
                            @if (session()->has('customer_id'))
                                <span class="px-3 py-1 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold">
                                    ✓ Terisi Otomatis dari Akun ({{ session('customer_email') }})
                                </span>
                            @endif
                        </div>

                        @if (! session()->has('customer_id'))
                            <div class="p-4 rounded-2xl bg-indigo-50/70 border border-indigo-200/80 flex flex-wrap items-center justify-between gap-3 text-xs">
                                <div class="space-y-0.5">
                                    <p class="font-extrabold text-indigo-950">Punya Akun Penyewa RuangKita?</p>
                                    <p class="text-slate-600">Masuk agar nama &amp; kontak terisi otomatis dan pesanan tersimpan di riwayat akun Anda.</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('customer.login') }}" class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold transition">Masuk</a>
                                    <a href="{{ route('customer.register') }}" class="px-3 py-1.5 rounded-xl bg-white hover:bg-slate-100 text-indigo-700 border border-indigo-200 font-bold transition">Daftar</a>
                                </div>
                            </div>
                        @endif

                        <div class="space-y-4 max-w-xl">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Lengkap Pemesan / Nama Tim</label>
                                <input type="text" id="bf-name" value="{{ session('customer_name', '') }}" required oninput="updateWizardReviewSummary()" placeholder="Contoh: Budi Santoso / Tim Produk RuangKita"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-900 focus:border-indigo-600 focus:outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kontak Pemesan (No. WhatsApp / Alamat Email)</label>
                                <input type="text" id="bf-contact" value="{{ session('customer_phone', session('customer_email', '')) }}" required oninput="updateWizardReviewSummary()" placeholder="Contoh: 081234567890 atau budi@perusahaan.com"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-900 focus:border-indigo-600 focus:outline-none">
                            </div>
                        </div>

                        <!-- Step 4 Footer Navigation -->
                        <div class="flex flex-wrap items-center justify-between gap-3 pt-4 border-t border-slate-100">
                            <button type="button" onclick="goToBookingStep(3)"
                                class="px-5 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm transition cursor-pointer">
                                &larr; Kembali ke Opsi Tambahan
                            </button>
                            <button type="button" onclick="goToBookingStep(5)"
                                class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-md shadow-indigo-600/20 transition flex items-center gap-2 cursor-pointer">
                                <span>Lanjut ke Pembayaran QRIS (Langkah 5)</span>
                                <span>&rarr;</span>
                            </button>
                        </div>
                    </div>

                    <!-- ==================== LAYAR LANGKAH 5: KONFIRMASI & PEMBAYARAN QRIS ==================== -->
                    <div id="wizard-panel-5" class="hidden bg-white rounded-3xl border border-slate-200/90 shadow-xs p-6 space-y-6">
                        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4">
                            <div>
                                <span class="text-[11px] font-extrabold uppercase tracking-wider text-emerald-600">Langkah 5 dari 5 · Tahap Akhir</span>
                                <h2 class="text-xl font-extrabold text-slate-900">Periksa Ringkasan &amp; Lanjut Pembayaran QRIS</h2>
                                <p class="text-xs text-slate-500 mt-0.5">Pastikan seluruh rincian ruangan, jadwal, opsi tambahan, dan data diri di bawah sudah benar sebelum membuat kode QRIS.</p>
                            </div>
                            <span class="px-3 py-1 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-extrabold">Siap Dibayar via QRIS</span>
                        </div>

                        <!-- Detailed Review Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2.5 text-xs">
                                <div class="flex items-center justify-between border-b border-slate-200/70 pb-2">
                                    <span class="font-extrabold text-slate-700 uppercase tracking-wider">1. Ruang &amp; Jadwal</span>
                                    <button type="button" onclick="goToBookingStep(1)" class="text-indigo-600 font-bold hover:underline cursor-pointer">Ubah</button>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Ruang Meeting:</span>
                                    <span id="rev-room-name" class="font-extrabold text-slate-900 text-right">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Tanggal Rapat:</span>
                                    <span id="rev-date" class="font-bold text-indigo-700 text-right">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Jam &amp; Durasi:</span>
                                    <span id="rev-time" class="font-mono-code font-bold text-slate-900 text-right">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Jumlah Peserta:</span>
                                    <span id="rev-participants" class="font-bold text-slate-900 text-right">-</span>
                                </div>
                            </div>

                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2.5 text-xs">
                                <div class="flex items-center justify-between border-b border-slate-200/70 pb-2">
                                    <span class="font-extrabold text-slate-700 uppercase tracking-wider">2. Pemesan &amp; Opsi Tambahan</span>
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="goToBookingStep(3)" class="text-indigo-600 font-bold hover:underline cursor-pointer">Ubah Add-on</button>
                                        <span class="text-slate-300">|</span>
                                        <button type="button" onclick="goToBookingStep(4)" class="text-indigo-600 font-bold hover:underline cursor-pointer">Ubah Data Diri</button>
                                    </div>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Nama Pemesan:</span>
                                    <span id="rev-booker-name" class="font-extrabold text-slate-900 text-right">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Kontak Pemesan:</span>
                                    <span id="rev-booker-contact" class="font-semibold text-slate-800 text-right">-</span>
                                </div>
                                <div class="pt-2 border-t border-slate-200/60 space-y-1.5">
                                    <span class="text-slate-500 font-bold block">Rincian Opsi Tambahan Dipilih:</span>
                                    <div id="rev-addons-list" class="space-y-1 text-slate-800 font-semibold">-</div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Rincian Tagihan Lengkap di Langkah 5 -->
                        <div class="p-4 rounded-2xl bg-gradient-to-br from-indigo-50/80 to-slate-50 border border-indigo-200/80 space-y-2.5 text-xs">
                            <div class="flex items-center justify-between border-b border-indigo-200/60 pb-2">
                                <span class="font-extrabold text-indigo-950 uppercase tracking-wider">3. Rincian Tagihan Pembayaran (Sewa Ruang + Opsi Tambahan)</span>
                                <span class="font-mono-code text-[11px] font-bold text-indigo-700">Metode: QRIS</span>
                            </div>
                            <div id="rev-invoice-lines" class="space-y-1.5"></div>
                            <div class="flex items-center justify-between pt-2 border-t border-indigo-200/80">
                                <span class="text-xs font-extrabold uppercase tracking-wider text-indigo-950">Total Tagihan QRIS:</span>
                                <span id="rev-total-price" class="text-lg font-extrabold font-mono-code text-indigo-700">Rp 0</span>
                            </div>
                        </div>

                        <!-- Step 5 Footer Navigation & Final Submit -->
                        <div class="flex flex-wrap items-center justify-between gap-3 pt-4 border-t border-slate-100">
                            <button type="button" onclick="goToBookingStep(4)"
                                class="px-5 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm transition cursor-pointer">
                                &larr; Kembali ke Data Diri
                            </button>

                            <button type="submit" id="bf-submit-btn"
                                class="px-7 py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-extrabold text-sm shadow-lg shadow-emerald-600/25 transition flex items-center justify-center gap-2 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                </svg>
                                <span>Buat Pesanan &amp; Tampilkan QRIS Sekarang</span>
                            </button>
                        </div>
                    </div>

                </div>

                <!-- RIGHT 4 COLS: STICKY LIVE RINGKASAN PESANAN -->
                <div class="lg:col-span-4 lg:sticky lg:top-24 bg-white rounded-3xl border border-slate-200/90 shadow-xs p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-indigo-600">Ringkasan Langsung</span>
                            <h3 class="text-base font-extrabold text-slate-900">Detail Pilihan Anda</h3>
                        </div>
                        <span id="wiz-side-step-badge" class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-bold">Tahap 1/5</span>
                    </div>

                    <div class="space-y-2.5 text-xs">
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/70 space-y-1.5">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Ruangan:</span>
                                <span id="side-room-name" class="font-extrabold text-slate-900 text-right">-</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Jumlah Peserta:</span>
                                <span id="side-participants" class="font-bold text-slate-800 text-right">4 orang</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Tanggal (WIB):</span>
                                <span id="side-date" class="font-bold text-indigo-700 text-right">-</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Durasi Rapat:</span>
                                <span id="bf-duration-label" class="font-bold text-slate-800 text-right">2 Jam</span>
                            </div>
                            <div class="pt-2 border-t border-slate-200/70 space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 font-semibold">Opsi Tambahan Dipilih:</span>
                                    <button type="button" onclick="goToBookingStep(3)" class="text-[11px] font-bold text-indigo-600 hover:underline cursor-pointer">Pilih/Ubah</button>
                                </div>
                                <div id="side-addons-summary" class="space-y-1 text-[11px] text-slate-700">
                                    <span class="text-slate-400 italic">Belum ada opsi tambahan</span>
                                </div>
                            </div>
                        </div>

                        <!-- Server Price Estimation Box -->
                        <div class="rounded-2xl bg-gradient-to-br from-indigo-50/90 to-slate-50 p-4 border border-indigo-100 space-y-2">
                            <div class="flex items-center justify-between text-xs text-slate-600">
                                <span id="side-room-price-label">Biaya Sewa Ruang:</span>
                                <span id="bf-room-price-preview" class="font-bold text-slate-800 font-mono-code">Rp 0</span>
                            </div>
                            <div class="space-y-1">
                                <div class="flex items-center justify-between text-xs text-slate-600">
                                    <span>Biaya Opsi Tambahan:</span>
                                    <span id="bf-addons-price-preview" class="font-bold text-indigo-700 font-mono-code">Rp 0</span>
                                </div>
                                <div id="side-addons-price-breakdown" class="space-y-1 pl-2 border-l-2 border-indigo-200/80 text-[11px] text-slate-600"></div>
                            </div>
                            <div class="flex items-center justify-between pt-2 border-t border-indigo-200/60">
                                <span class="text-xs font-extrabold uppercase tracking-wider text-indigo-950">Estimasi Total:</span>
                                <span id="bf-price-preview" class="text-lg font-extrabold text-indigo-700 font-mono-code">Rp 0</span>
                            </div>
                            <p id="bf-rule-hint" class="text-[11px] text-slate-500 leading-tight pt-1">
                                * Total harga dihitung otomatis oleh sistem berdasarkan sewa ruang + fasilitas tambahan.
                            </p>
                        </div>
                    </div>
                </div>

            </form>
        </section>

        <!-- TAB 2: KALENDER PEMAKAIAN (FR-11) -->
        <section id="tab-calendar" class="hidden space-y-6">
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs p-6 space-y-5">
                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-4">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">Jadwal Pemakaian Ruang</span>
                        <h2 class="text-xl font-extrabold text-slate-900">Kalender Pemakaian Ruang (Harian &amp; Mingguan)</h2>
                        <p id="cal-active-date-label" class="text-xs font-semibold text-slate-500 mt-0.5"></p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2.5">
                        <select id="cal-room-filter" onchange="loadCalendar()"
                            class="rounded-xl border border-slate-300 bg-slate-50 px-3.5 py-2 text-xs font-semibold text-slate-800 focus:border-indigo-600 focus:outline-none">
                            <option value="">Semua Ruang</option>
                        </select>

                        <!-- Date Navigation Controls -->
                        <div class="inline-flex items-center rounded-xl border border-slate-300 bg-slate-50 p-0.5">
                            <button type="button" onclick="shiftCalendarDate(-1)" title="Hari Sebelumnya"
                                class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-slate-700 hover:bg-white transition cursor-pointer">&larr;</button>
                            <input type="date" id="cal-date" onchange="loadCalendar()"
                                class="bg-transparent px-2 py-1 text-xs font-bold text-slate-800 focus:outline-none">
                            <button type="button" onclick="shiftCalendarDate(1)" title="Hari Berikutnya"
                                class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-slate-700 hover:bg-white transition cursor-pointer">&rarr;</button>
                        </div>

                        <button type="button" onclick="resetCalendarToToday()"
                            class="px-3 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 text-xs font-bold transition cursor-pointer">
                            Hari Ini
                        </button>

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
                        <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">Kelola Pesanan Akun Anda ({{ session('customer_name') }})</span>
                        <h2 class="text-xl font-extrabold text-slate-900">Pesanan Saya &amp; Pembatalan</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Hanya menampilkan pesanan milik akun Anda. Pembatalan &lt; 2 jam sebelum jam mulai dikenai denda 50%; &ge; 2 jam bebas denda (Rp 0).</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2.5">
                        <span class="px-3 py-2 rounded-xl bg-indigo-50 border border-indigo-200 text-xs font-extrabold text-indigo-800">
                            🔒 Akun: {{ session('customer_name') }}
                        </span>

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

                        <button type="button" onclick="resetBookingsFilter()"
                            class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition cursor-pointer">
                            Reset Filter
                        </button>
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

    <!-- QRIS Payment Gateway & Official Nota / Invoice Modal -->
    <div id="qris-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-950/75 backdrop-blur-xs p-4 overflow-y-auto">
        <div id="qris-modal-card" class="bg-white rounded-3xl max-w-2xl w-full border border-slate-200 shadow-2xl overflow-hidden my-6">
            <!-- Top Action Bar (Hidden on Print) -->
            <div class="no-print bg-slate-900 px-6 py-3.5 text-white flex items-center justify-between border-b border-slate-800">
                <div class="flex items-center gap-2.5">
                    <span class="px-2.5 py-0.5 rounded-md bg-indigo-500/20 border border-indigo-400/30 text-indigo-300 text-[10px] font-extrabold uppercase tracking-wider">Nota Resmi &amp; Bukti Reservasi</span>
                    <span class="text-xs text-slate-400 hidden sm:inline">Siap dicetak atau disimpan sebagai PDF</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="printOfficialInvoice()"
                        class="px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold flex items-center gap-1.5 transition cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <span>Cetak Nota / PDF</span>
                    </button>
                    <button type="button" onclick="closeQrisModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white font-bold cursor-pointer">✕</button>
                </div>
            </div>

            <!-- Printable Official Invoice / Nota Sheet -->
            <div id="printable-invoice-sheet" class="p-6 sm:p-8 space-y-6 bg-white text-slate-900 relative">

                <!-- 1. Kop / Header Nota Perusahaan -->
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 border-b-2 border-slate-900 pb-5">
                    <div class="flex items-start gap-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-600 flex items-center justify-center text-white font-extrabold shadow-sm shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-extrabold tracking-tight text-slate-900 uppercase">RUANGKITA COWORKING SPACE</h2>
                            <p class="text-xs font-medium text-slate-500">Gedung RuangKita Hub Lt. 3, Jl. Jenderal Sudirman, Jakarta</p>
                            <p class="text-[11px] font-mono-code text-slate-500 mt-0.5">NPWP: 09.241.883.1-021.000 • NMID QRIS: ID1026000914300</p>
                        </div>
                    </div>

                    <div class="sm:text-right space-y-1">
                        <div class="inline-flex items-center gap-2">
                            <span class="text-[11px] font-extrabold uppercase tracking-widest text-slate-400">INVOICE / NOTA</span>
                            <span id="qm-status-badge" class="px-2.5 py-0.5 rounded-md bg-amber-400 text-slate-950 text-[10px] font-extrabold uppercase">MENUNGGU PEMBAYARAN</span>
                        </div>
                        <p id="qm-invoice-number" class="text-sm font-mono-code font-extrabold text-indigo-700">INV/RK/20261124/0001</p>
                        <p class="text-xs font-mono-code text-slate-600">Kode Booking: <strong id="qm-booking-code" class="text-slate-900">RK-20261125-0001</strong></p>
                        <p id="qm-issued-at" class="text-[11px] text-slate-400">Tanggal Cetak: -</p>
                    </div>
                </div>

                <!-- 2. Grid Informasi Penyewa & Detail Jadwal Ruangan -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/90 space-y-1.5 text-xs">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Ditagihkan Kepada (Penyewa)</span>
                        <p id="qm-booker-name" class="text-sm font-extrabold text-slate-900">-</p>
                        <p id="qm-booker-contact" class="text-xs font-mono-code text-slate-600">-</p>
                        <div class="pt-1.5 mt-1.5 border-t border-slate-200/70 flex items-center justify-between text-[11px]">
                            <span class="text-slate-500">Status Reservasi:</span>
                            <span id="qm-booking-status-text" class="font-extrabold text-emerald-700">CONFIRMED</span>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/90 space-y-1.5 text-xs">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Detail Ruang &amp; Jadwal Rapat</span>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Ruang Meeting:</span>
                            <span id="qm-room-info" class="font-extrabold text-slate-900 text-right">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Tanggal Rapat:</span>
                            <span id="qm-date-info" class="font-bold text-slate-900 text-right">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Waktu (WIB):</span>
                            <span id="qm-schedule-info" class="font-mono-code font-bold text-indigo-700 text-right">-</span>
                        </div>
                    </div>
                </div>

                <!-- 3. Tabel Nota Rincian Layanan & Biaya -->
                <div class="rounded-2xl border border-slate-200 overflow-hidden">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-900 text-white text-[10px] font-extrabold uppercase tracking-wider">
                                <th class="py-2.5 px-3.5 w-10">No</th>
                                <th class="py-2.5 px-3.5">Deskripsi Layanan / Fasilitas</th>
                                <th class="py-2.5 px-3.5">Rincian Perhitungan</th>
                                <th class="py-2.5 px-3.5 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="qm-invoice-items-tbody" class="divide-y divide-slate-200/80 bg-white">
                        </tbody>
                        <tfoot class="bg-slate-50 border-t-2 border-slate-300 text-xs">
                            <tr>
                                <td colspan="3" class="py-2 px-3.5 text-right font-semibold text-slate-500">Subtotal Sewa Ruang:</td>
                                <td id="qm-room-price" class="py-2 px-3.5 text-right font-mono-code font-bold text-slate-800">Rp 0</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="py-2 px-3.5 text-right font-semibold text-slate-500">Subtotal Opsi Tambahan (Add-ons):</td>
                                <td id="qm-addons-subtotal" class="py-2 px-3.5 text-right font-mono-code font-bold text-indigo-700">Rp 0</td>
                            </tr>
                            <tr id="qm-penalty-row" class="hidden bg-amber-50/80">
                                <td colspan="3" class="py-2 px-3.5 text-right font-bold text-amber-800">Denda Pembatalan (&lt; 2 Jam Sebelum Mulai · 50%):</td>
                                <td id="qm-penalty-amount" class="py-2 px-3.5 text-right font-mono-code font-extrabold text-amber-800">Rp 0</td>
                            </tr>
                            <tr class="border-t border-slate-300 bg-indigo-50/60">
                                <td colspan="3" class="py-3 px-3.5 text-right font-extrabold uppercase tracking-wider text-indigo-950 text-sm">TOTAL TAGIHAN PEMBAYARAN:</td>
                                <td id="qm-total-amount" class="py-3 px-3.5 text-right font-mono-code font-extrabold text-indigo-700 text-base">Rp 0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Hidden legacy container kept for compatibility -->
                <div id="qm-addons-breakdown" class="hidden"></div>

                <!-- 4. Bagian QRIS & Pengesahan / Stempel Lunas -->
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-center rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/70 p-4">
                    <!-- QR Code Column (5 cols) -->
                    <div class="sm:col-span-5 flex flex-col items-center text-center space-y-2">
                        <div class="flex items-center gap-1.5">
                            <span class="px-2 py-0.5 rounded bg-rose-600 text-white text-[10px] font-extrabold tracking-wider">QRIS</span>
                            <span class="text-[10px] font-extrabold uppercase text-slate-600">Standar Pembayaran Nasional</span>
                        </div>
                        <div id="qm-qr-container" class="w-40 h-40 bg-white p-2.5 rounded-2xl border border-slate-200 shadow-2xs flex items-center justify-center"></div>
                        <p id="qm-reference" class="text-[10px] font-mono-code text-slate-500">Ref: -</p>
                    </div>

                    <!-- Payment Verification & Terms Column (7 cols) -->
                    <div class="sm:col-span-7 space-y-3 text-xs">
                        <!-- Paid Stamp Overlay -->
                        <div id="qm-paid-banner" class="hidden rounded-2xl bg-emerald-50 border-2 border-emerald-500/80 p-3.5 text-emerald-900 space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-extrabold uppercase tracking-wider text-emerald-800">✓ PEMBAYARAN QRIS LUNAS (PAID)</span>
                                <span class="px-2 py-0.5 rounded bg-emerald-600 text-white font-mono-code text-[10px] font-bold">VERIFIED</span>
                            </div>
                            <p id="qm-paid-timestamp" class="text-[11px] font-mono-code text-emerald-700">Waktu Lunas: -</p>
                            <p class="text-[11px] text-emerald-800">Nota ini merupakan bukti pembayaran yang sah untuk penggunaan ruang rapat RuangKita.</p>
                        </div>

                        <!-- Unpaid Instruction Banner -->
                        <div id="qm-unpaid-banner" class="rounded-2xl bg-amber-50 border border-amber-200 p-3.5 text-amber-900 space-y-1">
                            <p class="font-extrabold text-xs">Menunggu Pembayaran QRIS</p>
                            <p class="text-[11px] leading-relaxed">Scan kode QRIS di samping menggunakan aplikasi m-Banking atau e-Wallet (GoPay, OVO, DANA, ShopeePay, LinkAja), atau klik tombol simulasi bayar di bawah.</p>
                        </div>

                        <!-- Ketentuan & Tanda Terima Resmi -->
                        <div class="p-3 rounded-xl bg-white border border-slate-200/80 text-[11px] text-slate-500 space-y-1 leading-relaxed">
                            <p class="font-bold text-slate-700">Ketentuan Reservasi &amp; Pembatalan (BR-04 &amp; BR-05):</p>
                            <p>• Harap hadir 10 menit sebelum jadwal mulai dan tunjukkan kode booking ini kepada resepsionis.</p>
                            <p>• Pembatalan &ge; 2 jam sebelum jadwal mulai bebas denda (Rp 0). Pembatalan &lt; 2 jam dikenakan denda 50%.</p>
                        </div>
                    </div>
                </div>

                <!-- 5. Action Buttons (Hidden on Print) -->
                <div class="no-print space-y-2.5 pt-2 border-t border-slate-100">
                    <button type="button" id="qm-pay-btn" onclick="confirmQrisPaymentFromModal()"
                        class="w-full py-3.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-sm shadow-md shadow-emerald-600/20 transition flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Simulasikan Scan &amp; Bayar QRIS Sekarang</span>
                    </button>

                    <div class="flex flex-wrap items-center gap-2.5">
                        <button type="button" onclick="printOfficialInvoice()"
                            class="flex-1 py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-xs transition flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            <span>Cetak Nota / Simpan PDF</span>
                        </button>
                        <button type="button" onclick="closeQrisModal()"
                            class="py-3 px-5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer">
                            Tutup Jendela
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
            selectedCategory: 'ALL',
            bookingStep: 1,
            loggedInCustomer: {
                id: @json(session('customer_id')),
                name: @json(session('customer_name', '')),
                email: @json(session('customer_email', '')),
                phone: @json(session('customer_phone', '')),
            },
            additionalFacilities: [],
            selectedAddons: {}, // { [facilityId]: quantity }
            bookings: [],
            calendarView: 'day',
            lastAvailabilityData: null,
        };

        function validateStepBeforeAdvance(step) {
            const roomId = document.getElementById('bf-room')?.value;
            const room = state.rooms.find(r => String(r.id) === String(roomId));
            const participants = Number(document.getElementById('bf-participants')?.value || 0);
            const date = document.getElementById('bf-date')?.value;
            const start = document.getElementById('bf-start')?.value;
            const end = document.getElementById('bf-end')?.value;

            if (step === 1) {
                if (!room) {
                    showAlert('warning', 'Pilih Ruangan Terlebih Dahulu', 'Silakan pilih salah satu ruang meeting sebelum melanjutkan ke Langkah 2.');
                    return false;
                }
                if (!room.is_active) {
                    showAlert('error', 'Ruangan Tidak Aktif (BR-07)', `${room.name} sedang tidak aktif dan tidak dapat dipesan.`);
                    return false;
                }
                if (participants <= 0 || participants > room.capacity) {
                    showAlert('error', 'Kapasitas Ruangan Terlampaui (BR-02)', `Jumlah peserta (${participants} orang) melebihi kapasitas maksimal ${room.name} (${room.capacity} orang).`);
                    return false;
                }
            }

            if (step === 2) {
                if (!date || !start || !end) {
                    showAlert('warning', 'Lengkapi Tanggal & Jam Rapat', 'Silakan pilih tanggal, jam mulai, dan jam selesai.');
                    return false;
                }
                const startMins = timeToMinutes(start);
                const endMins = timeToMinutes(end);
                const diffMins = endMins - startMins;

                if (diffMins <= 0) {
                    showAlert('error', 'Urutan Waktu Tidak Valid (BR-06)', 'Jam selesai harus setelah jam mulai.');
                    return false;
                }
                if (startMins < 8 * 60 || endMins > 21 * 60) {
                    showAlert('error', 'Di Luar Jam Operasional (BR-04)', 'Jam pemesanan harus berada dalam jam operasional 08:00 – 21:00 WIB.');
                    return false;
                }
                if (diffMins < 60 || diffMins > 480) {
                    showAlert('error', 'Durasi Tidak Memenuhi Aturan (BR-03)', 'Durasi setiap pemesanan minimal 1 jam (60 menit) dan maksimal 8 jam (480 menit).');
                    return false;
                }

                // Check overlap against loaded booked_slots on client side for immediate feedback
                if (state.lastAvailabilityData && Array.isArray(state.lastAvailabilityData.booked_slots)) {
                    const conflict = state.lastAvailabilityData.booked_slots.find(b => {
                        const bStart = timeToMinutes(b.start_time);
                        const bEnd = timeToMinutes(b.end_time);
                        return startMins < bEnd && endMins > bStart;
                    });
                    if (conflict) {
                        showAlert(
                            'error',
                            'Jadwal Bentrok / Overlap (BR-01)',
                            `Rentang ${start}–${end} WIB beririsan dengan jadwal yang sudah terisi (${conflict.start_time}–${conflict.end_time} WIB atas nama ${conflict.booker_name}). Silakan pilih slot hijau yang kosong.`
                        );
                        return false;
                    }
                }
            }

            if (step === 4) {
                const name = (document.getElementById('bf-name')?.value || '').trim();
                const contact = (document.getElementById('bf-contact')?.value || '').trim();
                if (!name) {
                    showAlert('warning', 'Nama Pemesan Wajib Diisi', 'Silakan isi nama lengkap pemesan atau nama tim Anda.');
                    document.getElementById('bf-name')?.focus();
                    return false;
                }
                if (!contact) {
                    showAlert('warning', 'Kontak Pemesan Wajib Diisi', 'Silakan isi nomor WhatsApp atau alamat email aktif.');
                    document.getElementById('bf-contact')?.focus();
                    return false;
                }
            }

            return true;
        }

        function goToBookingStep(targetStep) {
            const target = Math.max(1, Math.min(5, Number(targetStep || 1)));

            // Validate every intermediate step when moving forward
            if (target > state.bookingStep) {
                for (let s = state.bookingStep; s < target; s++) {
                    if (!validateStepBeforeAdvance(s)) {
                        return;
                    }
                }
            }

            state.bookingStep = target;

            for (let i = 1; i <= 5; i++) {
                const panel = document.getElementById(`wizard-panel-${i}`);
                const navBtn = document.getElementById(`wiz-nav-${i}`);
                const circle = document.getElementById(`wiz-circle-${i}`);

                if (panel) {
                    panel.classList.toggle('hidden', i !== target);
                }

                if (navBtn && circle) {
                    const labelSpans = navBtn.querySelectorAll('span');
                    const stepSmallLabel = labelSpans[1];
                    const stepTitleLabel = labelSpans[2];

                    if (i === target) {
                        navBtn.className = `${i === 5 ? 'col-span-2 sm:col-span-1 ' : ''}flex items-center gap-2.5 p-2.5 rounded-2xl border-2 border-indigo-600 bg-indigo-50/80 text-left shadow-2xs transition cursor-pointer`;
                        circle.className = 'w-7 h-7 rounded-full bg-indigo-600 text-white text-xs font-extrabold flex items-center justify-center shrink-0';
                        circle.textContent = String(i);
                        if (stepSmallLabel) stepSmallLabel.className = 'block text-[10px] font-bold uppercase tracking-wider text-indigo-600';
                        if (stepTitleLabel) stepTitleLabel.className = 'block text-xs font-extrabold text-slate-900 truncate';
                    } else if (i < target) {
                        navBtn.className = `${i === 5 ? 'col-span-2 sm:col-span-1 ' : ''}flex items-center gap-2.5 p-2.5 rounded-2xl border border-emerald-300 bg-emerald-50/50 hover:bg-emerald-50 text-left transition cursor-pointer`;
                        circle.className = 'w-7 h-7 rounded-full bg-emerald-600 text-white text-xs font-extrabold flex items-center justify-center shrink-0';
                        circle.textContent = '✓';
                        if (stepSmallLabel) stepSmallLabel.className = 'block text-[10px] font-bold uppercase tracking-wider text-emerald-700';
                        if (stepTitleLabel) stepTitleLabel.className = 'block text-xs font-extrabold text-emerald-950 truncate';
                    } else {
                        navBtn.className = `${i === 5 ? 'col-span-2 sm:col-span-1 ' : ''}flex items-center gap-2.5 p-2.5 rounded-2xl border border-slate-200 bg-slate-50/60 hover:bg-slate-100 text-left transition cursor-pointer`;
                        circle.className = 'w-7 h-7 rounded-full bg-slate-200 text-slate-700 text-xs font-extrabold flex items-center justify-center shrink-0';
                        circle.textContent = String(i);
                        if (stepSmallLabel) stepSmallLabel.className = 'block text-[10px] font-bold uppercase tracking-wider text-slate-400';
                        if (stepTitleLabel) stepTitleLabel.className = 'block text-xs font-extrabold text-slate-700 truncate';
                    }
                }
            }

            const sideBadge = document.getElementById('wiz-side-step-badge');
            if (sideBadge) {
                sideBadge.textContent = `Tahap ${target}/5`;
            }

            updateWizardReviewSummary();
            window.scrollTo({ top: 160, behavior: 'smooth' });
        }

        function getSelectedAddonsDetailedList(durationMinutes, participants) {
            const hours = Math.max(0, durationMinutes / 60);
            const items = [];

            Object.entries(state.selectedAddons).forEach(([idStr, qty]) => {
                const addon = state.additionalFacilities.find(a => String(a.id) === String(idStr));
                if (!addon) return;
                const q = Math.max(1, Number(qty || 1));
                let subtotal = 0;
                let formulaText = '';

                if (addon.pricing_type === 'PER_HOUR') {
                    subtotal = Math.round(addon.price * hours * q);
                    formulaText = `${q} unit × ${hours} jam × ${formatRupiah(addon.price)}/jam`;
                } else if (addon.pricing_type === 'PER_PARTICIPANT') {
                    subtotal = Math.round(addon.price * Math.max(0, participants) * q);
                    formulaText = `${participants} peserta × ${formatRupiah(addon.price)}/peserta`;
                } else {
                    subtotal = Math.round(addon.price * q);
                    formulaText = `${q} paket × ${formatRupiah(addon.price)}/booking`;
                }

                items.push({
                    id: addon.id,
                    name: addon.name,
                    quantity: q,
                    pricing_type: addon.pricing_type,
                    unit_price: addon.price,
                    formula: formulaText,
                    subtotal,
                });
            });

            return items;
        }

        function updateWizardReviewSummary() {
            const roomId = document.getElementById('bf-room')?.value;
            const room = state.rooms.find(r => String(r.id) === String(roomId));
            const date = document.getElementById('bf-date')?.value || getTodayWibString();
            const start = document.getElementById('bf-start')?.value || '09:00';
            const end = document.getElementById('bf-end')?.value || '11:00';
            const participants = Number(document.getElementById('bf-participants')?.value || 0);
            const bookerName = (document.getElementById('bf-name')?.value || '').trim();
            const bookerContact = (document.getElementById('bf-contact')?.value || '').trim();
            const diffMins = Math.max(0, timeToMinutes(end) - timeToMinutes(start));
            const hours = diffMins / 60;

            const roomTotal = room && diffMins > 0 ? Math.max(0, Math.round(hours * room.price_per_hour)) : 0;
            const addonItems = getSelectedAddonsDetailedList(diffMins, participants);
            const addonsTotal = addonItems.reduce((sum, item) => sum + item.subtotal, 0);
            const grandTotal = roomTotal + addonsTotal;

            // Update sticky sidebar
            const sideRoom = document.getElementById('side-room-name');
            const sidePart = document.getElementById('side-participants');
            const sideDate = document.getElementById('side-date');
            const sideRoomPriceLabel = document.getElementById('side-room-price-label');
            const sideAddonsSummary = document.getElementById('side-addons-summary');
            const sideAddonsBreakdown = document.getElementById('side-addons-price-breakdown');

            if (sideRoom) sideRoom.textContent = room ? `${room.name} (Maks ${room.capacity})` : '-';
            if (sidePart) sidePart.textContent = `${participants} orang`;
            if (sideDate) sideDate.textContent = formatFriendlyDateWib(date);
            if (sideRoomPriceLabel) {
                sideRoomPriceLabel.textContent = room && hours > 0
                    ? `Sewa Ruang (${hours} jam × ${formatRupiah(room.price_per_hour)}):`
                    : 'Biaya Sewa Ruang:';
            }

            if (sideAddonsSummary) {
                if (!addonItems.length) {
                    sideAddonsSummary.innerHTML = '<span class="text-slate-400 italic">Tidak ada opsi tambahan dipilih</span>';
                } else {
                    sideAddonsSummary.innerHTML = addonItems.map(item => `
                        <div class="flex items-start justify-between gap-2 bg-white px-2.5 py-1.5 rounded-xl border border-indigo-100">
                            <div class="min-w-0">
                                <span class="font-bold text-indigo-950 block truncate">+ ${item.name}</span>
                                <span class="text-[10px] text-slate-500 block">${item.formula}</span>
                            </div>
                            <span class="font-mono-code font-bold text-indigo-700 shrink-0">${formatRupiah(item.subtotal)}</span>
                        </div>
                    `).join('');
                }
            }

            if (sideAddonsBreakdown) {
                if (!addonItems.length) {
                    sideAddonsBreakdown.innerHTML = '';
                } else {
                    sideAddonsBreakdown.innerHTML = addonItems.map(item => `
                        <div class="flex items-center justify-between gap-2">
                            <span class="truncate">• ${item.name} (${item.pricing_type === 'PER_PARTICIPANT' ? participants + ' org' : 'x' + item.quantity})</span>
                            <span class="font-mono-code font-semibold text-indigo-700 shrink-0">${formatRupiah(item.subtotal)}</span>
                        </div>
                    `).join('');
                }
            }

            // Update Step 5 Review Card
            const revRoom = document.getElementById('rev-room-name');
            const revDate = document.getElementById('rev-date');
            const revTime = document.getElementById('rev-time');
            const revPart = document.getElementById('rev-participants');
            const revName = document.getElementById('rev-booker-name');
            const revContact = document.getElementById('rev-booker-contact');
            const revAddons = document.getElementById('rev-addons-list');
            const revInvoiceLines = document.getElementById('rev-invoice-lines');
            const revTotalPrice = document.getElementById('rev-total-price');

            if (revRoom) revRoom.textContent = room ? `${room.name} (${getRoomCategoryInfo(room).label})` : '-';
            if (revDate) revDate.textContent = formatFriendlyDateWib(date);
            if (revTime) revTime.textContent = `${start} – ${end} WIB (${hours} Jam)`;
            if (revPart) revPart.textContent = `${participants} Peserta`;
            if (revName) revName.textContent = bookerName || '(Belum diisi)';
            if (revContact) revContact.textContent = bookerContact || '(Belum diisi)';

            if (revAddons) {
                if (!addonItems.length) {
                    revAddons.innerHTML = '<span class="text-slate-400 font-normal italic">Tidak ada opsi tambahan dipilih</span>';
                } else {
                    revAddons.innerHTML = addonItems.map(item => `
                        <div class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-xl bg-white border border-indigo-200/80">
                            <div>
                                <span class="font-bold text-indigo-900 block">+ ${item.name}</span>
                                <span class="text-[10px] font-normal text-slate-500">${item.formula}</span>
                            </div>
                            <span class="font-mono-code font-bold text-indigo-700">${formatRupiah(item.subtotal)}</span>
                        </div>
                    `).join('');
                }
            }

            if (revInvoiceLines) {
                const roomLineHtml = room ? `
                    <div class="flex items-center justify-between py-1">
                        <div>
                            <span class="font-bold text-slate-900">Sewa ${room.name}</span>
                            <span class="text-[11px] text-slate-500 block">${hours} jam × ${formatRupiah(room.price_per_hour)} / jam</span>
                        </div>
                        <span class="font-mono-code font-bold text-slate-900">${formatRupiah(roomTotal)}</span>
                    </div>
                ` : '';

                const addonLinesHtml = addonItems.map(item => `
                    <div class="flex items-center justify-between py-1 border-t border-indigo-100/80">
                        <div>
                            <span class="font-bold text-indigo-900">+ ${item.name}</span>
                            <span class="text-[11px] text-slate-500 block">${item.formula}</span>
                        </div>
                        <span class="font-mono-code font-bold text-indigo-700">${formatRupiah(item.subtotal)}</span>
                    </div>
                `).join('');

                revInvoiceLines.innerHTML = roomLineHtml + (addonLinesHtml || `
                    <div class="flex items-center justify-between py-1 border-t border-indigo-100/80 text-slate-400">
                        <span>Opsi Tambahan (Add-ons)</span>
                        <span class="font-mono-code">Rp 0</span>
                    </div>
                `);
            }

            if (revTotalPrice) {
                revTotalPrice.textContent = formatRupiah(grandTotal);
            }
        }

        function getRoomCategoryInfo(room) {
            const cap = Number(room.capacity || 0);
            const layout = String(room.layout_type || '').toUpperCase();
            if (cap <= 6 || layout === 'POD') {
                return {
                    code: 'SMALL',
                    label: 'Small / Huddle Pod',
                    badgeClass: 'bg-sky-50 text-sky-700 border-sky-200',
                    desc: 'Ideal untuk diskusi tim kecil & 1-on-1',
                };
            }
            if (cap <= 12) {
                return {
                    code: 'MEDIUM',
                    label: 'Medium / Boardroom',
                    badgeClass: 'bg-indigo-50 text-indigo-700 border-indigo-200',
                    desc: 'Ideal untuk rapat divisi & presentasi eksekutif',
                };
            }
            return {
                code: 'LARGE',
                label: 'Large / Conference Hall',
                badgeClass: 'bg-violet-50 text-violet-700 border-violet-200',
                desc: 'Ideal untuk workshop, seminar & townhall',
            };
        }

        function roomMatchesCategory(room, category) {
            if (!category || category === 'ALL') return true;
            return getRoomCategoryInfo(room).code === category;
        }

        function updateCategoryBadgesCount() {
            const counts = {
                ALL: state.rooms.length,
                SMALL: state.rooms.filter(r => getRoomCategoryInfo(r).code === 'SMALL').length,
                MEDIUM: state.rooms.filter(r => getRoomCategoryInfo(r).code === 'MEDIUM').length,
                LARGE: state.rooms.filter(r => getRoomCategoryInfo(r).code === 'LARGE').length,
            };

            ['ALL', 'SMALL', 'MEDIUM', 'LARGE'].forEach(cat => {
                const el = document.getElementById(`cat-count-${cat}`);
                if (el) {
                    el.textContent = String(counts[cat] || 0);
                    if (cat === state.selectedCategory) {
                        el.className = 'px-1.5 py-0.5 rounded-md bg-white/20 text-white text-[10px] font-extrabold';
                    } else {
                        el.className = 'px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[10px] font-extrabold';
                    }
                }
            });
        }

        function selectRoomCategory(category) {
            state.selectedCategory = category || 'ALL';
            document.querySelectorAll('.cat-pill-btn').forEach(btn => {
                const cat = btn.getAttribute('data-cat-pill');
                const spans = btn.querySelectorAll('span');
                const subSpan = spans[spans.length - 1];
                if (cat === state.selectedCategory) {
                    btn.className = 'cat-pill-btn p-3 rounded-2xl text-left border border-indigo-600 bg-indigo-600 text-white shadow-xs transition cursor-pointer';
                    if (subSpan) subSpan.className = 'block text-[11px] opacity-85 mt-0.5';
                } else {
                    btn.className = 'cat-pill-btn p-3 rounded-2xl text-left border border-slate-200 bg-white text-slate-700 hover:border-indigo-400 transition cursor-pointer';
                    if (subSpan) subSpan.className = 'block text-[11px] text-slate-500 mt-0.5';
                }
            });

            updateCategoryBadgesCount();
            populateBookingRoomOptions(true);
            renderRoomsCatalog();
            updatePricePreview();
            loadRoomAvailability();
        }

        function populateBookingRoomOptions(autoSelectFirstInCat = false) {
            const bfRoom = document.getElementById('bf-room');
            if (!bfRoom) return;
            const currentRoomId = bfRoom.value;
            bfRoom.innerHTML = '';

            const filteredRooms = state.rooms.filter(r => roomMatchesCategory(r, state.selectedCategory));
            const roomsToShow = filteredRooms.length ? filteredRooms : state.rooms;

            roomsToShow.forEach(room => {
                const catInfo = getRoomCategoryInfo(room);
                const opt = document.createElement('option');
                opt.value = room.id;
                opt.textContent = `[${catInfo.label}] ${room.name} (Maks ${room.capacity} org · ${formatRupiah(room.price_per_hour)}/jam)${room.is_active ? '' : ' [Tidak Tersedia]'}`;
                bfRoom.appendChild(opt);
            });

            if (!autoSelectFirstInCat && currentRoomId && roomsToShow.some(r => String(r.id) === String(currentRoomId))) {
                bfRoom.value = currentRoomId;
            } else {
                const firstActive = roomsToShow.find(r => r.is_active) || roomsToShow[0];
                if (firstActive) {
                    bfRoom.value = firstActive.id;
                    const partInput = document.getElementById('bf-participants');
                    if (partInput && Number(partInput.value) > firstActive.capacity) {
                        partInput.value = firstActive.capacity;
                    }
                }
            }
        }

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

        function addDaysToDateString(baseDateStr, offsetDays) {
            const [y, m, d] = (baseDateStr || getTodayWibString()).split('-').map(Number);
            const dt = new Date(Date.UTC(y, m - 1, d + offsetDays));
            const yy = dt.getUTCFullYear();
            const mm = String(dt.getUTCMonth() + 1).padStart(2, '0');
            const dd = String(dt.getUTCDate()).padStart(2, '0');
            return `${yy}-${mm}-${dd}`;
        }

        function formatFriendlyDateWib(dateStr) {
            if (!dateStr) return '-';
            const [y, m, d] = dateStr.split('-').map(Number);
            if (!y || !m || !d) return dateStr;
            const dt = new Date(Date.UTC(y, m - 1, d, 5, 0, 0));
            const formatted = new Intl.DateTimeFormat('id-ID', {
                timeZone: 'Asia/Jakarta',
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            }).format(dt);

            const today = getTodayWibString();
            const tomorrow = addDaysToDateString(today, 1);
            const dayAfter = addDaysToDateString(today, 2);

            if (dateStr === today) return `Hari Ini • ${formatted}`;
            if (dateStr === tomorrow) return `Besok • ${formatted}`;
            if (dateStr === dayAfter) return `Lusa • ${formatted}`;
            return formatted;
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

        function showToast(type, title, message) {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const toastStyles = {
                success: 'bg-emerald-950/95 border-emerald-500/60 text-emerald-50',
                error: 'bg-rose-950/95 border-rose-500/60 text-rose-50',
                warning: 'bg-amber-950/95 border-amber-500/60 text-amber-50',
                info: 'bg-slate-900/95 border-indigo-500/60 text-slate-50',
            };

            const toast = document.createElement('div');
            toast.className = `pointer-events-auto rounded-2xl p-4 border shadow-xl backdrop-blur-md flex items-start justify-between gap-3 transition-all duration-300 ${toastStyles[type] || toastStyles.info}`;
            toast.innerHTML = `
                <div class="space-y-0.5">
                    <p class="font-extrabold text-xs">${title}</p>
                    <p class="text-[11px] opacity-90 leading-relaxed">${message}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-xs font-bold opacity-70 hover:opacity-100 cursor-pointer">✕</button>
            `;
            container.appendChild(toast);
            setTimeout(() => {
                if (toast.parentElement) toast.remove();
            }, 5500);
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
            showToast(type, title, message);
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

            const calRoom = document.getElementById('cal-room-filter');
            const currentCalRoom = calRoom.value;
            calRoom.innerHTML = '<option value="">Semua Ruang</option>';

            const blRoom = document.getElementById('bl-room-filter');
            const currentBlRoom = blRoom.value;
            blRoom.innerHTML = '<option value="">Semua Ruang</option>';

            state.rooms.forEach(room => {
                const optCal = document.createElement('option');
                optCal.value = room.id;
                optCal.textContent = room.name;
                calRoom.appendChild(optCal);

                const optBl = document.createElement('option');
                optBl.value = room.id;
                optBl.textContent = room.name;
                blRoom.appendChild(optBl);
            });

            populateBookingRoomOptions(false);
            const urlParams = new URLSearchParams(window.location.search);
            const urlRoomId = urlParams.get('room_id');
            if (!state.initialQueryApplied && urlRoomId && state.rooms.some(r => String(r.id) === String(urlRoomId))) {
                state.initialQueryApplied = true;
                const matchedRoom = state.rooms.find(r => String(r.id) === String(urlRoomId));
                document.getElementById('bf-room').value = urlRoomId;

                const urlDate = urlParams.get('date');
                if (urlDate && /^\d{4}-\d{2}-\d{2}$/.test(urlDate)) {
                    document.getElementById('bf-date').value = urlDate;
                    syncQuickDateButtons();
                }

                const urlStart = urlParams.get('start_time');
                const urlEnd = urlParams.get('end_time');
                if (urlStart && document.querySelector(`#bf-start option[value="${urlStart}"]`)) {
                    document.getElementById('bf-start').value = urlStart;
                }
                if (urlEnd && document.querySelector(`#bf-end option[value="${urlEnd}"]`)) {
                    document.getElementById('bf-end').value = urlEnd;
                }

                const urlPart = Number(urlParams.get('participants') || 0);
                if (urlPart > 0 && matchedRoom) {
                    document.getElementById('bf-participants').value = Math.min(urlPart, matchedRoom.capacity);
                } else if (matchedRoom && Number(document.getElementById('bf-participants').value) > matchedRoom.capacity) {
                    document.getElementById('bf-participants').value = matchedRoom.capacity;
                }

                // Karena ruangan sudah dipilih dari Landing Page, langsung arahkan ke Langkah 2 (Tanggal & Jam)
                setTimeout(() => {
                    if (matchedRoom && matchedRoom.is_active) {
                        goToBookingStep(2);
                    }
                }, 50);
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

        function setQuickCapacityFilter(val) {
            const input = document.getElementById('filter-min-capacity');
            if (input) input.value = val;
            document.querySelectorAll('.cap-filter-btn').forEach(btn => {
                if (btn.getAttribute('data-cap') === String(val)) {
                    btn.className = 'cap-filter-btn px-2.5 py-1 rounded-lg text-xs font-bold bg-indigo-600 text-white cursor-pointer';
                } else {
                    btn.className = 'cap-filter-btn px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 cursor-pointer';
                }
            });
            loadRooms();
        }

        function onParticipantsInputChange() {
            const participants = Math.max(1, Number(document.getElementById('bf-participants')?.value || 1));
            const bfRoom = document.getElementById('bf-room');
            const currentRoom = state.rooms.find(r => String(r.id) === String(bfRoom?.value));

            // Jika ruang yang sedang terpilih tidak muat untuk jumlah peserta baru,
            // otomatis pilihkan ruang aktif di katalog yang kapasitasnya mencukupi
            if (currentRoom && currentRoom.capacity < participants) {
                const candidateRooms = state.rooms
                    .filter(r => r.is_active && r.capacity >= participants)
                    .sort((a, b) => a.capacity - b.capacity);

                if (candidateRooms.length > 0) {
                    const bestFitRoom = candidateRooms[0];
                    if ( state.selectedCategory !== 'ALL' && !roomMatchesCategory(bestFitRoom, state.selectedCategory)) {
                        state.selectedCategory = 'ALL';
                        selectRoomCategory('ALL');
                    }
                    bfRoom.value = bestFitRoom.id;
                    loadRoomAvailability();
                }
            }

            updatePricePreview();
            renderRoomsCatalog();
        }

        function setParticipantsCount(count) {
            const el = document.getElementById('bf-participants');
            if (!el) return;
            el.value = Math.max(1, Number(count || 1));
            onParticipantsInputChange();
        }

        function adjustParticipants(delta) {
            const el = document.getElementById('bf-participants');
            if (!el) return;
            const current = Number(el.value || 1);
            el.value = Math.max(1, current + delta);
            onParticipantsInputChange();
        }

        function setParticipantsToRoomMax() {
            // Cari kapasitas tertinggi dari katalog ruang aktif (sesuai kategori aktif atau seluruh katalog)
            const pool = state.rooms.filter(r => r.is_active && roomMatchesCategory(r, state.selectedCategory));
            const activeRooms = pool.length ? pool : state.rooms.filter(r => r.is_active);
            if (!activeRooms.length) return;

            const maxRoom = [...activeRooms].sort((a, b) => b.capacity - a.capacity)[0];
            const bfRoom = document.getElementById('bf-room');
            if (bfRoom && maxRoom) {
                bfRoom.value = maxRoom.id;
                setParticipantsCount(maxRoom.capacity);
                loadRoomAvailability();
            }
        }

        function setBookingQuickDate(offsetDays) {
            const targetDate = addDaysToDateString(getTodayWibString(), offsetDays);
            const dateInput = document.getElementById('bf-date');
            if (dateInput) {
                dateInput.value = targetDate;
                onBookingRoomOrDateChange();
            }
        }

        function syncQuickDateButtons() {
            const selectedDate = document.getElementById('bf-date')?.value || getTodayWibString();
            const today = getTodayWibString();
            const labelEl = document.getElementById('bf-date-human-label');
            if (labelEl) {
                labelEl.textContent = formatFriendlyDateWib(selectedDate);
            }

            [0, 1, 2].forEach(offset => {
                const btn = document.getElementById(`bf-qdate-${offset}`);
                if (!btn) return;
                const d = addDaysToDateString(today, offset);
                if (selectedDate === d) {
                    btn.className = 'px-2.5 py-1 rounded-lg text-[11px] font-bold bg-indigo-600 text-white transition cursor-pointer';
                } else {
                    btn.className = 'px-2.5 py-1 rounded-lg text-[11px] font-bold bg-white text-slate-700 border border-slate-200 hover:bg-slate-100 transition cursor-pointer';
                }
            });
        }

        function setBookingDurationHours(hours) {
            const startSelect = document.getElementById('bf-start');
            const endSelect = document.getElementById('bf-end');
            if (!startSelect || !endSelect) return;

            let startMins = timeToMinutes(startSelect.value);
            const durationMins = Math.round(Number(hours) * 60);
            let targetEndMins = startMins + durationMins;

            // If end exceeds 21:00 WIB, shift start time earlier so full duration fits
            if (targetEndMins > 21 * 60) {
                targetEndMins = 21 * 60;
                startMins = Math.max(8 * 60, targetEndMins - durationMins);
                const sh = String(Math.floor(startMins / 60)).padStart(2, '0');
                const sm = String(startMins % 60).padStart(2, '0');
                startSelect.value = `${sh}:${sm}`;
            }

            const eh = String(Math.floor(targetEndMins / 60)).padStart(2, '0');
            const em = String(targetEndMins % 60).padStart(2, '0');
            endSelect.value = `${eh}:${em}`;

            onTimeSelectionChange();
        }

        function syncDurationQuickButtons(diffMinutes) {
            document.querySelectorAll('.dur-quick-btn').forEach(btn => {
                const targetMins = Number(btn.getAttribute('data-dur-btn'));
                if (targetMins === diffMinutes) {
                    btn.className = 'dur-quick-btn py-1.5 px-2 rounded-lg text-xs font-bold border border-indigo-600 bg-indigo-600 text-white shadow-2xs transition cursor-pointer';
                } else {
                    btn.className = 'dur-quick-btn py-1.5 px-2 rounded-lg text-xs font-bold border border-slate-200 bg-white text-slate-700 hover:border-indigo-400 transition cursor-pointer';
                }
            });
        }

        function onTimeSelectionChange() {
            updatePricePreview();
            if (state.lastAvailabilityData) {
                renderAvailabilitySlotsGrid(state.lastAvailabilityData);
            }
        }

        function clearSelectedAddons() {
            state.selectedAddons = {};
            renderBookingAddonsList();
            updatePricePreview();
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
            const resetBtn = document.getElementById('bf-addons-reset-btn');
            if (resetBtn) {
                resetBtn.classList.toggle('hidden', count === 0);
            }

            return total;
        }

        function renderRoomsCatalog() {
            const grid = document.getElementById('rooms-catalog-grid');
            if (!grid) return;

            updateCategoryBadgesCount();

            const participants = Math.max(1, Number(document.getElementById('bf-participants')?.value || 1));
            const onlyFit = document.getElementById('filter-fit-participants')?.checked || false;
            const sortBy = document.getElementById('catalog-sort-by')?.value || 'recommended';
            const selectedRoomId = document.getElementById('bf-room')?.value;

            let catalogRooms = state.rooms.filter(r => roomMatchesCategory(r, state.selectedCategory));
            if (onlyFit) {
                catalogRooms = catalogRooms.filter(r => r.capacity >= participants);
            }

            // Sort rooms based on user choice
            catalogRooms = [...catalogRooms].sort((a, b) => {
                if (sortBy === 'price_asc') return a.price_per_hour - b.price_per_hour;
                if (sortBy === 'capacity_asc') return a.capacity - b.capacity;
                if (sortBy === 'capacity_desc') return b.capacity - a.capacity;
                // Default 'recommended': active rooms that fit participants first, sorted by closest capacity fit
                const aFits = a.is_active && a.capacity >= participants ? 1 : 0;
                const bFits = b.is_active && b.capacity >= participants ? 1 : 0;
                if (aFits !== bFits) return bFits - aFits;
                return Math.abs(a.capacity - participants) - Math.abs(b.capacity - participants);
            });

            const summaryTextEl = document.getElementById('catalog-summary-text');
            if (summaryTextEl) {
                const fitCount = catalogRooms.filter(r => r.is_active && r.capacity >= participants).length;
                summaryTextEl.textContent = `Menampilkan ${catalogRooms.length} ruangan (${fitCount} ruangan muat untuk ${participants} peserta). Klik kartu untuk melihat simulasi kursi.`;
            }

            if (!catalogRooms.length) {
                grid.innerHTML = `
                    <div class="col-span-2 p-8 rounded-2xl border border-dashed border-slate-300 bg-slate-50 text-center space-y-2">
                        <p class="text-sm font-bold text-slate-700">Tidak ada ruangan yang cocok dengan filter saat ini.</p>
                        <p class="text-xs text-slate-500">Coba pilih tab "Semua Ruang" atau kurangi jumlah peserta (${participants} orang).</p>
                        <button type="button" onclick="document.getElementById('filter-fit-participants').checked = false; selectRoomCategory('ALL');"
                            class="px-3.5 py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold cursor-pointer">
                            Tampilkan Semua Ruangan
                        </button>
                    </div>
                `;
                return;
            }

            const start = document.getElementById('bf-start')?.value || '09:00';
            const end = document.getElementById('bf-end')?.value || '11:00';
            const durationHours = Math.max(1, (timeToMinutes(end) - timeToMinutes(start)) / 60);

            grid.innerHTML = catalogRooms.map(room => {
                const isSelectedRoom = String(room.id) === String(selectedRoomId);
                const catInfo = getRoomCategoryInfo(room);
                const fitsParticipants = room.capacity >= participants;
                const facilitiesHtml = (room.facilities || []).map(f =>
                    `<span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[11px] font-medium border border-slate-200/70">${f}</span>`
                ).join('');
                const dimText = room.dimensions_label || `${room.length_m || 6}m × ${room.width_m || 4}m`;
                const areaText = room.area_sqm || Math.round((room.length_m || 6) * (room.width_m || 4) * 10) / 10;
                const layoutLabel = room.layout_label || room.layout_type || 'Boardroom';
                const estCost = Math.round(room.price_per_hour * durationHours);

                const cardBorder = isSelectedRoom
                    ? 'border-2 border-indigo-600 bg-indigo-50/25 ring-4 ring-indigo-500/10 shadow-sm'
                    : (room.is_active
                        ? (fitsParticipants ? 'border border-slate-200/90 bg-white hover:border-indigo-400 hover:shadow-sm' : 'border border-amber-200 bg-amber-50/20 hover:border-amber-300')
                        : 'border border-slate-200 bg-slate-50 opacity-70');

                const fitBadge = !room.is_active
                    ? `<span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">Nonaktif</span>`
                    : (fitsParticipants
                        ? `<span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">✓ Muat (${participants}/${room.capacity} Kursi)</span>`
                        : `<span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">⚠ Maks. ${room.capacity} Kursi</span>`);

                return `
                    <div onclick="if(${room.is_active ? 'true' : 'false'}) selectRoomForBooking(${room.id})"
                        class="rounded-2xl ${cardBorder} p-4 flex flex-col justify-between gap-3.5 transition ${room.is_active ? 'cursor-pointer' : ''}">
                        <div class="space-y-3">
                            <!-- Card Top Badges -->
                            <div class="flex flex-wrap items-center justify-between gap-1.5">
                                <span class="px-2.5 py-0.5 rounded-lg border text-[10px] font-extrabold uppercase tracking-wider ${catInfo.badgeClass || 'bg-indigo-50 text-indigo-700 border-indigo-200'}">
                                    ${catInfo.label}
                                </span>
                                ${fitBadge}
                            </div>

                            <!-- Room Title & Description -->
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="font-extrabold text-slate-900 text-base">${room.name}</h3>
                                    ${isSelectedRoom ? `<span class="px-2 py-0.5 rounded-md bg-indigo-600 text-white text-[10px] font-extrabold uppercase">✓ Terpilih</span>` : ''}
                                </div>
                                <p class="text-[11px] text-slate-500 mt-0.5">${catInfo.desc || ''}</p>
                            </div>

                            <!-- 3 Key Specs Grid -->
                            <div class="grid grid-cols-3 gap-1.5 text-center">
                                <div class="p-2 rounded-xl bg-slate-50 border border-slate-200/70">
                                    <span class="block text-[10px] text-slate-400 font-semibold uppercase">Kapasitas</span>
                                    <span class="block text-xs font-extrabold text-slate-800">${room.capacity} Kursi</span>
                                </div>
                                <div class="p-2 rounded-xl bg-slate-50 border border-slate-200/70">
                                    <span class="block text-[10px] text-slate-400 font-semibold uppercase">Dimensi</span>
                                    <span class="block text-xs font-mono-code font-bold text-indigo-700">${dimText}</span>
                                </div>
                                <div class="p-2 rounded-xl bg-slate-50 border border-slate-200/70">
                                    <span class="block text-[10px] text-slate-400 font-semibold uppercase">Luas</span>
                                    <span class="block text-xs font-mono-code font-bold text-slate-800">${areaText} m²</span>
                                </div>
                            </div>

                            <!-- Mini Room Blueprint Preview (Reflecting actual participant occupancy) -->
                            <div class="rounded-xl bg-slate-900 p-2.5 border border-slate-800">
                                <div class="flex items-center justify-between text-[10px] text-slate-300 px-1 pb-1">
                                    <span class="font-bold text-indigo-300">${layoutLabel}</span>
                                    <span class="text-emerald-400 font-semibold">${Math.min(participants, room.capacity)}/${room.capacity} kursi terisi</span>
                                </div>
                                <div class="h-28 w-full flex items-center justify-center">
                                    ${generateRoomBlueprintSvg(room, Math.min(participants, room.capacity), true)}
                                </div>
                            </div>

                            <!-- Included Facilities -->
                            <div>
                                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Fasilitas Bawaan:</span>
                                <div class="flex flex-wrap gap-1">${facilitiesHtml}</div>
                            </div>
                        </div>

                        <!-- Card Footer: Pricing & Action Buttons -->
                        <div class="flex items-center justify-between pt-3 border-t border-slate-100" onclick="event.stopPropagation()">
                            <div>
                                <span class="text-sm font-extrabold text-indigo-700 font-mono-code block">${formatRupiah(room.price_per_hour)}<span class="text-xs font-normal text-slate-500"> / jam</span></span>
                                <span class="text-[10px] text-slate-400">Estimasi ${durationHours} jam: <strong class="text-slate-600 font-mono-code">${formatRupiah(estCost)}</strong></span>
                            </div>
                            ${room.is_active ? `
                                <div class="flex items-center gap-1.5">
                                    <button type="button" onclick="selectRoomForBooking(${room.id})"
                                        class="px-3 py-2 rounded-xl text-xs font-bold ${isSelectedRoom ? 'bg-indigo-100 text-indigo-800 border border-indigo-300' : 'bg-slate-100 hover:bg-slate-200 text-slate-700'} transition cursor-pointer">
                                        ${isSelectedRoom ? '✓ Dipilih' : 'Pilih'}
                                    </button>
                                    <button type="button" onclick="selectRoomForBooking(${room.id}); goToBookingStep(2);"
                                        class="px-3.5 py-2 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white shadow-2xs transition cursor-pointer">
                                        Pilih &amp; Lanjut &rarr;
                                    </button>
                                </div>
                            ` : `<span class="text-xs text-slate-400 font-semibold">Tidak dapat dipesan</span>`}
                        </div>
                    </div>
                `;
            }).join('');
        }

        function selectRoomForBooking(roomId, targetDate = null) {
            const bfRoom = document.getElementById('bf-room');
            bfRoom.value = roomId;
            if (targetDate) {
                document.getElementById('bf-date').value = targetDate;
                switchTab('booking');
            }
            onBookingRoomOrDateChange();
            const room = state.rooms.find(r => String(r.id) === String(roomId));
            if (room) {
                showToast('info', `${room.name} Dipilih`, `Kapasitas maks. ${room.capacity} peserta (${formatRupiah(room.price_per_hour)}/jam). Lanjut pilih tanggal & jam rapat.`);
            }
            if (targetDate) {
                goToBookingStep(2);
            }
        }

        function onBookingRoomOrDateChange() {
            syncQuickDateButtons();
            renderRoomsCatalog();
            updatePricePreview();
            loadRoomAvailability();
        }

        function timeToMinutes(t) {
            const [h, m] = (t || '00:00').split(':').map(Number);
            return h * 60 + m;
        }

        function generateRoomBlueprintSvg(room, occupiedParticipants = 0, compact = false) {
            const lengthM = Number(room.length_m || 6.0);
            const widthM = Number(room.width_m || 4.0);
            const cap = Math.max(1, Number(room.capacity || 8));
            const occupied = Math.max(0, Math.min(cap, Number(occupiedParticipants || 0)));
            const layoutType = String(room.layout_type || 'BOARDROOM').toUpperCase();

            // Canvas coordinate space: 360 x 210
            const W = 360;
            const H = 210;
            const rx = 38, ry = 26, rw = 284, rh = 152;

            // Compute seat coordinates based on layoutType & capacity
            const seats = [];
            let tableSvg = '';

            if (layoutType === 'U_SHAPE') {
                // U-shape table opening towards left screen
                tableSvg = `
                    <path d="M 115 62 H 265 V 142 H 115 V 124 H 245 V 80 H 115 Z"
                        fill="#1e293b" stroke="#6366f1" stroke-width="1.8" />
                    <text x="188" y="106" text-anchor="middle" fill="#94a3b8" font-size="8" font-family="monospace">MEJA U-SHAPE</text>
                `;
                const topCount = Math.ceil((cap - 2) / 2);
                const bottomCount = Math.max(1, cap - 2 - topCount);
                for (let i = 0; i < topCount; i++) {
                    const x = 128 + (i * (125 / Math.max(1, topCount - 1 || 1)));
                    seats.push({ x, y: 49 });
                }
                seats.push({ x: 279, y: 84 });
                seats.push({ x: 279, y: 120 });
                for (let i = 0; i < bottomCount; i++) {
                    const x = 128 + (i * (125 / Math.max(1, bottomCount - 1 || 1)));
                    seats.push({ x, y: 155 });
                }
            } else if (layoutType === 'CLASSROOM') {
                // Classroom rows facing left screen
                const cols = 2;
                const rowsPerCol = Math.ceil(cap / (cols * 2));
                tableSvg = `
                    <rect x="135" y="54" width="18" height="96" rx="3" fill="#1e293b" stroke="#6366f1" stroke-width="1.5"/>
                    <rect x="215" y="54" width="18" height="96" rx="3" fill="#1e293b" stroke="#6366f1" stroke-width="1.5"/>
                `;
                for (let i = 0; i < cap; i++) {
                    const col = i % 2;
                    const row = Math.floor(i / 2);
                    const x = col === 0 ? 165 : 245;
                    const y = 62 + (row * (82 / Math.max(1, rowsPerCol - 1 || 1)));
                    seats.push({ x, y });
                }
            } else if (layoutType === 'POD') {
                // Compact pod table
                tableSvg = `
                    <rect x="128" y="72" width="105" height="60" rx="18" fill="#1e293b" stroke="#6366f1" stroke-width="1.8"/>
                    <text x="180" y="105" text-anchor="middle" fill="#94a3b8" font-size="8" font-family="monospace">HUDDLE TABLE</text>
                `;
                const topCount = Math.ceil(cap / 2);
                const bottomCount = cap - topCount;
                for (let i = 0; i < topCount; i++) {
                    const x = 148 + (i * (65 / Math.max(1, topCount - 1 || 1)));
                    seats.push({ x, y: 58 });
                }
                for (let i = 0; i < bottomCount; i++) {
                    const x = 148 + (i * (65 / Math.max(1, bottomCount - 1 || 1)));
                    seats.push({ x, y: 146 });
                }
            } else {
                // Default BOARDROOM long conference table
                tableSvg = `
                    <rect x="108" y="72" width="150" height="60" rx="12" fill="#1e293b" stroke="#6366f1" stroke-width="1.8"/>
                    <rect x="118" y="82" width="130" height="40" rx="6" fill="#0f172a" stroke="#334155" stroke-width="1"/>
                    <text x="183" y="105" text-anchor="middle" fill="#94a3b8" font-size="8" font-weight="bold" font-family="monospace">MEJA RAPAT UTAMA</text>
                `;
                // Head & tail seats if capacity >= 6
                const sideSeats = Math.max(2, cap - 2);
                const topCount = Math.ceil(sideSeats / 2);
                const bottomCount = sideSeats - topCount;

                seats.push({ x: 93, y: 102 }); // Head of table
                for (let i = 0; i < topCount; i++) {
                    const x = 126 + (i * (114 / Math.max(1, topCount - 1 || 1)));
                    seats.push({ x, y: 58 });
                }
                for (let i = 0; i < bottomCount; i++) {
                    const x = 126 + (i * (114 / Math.max(1, bottomCount - 1 || 1)));
                    seats.push({ x, y: 146 });
                }
                if (seats.length < cap) {
                    seats.push({ x: 273, y: 102 }); // Tail of table
                }
            }

            const seatsSvg = seats.slice(0, cap).map((s, idx) => {
                const isOccupied = idx < occupied;
                const fill = isOccupied ? '#34d399' : '#334155';
                const stroke = isOccupied ? '#a7f3d0' : '#64748b';
                return `
                    <g>
                        <circle cx="${s.x}" cy="${s.y}" r="7.5" fill="${fill}" stroke="${stroke}" stroke-width="1.6"/>
                        <text x="${s.x}" y="${s.y + 2.5}" text-anchor="middle" fill="${isOccupied ? '#022c22' : '#cbd5e1'}" font-size="6.5" font-weight="bold" font-family="monospace">${idx + 1}</text>
                    </g>
                `;
            }).join('');

            return `
                <svg viewBox="0 0 ${W} ${H}" class="w-full h-full max-h-44 select-none">
                    <defs>
                        <pattern id="grid-${room.id}-${compact ? 'c' : 'f'}" width="16" height="16" patternUnits="userSpaceOnUse">
                            <path d="M 16 0 L 0 0 0 16" fill="none" stroke="#1e293b" stroke-width="0.7"/>
                        </pattern>
                    </defs>

                    <!-- Dimension Top Arrow (Panjang / Length) -->
                    <line x1="${rx}" y1="14" x2="${rx + rw}" y2="14" stroke="#818cf8" stroke-width="1.2" stroke-dasharray="3,2"/>
                    <line x1="${rx}" y1="9" x2="${rx}" y2="19" stroke="#818cf8" stroke-width="1.5"/>
                    <line x1="${rx + rw}" y1="9" x2="${rx + rw}" y2="19" stroke="#818cf8" stroke-width="1.5"/>
                    <rect x="${rx + rw / 2 - 44}" y="5" width="88" height="16" rx="4" fill="#0f172a"/>
                    <text x="${rx + rw / 2}" y="16" text-anchor="middle" fill="#a5b4fc" font-size="9" font-weight="bold" font-family="monospace">PANJANG ${lengthM}m</text>

                    <!-- Dimension Left Arrow (Lebar / Width) -->
                    <line x1="16" y1="${ry}" x2="16" y2="${ry + rh}" stroke="#818cf8" stroke-width="1.2" stroke-dasharray="3,2"/>
                    <line x1="11" y1="${ry}" x2="21" y2="${ry}" stroke="#818cf8" stroke-width="1.5"/>
                    <line x1="11" y1="${ry + rh}" x2="21" y2="${ry + rh}" stroke="#818cf8" stroke-width="1.5"/>
                    <g transform="translate(15, ${ry + rh / 2}) rotate(-90)">
                        <rect x="-36" y="-8" width="72" height="15" rx="4" fill="#0f172a"/>
                        <text x="0" y="3" text-anchor="middle" fill="#a5b4fc" font-size="8.5" font-weight="bold" font-family="monospace">LEBAR ${widthM}m</text>
                    </g>

                    <!-- Outer Room Wall & Blueprint Grid -->
                    <rect x="${rx}" y="${ry}" width="${rw}" height="${rh}" rx="8" fill="url(#grid-${room.id}-${compact ? 'c' : 'f'})" stroke="#6366f1" stroke-width="2.2"/>

                    <!-- Screen / Smart TV & Whiteboard on Left Wall -->
                    <rect x="${rx + 4}" y="${ry + 36}" width="7" height="80" rx="2" fill="#38bdf8"/>
                    <text x="${rx + 17}" y="${ry + 78}" fill="#38bdf8" font-size="7" font-weight="bold" font-family="monospace" transform="rotate(-90, ${rx + 17}, ${ry + 78})">TV / PROYEKTOR</text>

                    <!-- AC Unit on Top Wall -->
                    <rect x="${rx + rw - 58}" y="${ry + 3}" width="42" height="6" rx="2" fill="#94a3b8"/>
                    <text x="${rx + rw - 37}" y="${ry + 16}" text-anchor="middle" fill="#64748b" font-size="6.5" font-family="monospace">AC</text>

                    <!-- Door on Bottom-Right Wall -->
                    <line x1="${rx + rw - 44}" y1="${ry + rh}" x2="${rx + rw - 14}" y2="${ry + rh}" stroke="#0f172a" stroke-width="3"/>
                    <path d="M ${rx + rw - 44} ${ry + rh} A 26 26 0 0 1 ${rx + rw - 18} ${ry + rh - 24} L ${rx + rw - 18} ${ry + rh}" fill="none" stroke="#fbbf24" stroke-width="1.2" stroke-dasharray="2,2"/>
                    <text x="${rx + rw - 29}" y="${ry + rh + 11}" text-anchor="middle" fill="#fbbf24" font-size="7.5" font-weight="bold" font-family="monospace">PINTU</text>

                    <!-- Main Conference Table -->
                    ${tableSvg}

                    <!-- Dynamic Participant Chairs -->
                    ${seatsSvg}
                </svg>
            `;
        }

        function renderSelectedRoomBlueprint(room, participants) {
            if (!room) return;
            const lengthM = room.length_m || 6.0;
            const widthM = room.width_m || 4.0;
            const dimLabel = room.dimensions_label || `${lengthM}m × ${widthM}m`;
            const areaSqm = room.area_sqm || Math.round(lengthM * widthM * 10) / 10;
            const layoutLabel = room.layout_label || room.layout_type || 'Boardroom';
            const occupied = Math.max(0, Math.min(room.capacity, Number(participants || 0)));
            const empty = Math.max(0, room.capacity - occupied);

            document.getElementById('bf-layout-room-title').textContent = `${room.name} (Kapasitas ${room.capacity} Kursi)`;
            document.getElementById('bf-layout-dim-badge').textContent = dimLabel;
            document.getElementById('bf-layout-type-badge').textContent = layoutLabel;
            document.getElementById('bf-layout-area-label').textContent = `Luas: ${areaSqm} m²`;
            document.getElementById('bf-layout-occupied-count').textContent = `${occupied} org`;
            document.getElementById('bf-layout-empty-count').textContent = `${empty} kursi`;
            document.getElementById('bf-layout-svg-container').innerHTML = generateRoomBlueprintSvg(room, occupied, false);
        }

        function updatePricePreview() {
            const roomId = document.getElementById('bf-room').value;
            const room = state.rooms.find(r => String(r.id) === String(roomId));
            const start = document.getElementById('bf-start').value;
            const end = document.getElementById('bf-end').value;
            const participants = Number(document.getElementById('bf-participants').value || 0);

            const metaEl = document.getElementById('bf-room-meta');
            const capBadgeEl = document.getElementById('bf-capacity-status-badge');
            const durationEl = document.getElementById('bf-duration-label');
            const roomPriceEl = document.getElementById('bf-room-price-preview');
            const addonsPriceEl = document.getElementById('bf-addons-price-preview');
            const priceEl = document.getElementById('bf-price-preview');
            const hintEl = document.getElementById('bf-rule-hint');

            if (!room) {
                metaEl.textContent = '';
                return;
            }

            const dimLabel = room.dimensions_label || `${room.length_m || 6}m × ${room.width_m || 4}m`;
            const areaSqm = room.area_sqm || Math.round((room.length_m || 6) * (room.width_m || 4) * 10) / 10;
            metaEl.textContent = `Kapasitas: maks. ${room.capacity} orang • Ukuran: ${dimLabel} (${areaSqm} m²) • Fasilitas Bawaan: ${(room.facilities || []).join(', ')}`;

            if (capBadgeEl) {
                if (participants <= 0 || participants > room.capacity) {
                    capBadgeEl.textContent = `Melebihi Kapasitas (${participants}/${room.capacity} org)`;
                    capBadgeEl.className = 'text-[11px] font-bold px-2 py-0.5 rounded-md bg-rose-50 text-rose-700 border border-rose-200';
                } else {
                    capBadgeEl.textContent = `${participants} / ${room.capacity} Kursi Terpakai`;
                    capBadgeEl.className = 'text-[11px] font-bold px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200';
                }
            }

            renderSelectedRoomBlueprint(room, participants);

            const diffMinutes = timeToMinutes(end) - timeToMinutes(start);
            const hours = diffMinutes / 60;
            syncDurationQuickButtons(diffMinutes);

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

            durationEl.textContent = `${hours} Jam (${diffMinutes} menit) • ${start} – ${end} WIB`;

            if (diffMinutes < 60 || diffMinutes > 480) {
                durationEl.className = 'font-bold text-rose-600';
                hintEl.textContent = '⚠ Durasi booking minimal 1 jam (60 menit) dan maksimal 8 jam (480 menit).';
                hintEl.className = 'text-[11px] text-rose-600 font-semibold';
            } else if (participants <= 0 || participants > room.capacity) {
                durationEl.className = 'font-bold text-amber-600';
                hintEl.textContent = `⚠ Jumlah peserta (${participants} orang) melebihi kapasitas ${room.name} (${room.capacity} orang). Silakan kurangi peserta atau pilih ruang yang lebih besar.`;
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
            updateWizardReviewSummary();
        }

        function renderAvailabilitySlotsGrid(data) {
            const slotsGrid = document.getElementById('avail-slots-grid');
            if (!slotsGrid || !data) return;

            const selectedStart = timeToMinutes(document.getElementById('bf-start')?.value || '09:00');
            const selectedEnd = timeToMinutes(document.getElementById('bf-end')?.value || '11:00');

            slotsGrid.innerHTML = (data.slots || []).map(slot => {
                const slotStartMins = timeToMinutes(slot.start_time);
                const isSelectedSlot = slot.is_available && slotStartMins >= selectedStart && slotStartMins < selectedEnd;

                if (isSelectedSlot) {
                    return `
                        <button type="button" onclick="pickSlotStartTime('${slot.start_time}')"
                            title="Sedang dipilih: ${slot.start_time} - ${slot.end_time} WIB (Klik untuk jadikan jam mulai)"
                            class="px-2 py-2 rounded-xl border-2 border-indigo-600 bg-indigo-600 text-white text-center shadow-xs transition cursor-pointer">
                            <span class="block font-mono-code text-xs font-extrabold">${slot.start_time}</span>
                            <span class="block text-[10px] font-bold">Dipilih ✓</span>
                        </button>
                    `;
                } else if (slot.is_available) {
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
        }

        async function loadRoomAvailability() {
            const roomId = document.getElementById('bf-room').value;
            const date = document.getElementById('bf-date').value;
            if (!roomId || !date) return;

            const room = state.rooms.find(r => String(r.id) === String(roomId));
            document.getElementById('avail-date-badge').textContent = `${room ? room.name : ''} • ${formatFriendlyDateWib(date)}`;

            try {
                const res = await fetch(`/api/rooms/${roomId}/availability?date=${encodeURIComponent(date)}`);
                const json = await res.json();
                if (!res.ok) return;

                const data = json.data;
                state.lastAvailabilityData = data;
                const freeContainer = document.getElementById('avail-free-ranges');
                const bookedContainer = document.getElementById('avail-booked-ranges');

                if (!data.available_slots || !data.available_slots.length) {
                    freeContainer.innerHTML = '<p class="text-rose-700 font-semibold">Tidak ada slot kosong pada tanggal ini.</p>';
                } else {
                    freeContainer.innerHTML = data.available_slots.map(s => `
                        <button type="button" onclick="pickSlotStartTime('${s.start_time}')"
                            class="w-full flex items-center justify-between bg-white/90 hover:bg-emerald-600 hover:text-white px-3 py-2 rounded-xl border border-emerald-200/80 transition cursor-pointer group">
                            <span class="font-mono-code font-bold text-emerald-900 group-hover:text-white">${s.start_time} – ${s.end_time} WIB</span>
                            <span class="text-[11px] text-emerald-700 group-hover:text-emerald-100 font-semibold">${(s.duration_minutes / 60)} jam tersedia · Pilih &rarr;</span>
                        </button>
                    `).join('');
                }

                if (!data.booked_slots || !data.booked_slots.length) {
                    bookedContainer.innerHTML = '<p class="text-slate-500">Seluruh jam operasional 08:00–21:00 WIB masih kosong.</p>';
                } else {
                    bookedContainer.innerHTML = data.booked_slots.map(b => `
                        <div class="flex items-center justify-between bg-white/80 px-3 py-2 rounded-xl border border-rose-200/80">
                            <span class="font-mono-code font-bold text-rose-900">${b.start_time} – ${b.end_time} WIB</span>
                            <span class="text-[11px] text-rose-700 font-semibold truncate max-w-[140px]">${b.booker_name}</span>
                        </div>
                    `).join('');
                }

                renderAvailabilitySlotsGrid(data);
            } catch (e) {
                console.error(e);
            }
        }

        function pickSlotStartTime(startTime) {
            const startSelect = document.getElementById('bf-start');
            const endSelect = document.getElementById('bf-end');
            const prevDuration = Math.max(60, Math.min(480, timeToMinutes(endSelect.value) - timeToMinutes(startSelect.value) || 120));
            startSelect.value = startTime;

            const startMins = timeToMinutes(startTime);
            const targetEndMins = Math.min(21 * 60, startMins + prevDuration);
            const finalEndMins = Math.max(startMins + 60, targetEndMins);
            const eh = String(Math.floor(finalEndMins / 60)).padStart(2, '0');
            const em = String(finalEndMins % 60).padStart(2, '0');
            endSelect.value = `${eh}:${em}`;

            onTimeSelectionChange();
        }

        async function submitBooking(e) {
            e.preventDefault();
            const additional_facilities = Object.entries(state.selectedAddons).map(([id, quantity]) => ({
                id: Number(id),
                quantity: Number(quantity),
            }));

            const payload = {
                user_id: state.loggedInCustomer?.id || null,
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
                    document.getElementById('bf-name').value = state.loggedInCustomer?.name || '';
                    document.getElementById('bf-contact').value = state.loggedInCustomer?.phone || state.loggedInCustomer?.email || '';
                    state.selectedAddons = {};
                    renderBookingAddonsList();
                    goToBookingStep(1);
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

        function shiftCalendarDate(deltaDays) {
            const calDateEl = document.getElementById('cal-date');
            if (!calDateEl) return;
            const step = state.calendarView === 'week' ? deltaDays * 7 : deltaDays;
            calDateEl.value = addDaysToDateString(calDateEl.value || getTodayWibString(), step);
            loadCalendar();
        }

        function resetCalendarToToday() {
            const calDateEl = document.getElementById('cal-date');
            if (!calDateEl) return;
            calDateEl.value = getTodayWibString();
            loadCalendar();
        }

        function resetBookingsFilter() {
            const payEl = document.getElementById('bl-payment-filter');
            const statEl = document.getElementById('bl-status-filter');
            const roomEl = document.getElementById('bl-room-filter');
            const searchEl = document.getElementById('bl-search');
            if (payEl) payEl.value = '';
            if (statEl) statEl.value = '';
            if (roomEl) roomEl.value = '';
            if (searchEl) searchEl.value = '';
            loadBookingsList();
        }

        async function loadCalendar() {
            const date = document.getElementById('cal-date').value || getTodayWibString();
            const roomId = document.getElementById('cal-room-filter').value;
            const activeDateLabel = document.getElementById('cal-active-date-label');
            if (activeDateLabel) {
                activeDateLabel.textContent = `Menampilkan jadwal mulai: ${formatFriendlyDateWib(date)}`;
            }

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
                        <div class="p-4 rounded-2xl border border-slate-200/80 bg-slate-50/50 space-y-2.5 flex flex-col justify-between">
                            <div class="space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="font-extrabold text-sm text-slate-900">${r.room_name}</h4>
                                        <span class="text-[11px] font-semibold text-slate-500">Kapasitas maks. ${r.capacity} org</span>
                                    </div>
                                    ${r.is_active ? `
                                        <button type="button" onclick="selectRoomForBooking(${r.room_id}, '${dayObj.date}')"
                                            class="px-2.5 py-1 rounded-lg bg-white hover:bg-indigo-600 text-indigo-700 hover:text-white border border-indigo-200 text-[11px] font-bold transition cursor-pointer">
                                            + Pesan Ruang Ini
                                        </button>
                                    ` : `<span class="text-[10px] font-bold text-rose-600">Nonaktif</span>`}
                                </div>
                                <div class="space-y-2">${bookingsHtml}</div>
                            </div>
                        </div>
                    `;
                }).join('');

                return `
                    <div class="space-y-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-3 py-1 rounded-full bg-slate-900 text-white font-mono-code text-xs font-bold">${dayObj.date}</span>
                            <span class="text-xs font-bold text-indigo-700">${formatFriendlyDateWib(dayObj.date)}</span>
                            <span class="text-xs text-slate-400">• Jam Operasional 08:00 – 21:00 WIB</span>
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
            if (state.loggedInCustomer?.id) {
                params.set('user_id', state.loggedInCustomer.id);
                if (state.loggedInCustomer.phone) {
                    params.set('customer_contact', state.loggedInCustomer.phone);
                }
            }

            const res = await fetch(`/api/bookings?${params.toString()}`);
            const json = await res.json();
            state.bookings = json.data || [];

            const countBadge = document.getElementById('tab-bookings-count-badge');
            if (countBadge && !status && !paymentStatus && !roomId && !search) {
                countBadge.textContent = state.bookings.length;
            }

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
                            <span class="font-sans text-[11px] text-slate-500 block">${formatFriendlyDateWib(b.date)}</span>
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
                                ${isPaid ? 'Lihat / Cetak Nota' : 'Bayar QRIS & Nota'}
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

        function printOfficialInvoice() {
            window.print();
        }

        async function openQrisModal(bookingId) {
            currentQrisBookingId = bookingId;
            try {
                const res = await fetch(`/api/bookings/${bookingId}/qris`);
                const json = await res.json();
                if (!res.ok || !json.data) {
                    showAlert('error', 'Gagal Memuat Nota & QRIS', json.message || 'Tidak dapat memuat detail nota dan pembayaran QRIS.');
                    return;
                }

                const q = json.data;
                const b = q.booking;
                const isPaid = q.payment_status === 'PAID';
                const isCancelled = b.status === 'CANCELLED';
                const roomName = b.room ? b.room.name : `Ruang #${b.room_id}`;
                const roomRate = b.room && b.room.price_per_hour
                    ? b.room.price_per_hour
                    : (b.duration_hours > 0 ? Math.round((q.room_price || 0) / b.duration_hours) : 0);

                const dateCompact = (b.date || getTodayWibString()).replace(/-/g, '');
                const paddedId = String(q.booking_id || b.id).padStart(4, '0');
                const invNumber = `INV/RK/${dateCompact}/${paddedId}`;

                const nowFormatted = new Intl.DateTimeFormat('id-ID', {
                    timeZone: 'Asia/Jakarta',
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                }).format(new Date()) + ' WIB';

                document.getElementById('qm-invoice-number').textContent = invNumber;
                document.getElementById('qm-booking-code').textContent = `${q.booking_code} (#${q.booking_id})`;
                document.getElementById('qm-issued-at').textContent = `Dicetak: ${nowFormatted}`;
                document.getElementById('qm-total-amount').textContent = formatRupiah(q.total_price);
                document.getElementById('qm-reference').textContent = `Ref: ${q.payment_reference} • Metode: QRIS Nasional`;
                document.getElementById('qm-qr-container').innerHTML = generateQrisSvg(q.qris_payload);

                document.getElementById('qm-booker-name').textContent = b.booker_name || '-';
                document.getElementById('qm-booker-contact').textContent = `Kontak: ${b.booker_contact || '-'}`;

                const bookingStatusEl = document.getElementById('qm-booking-status-text');
                if (bookingStatusEl) {
                    bookingStatusEl.textContent = b.status;
                    bookingStatusEl.className = isCancelled ? 'font-extrabold text-rose-600' : 'font-extrabold text-emerald-700';
                }

                document.getElementById('qm-room-info').textContent = `${roomName} (${b.participants} peserta)`;
                const dateInfoEl = document.getElementById('qm-date-info');
                if (dateInfoEl) {
                    dateInfoEl.textContent = formatFriendlyDateWib(b.date);
                }
                document.getElementById('qm-schedule-info').textContent = `${b.start_hour} – ${b.end_hour} WIB (${b.duration_hours} jam)`;
                document.getElementById('qm-room-price').textContent = formatRupiah(q.room_price);

                const addonsSubtotalEl = document.getElementById('qm-addons-subtotal');
                if (addonsSubtotalEl) {
                    addonsSubtotalEl.textContent = formatRupiah(q.additional_facilities_price || 0);
                }

                // Populate itemized invoice table rows
                const itemsTbody = document.getElementById('qm-invoice-items-tbody');
                if (itemsTbody) {
                    const rows = [];
                    rows.push(`
                        <tr>
                            <td class="py-3 px-3.5 font-mono-code font-bold text-slate-400">01</td>
                            <td class="py-3 px-3.5">
                                <span class="font-extrabold text-slate-900 block">Sewa Ruang Meeting — ${roomName}</span>
                                <span class="text-[11px] text-slate-500">Jadwal: ${b.date} (${b.start_hour}–${b.end_hour} WIB) • ${b.participants} Peserta</span>
                            </td>
                            <td class="py-3 px-3.5 font-mono-code text-slate-700">${b.duration_hours} jam × ${formatRupiah(roomRate)}/jam</td>
                            <td class="py-3 px-3.5 text-right font-mono-code font-extrabold text-slate-900">${formatRupiah(q.room_price)}</td>
                        </tr>
                    `);

                    const addons = b.additional_facilities || [];
                    addons.forEach((a, idx) => {
                        const rowNum = String(idx + 2).padStart(2, '0');
                        let calcDetail = `${a.quantity} paket × ${formatRupiah(a.unit_price)}`;
                        if (a.pricing_type === 'PER_PARTICIPANT') {
                            calcDetail = `${b.participants} peserta × ${formatRupiah(a.unit_price)}/peserta`;
                        } else if (a.pricing_type === 'PER_HOUR') {
                            calcDetail = `${a.quantity} unit × ${b.duration_hours} jam × ${formatRupiah(a.unit_price)}/jam`;
                        }

                        rows.push(`
                            <tr>
                                <td class="py-2.5 px-3.5 font-mono-code font-bold text-slate-400">${rowNum}</td>
                                <td class="py-2.5 px-3.5">
                                    <span class="font-bold text-indigo-950 block">+ ${a.name}</span>
                                    <span class="text-[10px] text-slate-500 uppercase">Opsi Tambahan (${a.pricing_label || a.pricing_type})</span>
                                </td>
                                <td class="py-2.5 px-3.5 font-mono-code text-slate-700">${calcDetail}</td>
                                <td class="py-2.5 px-3.5 text-right font-mono-code font-bold text-indigo-700">${formatRupiah(a.subtotal)}</td>
                            </tr>
                        `);
                    });

                    itemsTbody.innerHTML = rows.join('');
                }

                // Penalty row if cancelled with penalty
                const penaltyRow = document.getElementById('qm-penalty-row');
                const penaltyAmountEl = document.getElementById('qm-penalty-amount');
                if (penaltyRow && penaltyAmountEl) {
                    if (isCancelled && Number(b.penalty_amount || 0) > 0) {
                        penaltyRow.classList.remove('hidden');
                        penaltyAmountEl.textContent = formatRupiah(b.penalty_amount);
                    } else {
                        penaltyRow.classList.add('hidden');
                    }
                }

                const statusBadge = document.getElementById('qm-status-badge');
                const paidBanner = document.getElementById('qm-paid-banner');
                const unpaidBanner = document.getElementById('qm-unpaid-banner');
                const paidTimestamp = document.getElementById('qm-paid-timestamp');
                const payBtn = document.getElementById('qm-pay-btn');

                if (isPaid) {
                    statusBadge.textContent = 'LUNAS (PAID)';
                    statusBadge.className = 'px-2.5 py-0.5 rounded-md bg-emerald-500 text-white text-[10px] font-extrabold uppercase';
                    paidBanner.classList.remove('hidden');
                    if (unpaidBanner) unpaidBanner.classList.add('hidden');
                    if (paidTimestamp) {
                        paidTimestamp.textContent = `Diverifikasi pada: ${q.paid_at ? q.paid_at.replace('T', ' ').substring(0, 19) + ' WIB' : nowFormatted}`;
                    }
                    payBtn.classList.add('hidden');
                } else if (isCancelled) {
                    statusBadge.textContent = 'DIBATALKAN';
                    statusBadge.className = 'px-2.5 py-0.5 rounded-md bg-rose-500 text-white text-[10px] font-extrabold uppercase';
                    paidBanner.classList.add('hidden');
                    if (unpaidBanner) unpaidBanner.classList.add('hidden');
                    payBtn.classList.add('hidden');
                } else {
                    statusBadge.textContent = 'BELUM BAYAR (UNPAID)';
                    statusBadge.className = 'px-2.5 py-0.5 rounded-md bg-amber-400 text-slate-950 text-[10px] font-extrabold uppercase';
                    paidBanner.classList.add('hidden');
                    if (unpaidBanner) unpaidBanner.classList.remove('hidden');
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
            syncQuickDateButtons();
            updateWibClock();
            setInterval(updateWibClock, 1000);

            @if (session('auth_success'))
                showAlert('success', 'Autentikasi Berhasil', @json(session('auth_success')));
            @endif

            refreshAllData();
        });
    </script>
</body>
</html>
