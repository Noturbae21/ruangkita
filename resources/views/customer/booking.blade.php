@extends('customer.layout')

@section('title', 'Pesan Ruang Meeting — RuangKita')

@section('hero')
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
@endsection

@section('content')
    <section class="space-y-6">

        <!-- Interactive 5-Step Progress Bar -->
        <div class="bg-white rounded-3xl border border-slate-200/90 p-4 sm:p-5 shadow-2xs">
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5" id="wizard-progress-bar">
                <button type="button" onclick="goToBookingStep(1)" id="wiz-nav-1"
                    class="flex items-center gap-2.5 p-2.5 rounded-2xl border-2 border-indigo-600 bg-indigo-50/70 text-left transition cursor-pointer">
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
                    <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-5 items-start">
                            <!-- Jumlah Peserta -->
                            <div class="md:col-span-5 space-y-2">
                                <div class="flex flex-wrap items-center justify-between gap-1.5">
                                    <label class="block text-xs font-extrabold text-slate-800 uppercase tracking-wider whitespace-nowrap">1. Jumlah Peserta</label>
                                    <div class="flex items-center gap-1">
                                        <button type="button" onclick="setParticipantsCount(2)" data-part-quick="2" class="part-quick-btn px-2 py-0.5 rounded-md text-[11px] font-bold bg-white border border-slate-200 hover:border-indigo-400 transition cursor-pointer">2</button>
                                        <button type="button" onclick="setParticipantsCount(4)" data-part-quick="4" class="part-quick-btn px-2 py-0.5 rounded-md text-[11px] font-bold bg-indigo-600 text-white border border-indigo-600 transition cursor-pointer">4</button>
                                        <button type="button" onclick="setParticipantsCount(8)" data-part-quick="8" class="part-quick-btn px-2 py-0.5 rounded-md text-[11px] font-bold bg-white border border-slate-200 hover:border-indigo-400 transition cursor-pointer">8</button>
                                        <button type="button" onclick="setParticipantsCount(12)" data-part-quick="12" class="part-quick-btn px-2 py-0.5 rounded-md text-[11px] font-bold bg-white border border-slate-200 hover:border-indigo-400 transition cursor-pointer">12</button>
                                        <button type="button" onclick="setParticipantsToRoomMax()" id="part-quick-max" title="Sesuaikan dengan kapasitas maksimal ruang terpilih" class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-100 transition cursor-pointer">Maks</button>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" onclick="adjustParticipants(-1)"
                                        class="w-10 h-10 rounded-xl border border-slate-300 bg-white hover:bg-slate-100 text-slate-700 font-extrabold text-base flex items-center justify-center transition cursor-pointer shrink-0">−</button>
                                    <input type="number" id="bf-participants" min="1" value="4" required oninput="onParticipantsInputChange()"
                                        class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-sm font-extrabold text-center text-slate-900 focus:border-indigo-600 focus:outline-none">
                                    <button type="button" onclick="adjustParticipants(1)"
                                        class="w-10 h-10 rounded-xl border border-slate-300 bg-white hover:bg-slate-100 text-slate-700 font-extrabold text-base flex items-center justify-center transition cursor-pointer shrink-0">+</button>
                                </div>
                                <p class="text-[11px] text-slate-500">Pilih jumlah peserta untuk rekomendasi ruang otomatis.</p>
                            </div>

                            <!-- Pilihan Cepat Dropdown & Filter Hanya yang Muat -->
                            <div class="md:col-span-7 space-y-2">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <label class="block text-xs font-extrabold text-slate-800 uppercase tracking-wider whitespace-nowrap">Ruang Meeting Terpilih</label>
                                    <label class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 cursor-pointer select-none">
                                        <input type="checkbox" id="filter-fit-participants" onchange="onFilterFitParticipantsChange()" class="w-3.5 h-3.5 rounded text-indigo-600 cursor-pointer">
                                        <span>Hanya tampilkan ruang yang muat</span>
                                    </label>
                                </div>
                                <select id="bf-room" required onchange="onBookingRoomOrDateChange()"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm font-bold text-slate-900 focus:border-indigo-600 focus:outline-none">
                                    <option value="">Memuat daftar ruang...</option>
                                </select>
                                <p id="bf-room-meta" class="text-[11px] text-slate-500 leading-relaxed"></p>
                            </div>
                        </div>

                        <!-- 1B. Tab Kategori Ruangan -->
                        <div class="pt-2 border-t border-slate-200/70">
                            <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                                <label class="block text-xs font-extrabold text-slate-800 uppercase tracking-wider">2. Filter Kategori Ruangan</label>
                                <div class="flex items-center gap-2">
                                    <span class="text-[11px] text-slate-500">Urutkan Katalog:</span>
                                    <select id="catalog-sort-by" onchange="onCatalogSortChange()"
                                        class="rounded-lg border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 focus:border-indigo-600 focus:outline-none">
                                        <option value="recommended">Rekomendasi Kapasitas</option>
                                        <option value="price_asc">Harga Terendah &rarr; Tertinggi</option>
                                        <option value="price_desc">Harga Tertinggi &rarr; Terendah</option>
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
                                <input type="date" id="bf-date" min="{{ now('Asia/Jakarta')->format('Y-m-d') }}" required onchange="onBookingRoomOrDateChange()"
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
                                <span class="inline-flex items-center gap-1.5 text-slate-500 font-semibold"><span class="w-3 h-3 rounded-sm bg-slate-300 inline-block"></span> Lewat</span>
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

                    <div class="space-y-4 max-w-xl">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Lengkap Pemesan / Nama Tim</label>
                            <input type="text" id="bf-name" value="{{ session('customer_name', '') }}" required oninput="updateWizardReviewSummary()" placeholder="Contoh: Rina Kartika / Tim Produk"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-900 focus:border-indigo-600 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kontak Pemesan (No. WhatsApp / Alamat Email)</label>
                            <input type="text" id="bf-contact" value="{{ session('customer_phone', session('customer_email', '')) }}" required oninput="updateWizardReviewSummary()" placeholder="Contoh: 081234567890"
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
@endsection

@section('scripts')
    <script>
        let state = {
            rooms: [],
            selectedCategory: 'ALL',
            bookingStep: 1,
            additionalFacilities: [],
            selectedAddons: {},
            lastAvailabilityData: null,
        };

        function timeToMinutes(t) {
            const [h, m] = (t || '00:00').split(':').map(Number);
            return h * 60 + m;
        }

        function minutesToTimeStr(mins) {
            const h = String(Math.floor(mins / 60)).padStart(2, '0');
            const m = String(mins % 60).padStart(2, '0');
            return `${h}:${m}`;
        }

        function getCurrentWibMinutes() {
            const parts = new Intl.DateTimeFormat('en-GB', {
                timeZone: 'Asia/Jakarta',
                hour: '2-digit',
                minute: '2-digit',
                hour12: false,
            }).formatToParts(new Date());
            const hh = Number(parts.find(p => p.type === 'hour')?.value || 0);
            const mm = Number(parts.find(p => p.type === 'minute')?.value || 0);
            return hh * 60 + mm;
        }

        function isSlotInPastWib(dateStr, startTimeStr) {
            if (!dateStr) return false;
            const today = getTodayWibString();
            if (dateStr < today) return true;
            if (dateStr > today) return false;
            return timeToMinutes(startTimeStr) <= getCurrentWibMinutes();
        }

        function formatPricingTypeLabel(type) {
            if (type === 'PER_HOUR') return '/ jam';
            if (type === 'PER_PARTICIPANT') return '/ peserta';
            return '/ booking';
        }

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
                if (isSlotInPastWib(date, start)) {
                    showAlert(
                        'error',
                        'Jam Sudah Terlewat (BR-06)',
                        `Jadwal ${date} pukul ${start} WIB sudah terlewat dari waktu saat ini. Silakan pilih jam yang belum lewat atau pilih tanggal Besok/Lusa.`
                    );
                    return false;
                }

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

        function sortRoomsList(roomsArray) {
            const participants = Math.max(1, Number(document.getElementById('bf-participants')?.value || 1));
            const sortBy = document.getElementById('catalog-sort-by')?.value || 'recommended';

            return [...roomsArray].sort((a, b) => {
                const priceA = Number(a.price_per_hour || 0);
                const priceB = Number(b.price_per_hour || 0);
                const capA = Number(a.capacity || 0);
                const capB = Number(b.capacity || 0);

                if (sortBy === 'price_asc') return priceA - priceB || capA - capB;
                if (sortBy === 'price_desc') return priceB - priceA || capB - capA;
                if (sortBy === 'capacity_asc') return capA - capB || priceA - priceB;
                if (sortBy === 'capacity_desc') return capB - capA || priceB - priceA;

                // Default 'recommended': active rooms that fit participants first, sorted by closest capacity fit
                const aFits = a.is_active && capA >= participants ? 1 : 0;
                const bFits = b.is_active && capB >= participants ? 1 : 0;
                if (aFits !== bFits) return bFits - aFits;
                return Math.abs(capA - participants) - Math.abs(capB - participants) || priceA - priceB;
            });
        }

        function getFilteredAndSortedRooms() {
            const participants = Math.max(1, Number(document.getElementById('bf-participants')?.value || 1));
            const onlyFit = document.getElementById('filter-fit-participants')?.checked || false;

            let list = state.rooms.filter(r => roomMatchesCategory(r, state.selectedCategory));
            if (onlyFit) {
                list = list.filter(r => Number(r.capacity || 0) >= participants);
            }
            return sortRoomsList(list);
        }

        function onCatalogSortChange() {
            populateBookingRoomOptions(true);
            renderRoomsCatalog();
            updatePricePreview();
            loadRoomAvailability();
        }

        function onFilterFitParticipantsChange() {
            populateBookingRoomOptions(false);
            renderRoomsCatalog();
            updatePricePreview();
            loadRoomAvailability();
        }

        function populateBookingRoomOptions(autoSelectFirstInCat = false) {
            const bfRoom = document.getElementById('bf-room');
            if (!bfRoom) return;
            const currentRoomId = bfRoom.value;
            bfRoom.innerHTML = '';

            const filteredRooms = getFilteredAndSortedRooms();
            const roomsToShow = filteredRooms.length ? filteredRooms : sortRoomsList(state.rooms);

            if (!roomsToShow.length) {
                bfRoom.innerHTML = '<option value="">Belum ada ruang meeting tersedia</option>';
                return;
            }

            roomsToShow.forEach(room => {
                const catInfo = getRoomCategoryInfo(room);
                const opt = document.createElement('option');
                opt.value = room.id;
                opt.textContent = `[${catInfo.label}] ${room.name} — Maks ${room.capacity} Kursi (${formatRupiah(room.price_per_hour)}/jam)${room.is_active ? '' : ' [Nonaktif]'}`;
                bfRoom.appendChild(opt);
            });

            if (!autoSelectFirstInCat && currentRoomId && roomsToShow.some(r => String(r.id) === String(currentRoomId))) {
                bfRoom.value = currentRoomId;
            } else {
                const firstActive = roomsToShow.find(r => r.is_active) || roomsToShow[0];
                if (firstActive) {
                    bfRoom.value = firstActive.id;
                }
            }
        }

        function populateTimeDropdowns() {
            const startSelect = document.getElementById('bf-start');
            const endSelect = document.getElementById('bf-end');
            const prevStart = startSelect.value || '09:00';
            const prevEnd = endSelect.value || '11:00';
            startSelect.innerHTML = '';
            endSelect.innerHTML = '';

            const selectedDate = document.getElementById('bf-date')?.value || getTodayWibString();
            const times = [];
            for (let h = 8; h <= 21; h++) {
                const hh = String(h).padStart(2, '0');
                times.push(`${hh}:00`);
                if (h < 21) {
                    times.push(`${hh}:30`);
                }
            }

            let firstValidStart = null;

            times.forEach(t => {
                if (t !== '21:00') {
                    const isPast = isSlotInPastWib(selectedDate, t);
                    // Jam mulai maksimal 20:00 agar minimal 1 jam sebelum tutup 21:00
                    const isTooLateFor1Hour = timeToMinutes(t) > 20 * 60;
                    const optStart = document.createElement('option');
                    optStart.value = t;
                    optStart.disabled = isPast || isTooLateFor1Hour;
                    optStart.textContent = isPast ? `${t} WIB (Sudah Lewat)` : `${t} WIB`;
                    if (!optStart.disabled && !firstValidStart) {
                        firstValidStart = t;
                    }
                    startSelect.appendChild(optStart);
                }
                if (t !== '08:00') {
                    const optEnd = document.createElement('option');
                    optEnd.value = t;
                    optEnd.textContent = t + ' WIB';
                    endSelect.appendChild(optEnd);
                }
            });

            if (firstValidStart) {
                const prevStartIsPast = isSlotInPastWib(selectedDate, prevStart) || timeToMinutes(prevStart) > 20 * 60;
                const chosenStart = prevStartIsPast ? firstValidStart : prevStart;
                startSelect.value = chosenStart;

                const startMins = timeToMinutes(chosenStart);
                const prevEndMins = timeToMinutes(prevEnd);
                const desiredEndMins = (prevEndMins >= startMins + 60 && prevEndMins <= startMins + 480 && prevEndMins <= 21 * 60)
                    ? prevEndMins
                    : Math.min(21 * 60, startMins + 120);
                endSelect.value = minutesToTimeStr(Math.max(startMins + 60, desiredEndMins));
            } else {
                startSelect.value = prevStart;
                endSelect.value = prevEnd;
            }
        }

        async function loadRooms() {
            const minCap = document.getElementById('filter-min-capacity')?.value || '';
            const url = minCap ? `/api/rooms?min_capacity=${encodeURIComponent(minCap)}` : '/api/rooms';

            const res = await fetch(url);
            const json = await res.json();
            state.rooms = json.data || [];

            const activeRooms = state.rooms.filter(r => r.is_active);
            const statEl = document.getElementById('stat-active-rooms');
            if (statEl) statEl.textContent = `${activeRooms.length} Ruang`;

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

                setTimeout(() => {
                    if (matchedRoom && matchedRoom.is_active) {
                        goToBookingStep(2);
                    }
                }, 50);
            }

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

        function syncParticipantsQuickButtons() {
            const current = Number(document.getElementById('bf-participants')?.value || 0);
            const bfRoom = document.getElementById('bf-room');
            const selectedRoom = state.rooms.find(r => String(r.id) === String(bfRoom?.value));

            document.querySelectorAll('.part-quick-btn').forEach(btn => {
                const val = Number(btn.getAttribute('data-part-quick'));
                if (val === current) {
                    btn.className = 'part-quick-btn px-2 py-0.5 rounded-md text-[11px] font-bold bg-indigo-600 text-white border border-indigo-600 transition cursor-pointer';
                } else {
                    btn.className = 'part-quick-btn px-2 py-0.5 rounded-md text-[11px] font-bold bg-white text-slate-700 border border-slate-200 hover:border-indigo-400 transition cursor-pointer';
                }
            });

            const maxBtn = document.getElementById('part-quick-max');
            if (maxBtn) {
                if (selectedRoom && current === Number(selectedRoom.capacity)) {
                    maxBtn.className = 'px-2 py-0.5 rounded-md text-[11px] font-bold bg-indigo-600 text-white border border-indigo-600 transition cursor-pointer';
                } else {
                    maxBtn.className = 'px-2 py-0.5 rounded-md text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-100 transition cursor-pointer';
                }
            }
        }

        function onParticipantsInputChange() {
            const participants = Math.max(1, Number(document.getElementById('bf-participants')?.value || 1));
            const bfRoom = document.getElementById('bf-room');
            const currentRoom = state.rooms.find(r => String(r.id) === String(bfRoom?.value));
            const sortBy = document.getElementById('catalog-sort-by')?.value || 'recommended';

            // Jika ruang saat ini tidak muat, atau sedang mode "Rekomendasi Kapasitas", pilihkan ruang yang paling pas
            if ((currentRoom && currentRoom.capacity < participants) || sortBy === 'recommended') {
                const candidateRooms = state.rooms
                    .filter(r => r.is_active && r.capacity >= participants && roomMatchesCategory(r, state.selectedCategory))
                    .sort((a, b) => a.capacity - b.capacity);

                const fallbackRooms = candidateRooms.length
                    ? candidateRooms
                    : state.rooms.filter(r => r.is_active && r.capacity >= participants).sort((a, b) => a.capacity - b.capacity);

                if (fallbackRooms.length > 0) {
                    const bestFitRoom = fallbackRooms[0];
                    if (state.selectedCategory !== 'ALL' && !roomMatchesCategory(bestFitRoom, state.selectedCategory)) {
                        state.selectedCategory = 'ALL';
                        selectRoomCategory('ALL');
                    }
                    bfRoom.value = bestFitRoom.id;
                    loadRoomAvailability();
                }
            }

            populateBookingRoomOptions(false);
            syncParticipantsQuickButtons();
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
            const bfRoom = document.getElementById('bf-room');
            const selectedRoom = state.rooms.find(r => String(r.id) === String(bfRoom?.value));
            if (selectedRoom) {
                const el = document.getElementById('bf-participants');
                if (el) {
                    el.value = selectedRoom.capacity;
                }
                syncParticipantsQuickButtons();
                updatePricePreview();
                renderRoomsCatalog();
                return;
            }

            const pool = state.rooms.filter(r => r.is_active && roomMatchesCategory(r, state.selectedCategory));
            const activeRooms = pool.length ? pool : state.rooms.filter(r => r.is_active);
            if (!activeRooms.length) return;

            const maxRoom = [...activeRooms].sort((a, b) => b.capacity - a.capacity)[0];
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
                catalogRooms = catalogRooms.filter(r => Number(r.capacity || 0) >= participants);
            }

            catalogRooms = sortRoomsList(catalogRooms);

            const summaryTextEl = document.getElementById('catalog-summary-text');
            if (summaryTextEl) {
                const fitCount = catalogRooms.filter(r => r.is_active && r.capacity >= participants).length;
                summaryTextEl.textContent = `Menampilkan ${catalogRooms.length} ruangan (${fitCount} ruangan muat untuk ${participants} peserta). Klik kartu untuk melihat simulasi kursi.`;
            }

            if (!catalogRooms.length) {
                grid.innerHTML = `
                    <div class="col-span-2 p-8 rounded-2xl border border-dashed border-slate-300 bg-slate-50 text-center space-y-2">
                        <p class="text-sm font-bold text-slate-700">Belum ada ruangan yang cocok dengan filter saat ini.</p>
                        <p class="text-xs text-slate-500">Admin dapat menambahkan ruang meeting melalui Dashboard Admin, atau coba ubah filter jumlah peserta.</p>
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
                            <div class="flex flex-wrap items-center justify-between gap-1.5">
                                <span class="px-2.5 py-0.5 rounded-lg border text-[10px] font-extrabold uppercase tracking-wider ${catInfo.badgeClass || 'bg-indigo-50 text-indigo-700 border-indigo-200'}">
                                    ${catInfo.label}
                                </span>
                                ${fitBadge}
                            </div>

                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="font-extrabold text-slate-900 text-base">${room.name}</h3>
                                    ${isSelectedRoom ? `<span class="px-2 py-0.5 rounded-md bg-indigo-600 text-white text-[10px] font-extrabold uppercase">✓ Terpilih</span>` : ''}
                                </div>
                                <p class="text-[11px] text-slate-500 mt-0.5">${catInfo.desc || ''}</p>
                            </div>

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

                            <div class="rounded-xl bg-slate-900 p-2.5 border border-slate-800">
                                <div class="flex items-center justify-between text-[10px] text-slate-300 px-1 pb-1">
                                    <span class="font-bold text-indigo-300">${layoutLabel}</span>
                                    <span class="text-emerald-400 font-semibold">${Math.min(participants, room.capacity)}/${room.capacity} kursi terisi</span>
                                </div>
                                <div class="h-28 w-full flex items-center justify-center">
                                    ${generateRoomBlueprintSvg(room, Math.min(participants, room.capacity), true)}
                                </div>
                            </div>

                            <div>
                                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Fasilitas Bawaan:</span>
                                <div class="flex flex-wrap gap-1">${facilitiesHtml}</div>
                            </div>
                        </div>

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
            populateTimeDropdowns();
            syncQuickDateButtons();
            syncParticipantsQuickButtons();
            renderRoomsCatalog();
            updatePricePreview();
            loadRoomAvailability();
        }

        function generateRoomBlueprintSvg(room, occupiedParticipants = 0, compact = false) {
            const lengthM = Number(room.length_m || 6.0);
            const widthM = Number(room.width_m || 4.0);
            const cap = Math.max(1, Number(room.capacity || 8));
            const occupied = Math.max(0, Math.min(cap, Number(occupiedParticipants || 0)));
            const layoutType = String(room.layout_type || 'BOARDROOM').toUpperCase();

            const W = 360;
            const H = 210;
            const rx = 38, ry = 26, rw = 284, rh = 152;

            const seats = [];
            let tableSvg = '';

            if (layoutType === 'U_SHAPE') {
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
                tableSvg = `
                    <rect x="108" y="72" width="150" height="60" rx="12" fill="#1e293b" stroke="#6366f1" stroke-width="1.8"/>
                    <rect x="118" y="82" width="130" height="40" rx="6" fill="#0f172a" stroke="#334155" stroke-width="1"/>
                    <text x="183" y="105" text-anchor="middle" fill="#94a3b8" font-size="8" font-weight="bold" font-family="monospace">MEJA RAPAT UTAMA</text>
                `;
                const sideSeats = Math.max(2, cap - 2);
                const topCount = Math.ceil(sideSeats / 2);
                const bottomCount = sideSeats - topCount;

                seats.push({ x: 93, y: 102 });
                for (let i = 0; i < topCount; i++) {
                    const x = 126 + (i * (114 / Math.max(1, topCount - 1 || 1)));
                    seats.push({ x, y: 58 });
                }
                for (let i = 0; i < bottomCount; i++) {
                    const x = 126 + (i * (114 / Math.max(1, bottomCount - 1 || 1)));
                    seats.push({ x, y: 146 });
                }
                if (seats.length < cap) {
                    seats.push({ x: 273, y: 102 });
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

                    <line x1="${rx}" y1="14" x2="${rx + rw}" y2="14" stroke="#818cf8" stroke-width="1.2" stroke-dasharray="3,2"/>
                    <line x1="${rx}" y1="9" x2="${rx}" y2="19" stroke="#818cf8" stroke-width="1.5"/>
                    <line x1="${rx + rw}" y1="9" x2="${rx + rw}" y2="19" stroke="#818cf8" stroke-width="1.5"/>
                    <rect x="${rx + rw / 2 - 44}" y="5" width="88" height="16" rx="4" fill="#0f172a"/>
                    <text x="${rx + rw / 2}" y="16" text-anchor="middle" fill="#a5b4fc" font-size="9" font-weight="bold" font-family="monospace">PANJANG ${lengthM}m</text>

                    <line x1="16" y1="${ry}" x2="16" y2="${ry + rh}" stroke="#818cf8" stroke-width="1.2" stroke-dasharray="3,2"/>
                    <line x1="11" y1="${ry}" x2="21" y2="${ry}" stroke="#818cf8" stroke-width="1.5"/>
                    <line x1="11" y1="${ry + rh}" x2="21" y2="${ry + rh}" stroke="#818cf8" stroke-width="1.5"/>
                    <g transform="translate(15, ${ry + rh / 2}) rotate(-90)">
                        <rect x="-36" y="-8" width="72" height="15" rx="4" fill="#0f172a"/>
                        <text x="0" y="3" text-anchor="middle" fill="#a5b4fc" font-size="8.5" font-weight="bold" font-family="monospace">LEBAR ${widthM}m</text>
                    </g>

                    <rect x="${rx}" y="${ry}" width="${rw}" height="${rh}" rx="8" fill="url(#grid-${room.id}-${compact ? 'c' : 'f'})" stroke="#6366f1" stroke-width="2.2"/>

                    <rect x="${rx + 4}" y="${ry + 36}" width="7" height="80" rx="2" fill="#38bdf8"/>
                    <text x="${rx + 17}" y="${ry + 78}" fill="#38bdf8" font-size="7" font-weight="bold" font-family="monospace" transform="rotate(-90, ${rx + 17}, ${ry + 78})">TV / PROYEKTOR</text>

                    <rect x="${rx + rw - 58}" y="${ry + 3}" width="42" height="6" rx="2" fill="#94a3b8"/>
                    <text x="${rx + rw - 37}" y="${ry + 16}" text-anchor="middle" fill="#64748b" font-size="6.5" font-family="monospace">AC</text>

                    <line x1="${rx + rw - 44}" y1="${ry + rh}" x2="${rx + rw - 14}" y2="${ry + rh}" stroke="#0f172a" stroke-width="3"/>
                    <path d="M ${rx + rw - 44} ${ry + rh} A 26 26 0 0 1 ${rx + rw - 18} ${ry + rh - 24} L ${rx + rw - 18} ${ry + rh}" fill="none" stroke="#fbbf24" stroke-width="1.2" stroke-dasharray="2,2"/>
                    <text x="${rx + rw - 29}" y="${ry + rh + 11}" text-anchor="middle" fill="#fbbf24" font-size="7.5" font-weight="bold" font-family="monospace">PINTU</text>

                    ${tableSvg}
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

            const dateStr = document.getElementById('bf-date')?.value || getTodayWibString();
            const isPastTime = isSlotInPastWib(dateStr, start);

            if (isPastTime) {
                durationEl.className = 'font-bold text-rose-600';
                hintEl.textContent = `⚠ Jam mulai (${start} WIB) pada tanggal ${dateStr} sudah terlewat. Silakan pilih jam setelah waktu sekarang atau pilih tanggal Besok.`;
                hintEl.className = 'text-[11px] text-rose-600 font-semibold';
            } else if (diffMinutes < 60 || diffMinutes > 480) {
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

            const selectedDate = document.getElementById('bf-date')?.value || data.date || getTodayWibString();
            const selectedStart = timeToMinutes(document.getElementById('bf-start')?.value || '09:00');
            const selectedEnd = timeToMinutes(document.getElementById('bf-end')?.value || '11:00');

            slotsGrid.innerHTML = (data.slots || []).map(slot => {
                const slotStartMins = timeToMinutes(slot.start_time);
                const isPast = isSlotInPastWib(selectedDate, slot.start_time);
                const isSelectedSlot = !isPast && slot.is_available && slotStartMins >= selectedStart && slotStartMins < selectedEnd;

                if (isPast) {
                    return `
                        <div title="Waktu sudah terlewat (${slot.start_time} - ${slot.end_time} WIB)"
                            class="px-2 py-2 rounded-xl border border-slate-200 bg-slate-100 text-slate-400 text-center opacity-75 cursor-not-allowed select-none">
                            <span class="block font-mono-code text-xs font-bold line-through">${slot.start_time}</span>
                            <span class="block text-[10px] font-semibold">Lewat</span>
                        </div>
                    `;
                } else if (isSelectedSlot) {
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

                const today = getTodayWibString();
                const nowMins = getCurrentWibMinutes();
                // Round up current WIB time to next 30-minute boundary
                const nextHalfHourMins = Math.ceil((nowMins + 1) / 30) * 30;

                const futureFreeSlots = (data.available_slots || []).map(s => {
                    if (date < today) return null;
                    if (date > today) return s;
                    const sStart = Math.max(timeToMinutes(s.start_time), nextHalfHourMins);
                    const sEnd = timeToMinutes(s.end_time);
                    if (sEnd - sStart < 60) return null;
                    return {
                        ...s,
                        start_time: minutesToTimeStr(sStart),
                        duration_minutes: sEnd - sStart,
                    };
                }).filter(Boolean);

                if (!futureFreeSlots.length) {
                    freeContainer.innerHTML = date === today && nowMins >= 20 * 60
                        ? `<div class="space-y-1.5"><p class="text-amber-800 font-semibold">Jam operasional hari ini (08:00–21:00 WIB) sudah berakhir.</p><button type="button" onclick="setBookingQuickDate(1)" class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-xs font-bold cursor-pointer">Pilih Jadwal Besok &rarr;</button></div>`
                        : '<p class="text-rose-700 font-semibold">Tidak ada slot kosong yang masih bisa dipesan pada tanggal ini.</p>';
                } else {
                    freeContainer.innerHTML = futureFreeSlots.map(s => `
                        <button type="button" onclick="pickSlotStartTime('${s.start_time}')"
                            class="w-full flex items-center justify-between bg-white/90 hover:bg-emerald-600 hover:text-white px-3 py-2 rounded-xl border border-emerald-200/80 transition cursor-pointer group">
                            <span class="font-mono-code font-bold text-emerald-900 group-hover:text-white">${s.start_time} – ${s.end_time} WIB</span>
                            <span class="text-[11px] text-emerald-700 group-hover:text-emerald-100 font-semibold">${(s.duration_minutes / 60)} jam tersedia · Pilih &rarr;</span>
                        </button>
                    `).join('');
                }

                if (!data.booked_slots || !data.booked_slots.length) {
                    bookedContainer.innerHTML = '<p class="text-slate-500">Belum ada booking terkonfirmasi pada tanggal ini.</p>';
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
            const selectedDate = document.getElementById('bf-date')?.value || getTodayWibString();
            if (isSlotInPastWib(selectedDate, startTime)) {
                showAlert('warning', 'Slot Waktu Sudah Lewat', `Pukul ${startTime} WIB pada ${selectedDate} sudah terlewat dan tidak dapat dipesan.`);
                return;
            }
            if (timeToMinutes(startTime) > 20 * 60) {
                showAlert('warning', 'Durasi Minimal 1 Jam', 'Jam mulai maksimal pukul 20:00 WIB karena jam operasional tutup pukul 21:00 WIB.');
                return;
            }
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
                user_id: loggedInCustomer?.id || null,
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
                    document.getElementById('bf-name').value = loggedInCustomer?.name || '';
                    document.getElementById('bf-contact').value = loggedInCustomer?.phone || loggedInCustomer?.email || '';
                    state.selectedAddons = {};
                    renderBookingAddonsList();
                    goToBookingStep(1);
                    await loadRoomAvailability();
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

        async function onQrisPaymentCompleted() {
            await loadRoomAvailability();
        }

        document.addEventListener('DOMContentLoaded', async () => {
            const today = getTodayWibString();
            const nowMins = getCurrentWibMinutes();
            const dateInput = document.getElementById('bf-date');
            dateInput.min = today;
            // Jika sudah melewati jam 20:00 WIB (jam operasional hari ini sudah tidak cukup untuk min. 1 jam sewa),
            // tetap bisa melihat Hari Ini sebagai "Lewat", namun default awal diarahkan ke Hari Ini atau sesuai query URL
            dateInput.value = today;

            populateTimeDropdowns();
            syncQuickDateButtons();

            await Promise.all([
                loadRooms(),
                loadAdditionalFacilities(),
            ]);
        });
    </script>
@endsection
