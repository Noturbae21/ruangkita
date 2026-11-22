<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Admin — RuangKita Coworking Space</title>

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
<body class="bg-slate-100 text-slate-900 min-h-screen flex flex-col antialiased">

    <!-- Top Admin Strip -->
    <div class="h-1.5 w-full bg-gradient-to-r from-amber-500 via-indigo-600 to-emerald-500"></div>

    <!-- Admin Navbar -->
    <header class="sticky top-0 z-30 bg-slate-900 text-white border-b border-slate-800 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white font-extrabold shadow-md">
                    A
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-lg font-extrabold tracking-tight text-white">RuangKita Admin</span>
                        <span class="text-[11px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-400/30">
                            Admin Coworking
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 hidden sm:block">Login sebagai: {{ $adminName }} ({{ $adminEmail }})</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}"
                    class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-xs font-bold text-slate-200 transition">
                    Lihat Halaman Pemesan &rarr;
                </a>

                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit"
                        class="px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-xs font-bold text-white transition cursor-pointer">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1 space-y-6">

        <!-- Admin KPI Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Ruang Meeting</p>
                <p id="adm-stat-rooms" class="text-2xl font-extrabold text-slate-900 mt-1">-</p>
                <p id="adm-stat-rooms-sub" class="text-xs text-emerald-600 font-semibold mt-0.5">Memuat...</p>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Booking CONFIRMED</p>
                <p id="adm-stat-confirmed" class="text-2xl font-extrabold text-indigo-700 mt-1">-</p>
                <p id="adm-stat-cancelled" class="text-xs text-slate-500 font-medium mt-0.5">- dibatalkan</p>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Nilai Booking Aktif</p>
                <p id="adm-stat-revenue" class="text-2xl font-extrabold text-emerald-700 font-mono-code mt-1">Rp 0</p>
                <p class="text-xs text-slate-500 mt-0.5">Dari booking berstatus CONFIRMED</p>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Denda Pembatalan</p>
                <p id="adm-stat-penalty" class="text-2xl font-extrabold text-amber-600 font-mono-code mt-1">Rp 0</p>
                <p class="text-xs text-slate-500 mt-0.5">Akumulasi denda 50% (&lt; 2 jam)</p>
            </div>
        </div>

        <!-- Notification Alert -->
        <div id="adm-alert-box" class="hidden rounded-2xl p-4 border shadow-xs"></div>

        <!-- Admin Navigation Tabs -->
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-200 pb-4">
            <nav class="flex flex-wrap gap-2">
                <button onclick="switchAdminTab('rooms')" id="adm-tab-btn-rooms"
                    class="px-4 py-2.5 rounded-xl text-sm font-bold transition bg-slate-900 text-white shadow-xs cursor-pointer">
                    1. Manajemen Ruang (CRUD Ruang)
                </button>
                <button onclick="switchAdminTab('addons')" id="adm-tab-btn-addons"
                    class="px-4 py-2.5 rounded-xl text-sm font-bold transition bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 cursor-pointer">
                    2. Master Fasilitas Tambahan (Add-on)
                </button>
                <button onclick="switchAdminTab('bookings')" id="adm-tab-btn-bookings"
                    class="px-4 py-2.5 rounded-xl text-sm font-bold transition bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 cursor-pointer">
                    3. Kelola Seluruh Booking &amp; Pembatalan
                </button>
                <button onclick="switchAdminTab('calendar')" id="adm-tab-btn-calendar"
                    class="px-4 py-2.5 rounded-xl text-sm font-bold transition bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 cursor-pointer">
                    4. Kalender Operasional Seluruh Ruang
                </button>
            </nav>

            <button onclick="refreshAdminData()" class="px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-xs font-bold text-slate-700 transition cursor-pointer">
                Muat Ulang Data
            </button>
        </div>

        <!-- ADMIN TAB 1: CRUD RUANG (FR-01 to FR-04) -->
        <section id="adm-tab-rooms" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <div class="lg:col-span-4 bg-white rounded-3xl border border-slate-200/90 shadow-2xs p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">FR-01 &amp; FR-03</span>
                        <h2 id="rf-title" class="text-lg font-extrabold text-slate-900">Tambah Ruang Baru</h2>
                    </div>
                    <button type="button" id="rf-cancel-btn" onclick="resetRoomForm()" class="hidden text-xs font-bold text-rose-600 hover:underline cursor-pointer">
                        Batal Edit
                    </button>
                </div>

                <form id="room-form" onsubmit="submitRoomForm(event)" class="space-y-4">
                    <input type="hidden" id="rf-id" value="">

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Ruang</label>
                        <input type="text" id="rf-name" required placeholder="Contoh: Ruang Borobudur"
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-medium focus:border-indigo-600 focus:outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kapasitas (Orang)</label>
                            <input type="number" id="rf-capacity" min="1" required placeholder="12"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-medium focus:border-indigo-600 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Harga / Jam (Rp)</label>
                            <input type="number" id="rf-price" min="0" step="5000" required placeholder="200000"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-medium focus:border-indigo-600 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Daftar Fasilitas (Pisahkan koma)</label>
                        <input type="text" id="rf-facilities" placeholder="Smart TV, Proyektor, WiFi, Whiteboard, AC"
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-medium focus:border-indigo-600 focus:outline-none">
                    </div>

                    <div class="flex items-center gap-2.5 pt-1">
                        <input type="checkbox" id="rf-active" checked class="w-4 h-4 rounded text-indigo-600">
                        <label for="rf-active" class="text-sm font-semibold text-slate-700">Status Ruang Aktif</label>
                    </div>

                    <button type="submit"
                        class="w-full py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-sm transition cursor-pointer">
                        Simpan Data Ruang
                    </button>
                </form>

                <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-900 space-y-1">
                    <p class="font-bold">Proteksi FR-04:</p>
                    <p class="leading-relaxed">Ruang yang masih memiliki booking mendatang tidak akan terhapus permanen, melainkan otomatis dinonaktifkan.</p>
                </div>
            </div>

            <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200/90 shadow-2xs p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">FR-02 &amp; FR-04</span>
                        <h2 class="text-lg font-extrabold text-slate-900">Daftar Ruang Meeting Coworking</h2>
                    </div>
                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 text-xs font-mono-code">GET / POST / PUT / DELETE /api/rooms</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-500 bg-slate-50">
                                <th class="py-3 px-4">ID</th>
                                <th class="py-3 px-4">Nama Ruang</th>
                                <th class="py-3 px-4">Kapasitas</th>
                                <th class="py-3 px-4">Tarif / Jam</th>
                                <th class="py-3 px-4">Fasilitas</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Aksi Admin</th>
                            </tr>
                        </thead>
                        <tbody id="adm-rooms-tbody" class="divide-y divide-slate-200/70 text-sm"></tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- ADMIN TAB 1.5: MASTER FASILITAS TAMBAHAN (ADD-ONS) -->
        <section id="adm-tab-addons" class="hidden grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <div class="lg:col-span-4 bg-white rounded-3xl border border-slate-200/90 shadow-2xs p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">Master Add-on</span>
                        <h2 id="af-title" class="text-lg font-extrabold text-slate-900">Tambah Fasilitas Tambahan</h2>
                    </div>
                    <button type="button" id="af-cancel-btn" onclick="resetAddonForm()" class="hidden text-xs font-bold text-rose-600 hover:underline cursor-pointer">
                        Batal Edit
                    </button>
                </div>

                <form id="addon-form" onsubmit="submitAddonForm(event)" class="space-y-4">
                    <input type="hidden" id="af-id" value="">

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Fasilitas Tambahan</label>
                        <input type="text" id="af-name" required placeholder="Contoh: Paket Coffee Break & Snack"
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-medium focus:border-indigo-600 focus:outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tarif (Rp)</label>
                            <input type="number" id="af-price" min="0" step="1000" required placeholder="35000"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-medium focus:border-indigo-600 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Skema Tarif</label>
                            <select id="af-pricing-type" required
                                class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm font-medium focus:border-indigo-600 focus:outline-none">
                                <option value="PER_BOOKING">Per Booking (Flat)</option>
                                <option value="PER_HOUR">Per Jam Sewa</option>
                                <option value="PER_PARTICIPANT">Per Peserta</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Deskripsi Singkat</label>
                        <input type="text" id="af-description" placeholder="Keterangan fasilitas tambahan..."
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-medium focus:border-indigo-600 focus:outline-none">
                    </div>

                    <div class="flex items-center gap-2.5 pt-1">
                        <input type="checkbox" id="af-active" checked class="w-4 h-4 rounded text-indigo-600">
                        <label for="af-active" class="text-sm font-semibold text-slate-700">Status Fasilitas Aktif</label>
                    </div>

                    <button type="submit"
                        class="w-full py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-sm transition cursor-pointer">
                        Simpan Fasilitas Tambahan
                    </button>
                </form>

                <div class="p-3.5 rounded-2xl bg-indigo-50 border border-indigo-200 text-xs text-indigo-900 space-y-1">
                    <p class="font-bold">Skema Perhitungan Otomatis:</p>
                    <p class="leading-relaxed">• <strong>Per Booking</strong>: Tarif × Jumlah Unit<br>• <strong>Per Jam</strong>: Tarif × Durasi (Jam) × Jumlah Unit<br>• <strong>Per Peserta</strong>: Tarif × Jumlah Peserta</p>
                </div>
            </div>

            <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200/90 shadow-2xs p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">Katalog Add-on</span>
                        <h2 class="text-lg font-extrabold text-slate-900">Daftar Master Fasilitas Tambahan</h2>
                    </div>
                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 text-xs font-mono-code">CRUD /api/additional-facilities</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-500 bg-slate-50">
                                <th class="py-3 px-4">ID</th>
                                <th class="py-3 px-4">Nama &amp; Deskripsi</th>
                                <th class="py-3 px-4">Tarif</th>
                                <th class="py-3 px-4">Skema</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Aksi Admin</th>
                            </tr>
                        </thead>
                        <tbody id="adm-addons-tbody" class="divide-y divide-slate-200/70 text-sm"></tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- ADMIN TAB 2: KELOLA SELURUH BOOKING -->
        <section id="adm-tab-bookings" class="hidden bg-white rounded-3xl border border-slate-200/90 shadow-2xs p-6 space-y-5">
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-4">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">Manajemen Booking Admin</span>
                    <h2 class="text-xl font-extrabold text-slate-900">Seluruh Data Booking Pemesan</h2>
                </div>

                <div class="flex flex-wrap items-center gap-2.5">
                    <select id="adm-bl-status" onchange="loadAdminBookings()"
                        class="rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700">
                        <option value="">Semua Status</option>
                        <option value="CONFIRMED">CONFIRMED</option>
                        <option value="CANCELLED">CANCELLED</option>
                    </select>

                    <select id="adm-bl-room" onchange="loadAdminBookings()"
                        class="rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700">
                        <option value="">Semua Ruang</option>
                    </select>

                    <input type="text" id="adm-bl-search" oninput="loadAdminBookings()" placeholder="Cari nama pemesan..."
                        class="rounded-xl border border-slate-300 bg-slate-50 px-3.5 py-2 text-xs font-medium text-slate-800">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-500 bg-slate-50">
                            <th class="py-3.5 px-4">ID</th>
                            <th class="py-3.5 px-4">Ruang</th>
                            <th class="py-3.5 px-4">Pemesan &amp; Kontak</th>
                            <th class="py-3.5 px-4">Jadwal (WIB)</th>
                            <th class="py-3.5 px-4">Peserta</th>
                            <th class="py-3.5 px-4">Total Harga</th>
                            <th class="py-3.5 px-4">Status &amp; Denda</th>
                            <th class="py-3.5 px-4 text-right">Tindakan Admin</th>
                        </tr>
                    </thead>
                    <tbody id="adm-bookings-tbody" class="divide-y divide-slate-200/70 text-sm"></tbody>
                </table>
            </div>
        </section>

        <!-- ADMIN TAB 3: KALENDER OPERASIONAL -->
        <section id="adm-tab-calendar" class="hidden bg-white rounded-3xl border border-slate-200/90 shadow-2xs p-6 space-y-5">
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-4">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">FR-11 · Monitoring Jadwal</span>
                    <h2 class="text-xl font-extrabold text-slate-900">Kalender Operasional Seluruh Ruang</h2>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <select id="adm-cal-room" onchange="loadAdminCalendar()"
                        class="rounded-xl border border-slate-300 bg-slate-50 px-3.5 py-2 text-xs font-semibold text-slate-800">
                        <option value="">Semua Ruang</option>
                    </select>
                    <input type="date" id="adm-cal-date" onchange="loadAdminCalendar()"
                        class="rounded-xl border border-slate-300 bg-slate-50 px-3.5 py-2 text-xs font-semibold text-slate-800">
                    <select id="adm-cal-view" onchange="loadAdminCalendar()"
                        class="rounded-xl border border-slate-300 bg-slate-50 px-3.5 py-2 text-xs font-semibold text-slate-800">
                        <option value="day">Tampilan Harian</option>
                        <option value="week">Tampilan Mingguan (7 Hari)</option>
                    </select>
                </div>
            </div>

            <div id="adm-calendar-container" class="space-y-6"></div>
        </section>

    </main>

    <script>
        let adminState = { rooms: [], addons: [], bookings: [] };

        function formatPricingSchemeBadge(type) {
            if (type === 'PER_HOUR') return '<span class="px-2 py-0.5 rounded-md bg-sky-50 text-sky-700 border border-sky-200 text-[11px] font-bold">Per Jam</span>';
            if (type === 'PER_PARTICIPANT') return '<span class="px-2 py-0.5 rounded-md bg-violet-50 text-violet-700 border border-violet-200 text-[11px] font-bold">Per Peserta</span>';
            return '<span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 border border-slate-200 text-[11px] font-bold">Per Booking</span>';
        }

        function formatRupiah(amount) {
            return 'Rp ' + Number(amount || 0).toLocaleString('id-ID');
        }

        function getTodayWibString() {
            return new Intl.DateTimeFormat('en-CA', {
                timeZone: 'Asia/Jakarta',
                year: 'numeric',
                month: '2-digit',
                day: '2-digit'
            }).format(new Date());
        }

        function showAdminAlert(type, title, message, status = null) {
            const box = document.getElementById('adm-alert-box');
            const colors = {
                success: 'bg-emerald-50 border-emerald-300 text-emerald-950',
                warning: 'bg-amber-50 border-amber-300 text-amber-950',
                error: 'bg-rose-50 border-rose-300 text-rose-950',
            };
            box.className = `rounded-2xl p-4 border shadow-xs flex items-start justify-between gap-4 ${colors[type] || colors.success}`;
            box.innerHTML = `
                <div>
                    <p class="font-extrabold text-sm">${status ? `[HTTP ${status}] ` : ''}${title}</p>
                    <p class="text-xs mt-0.5">${message}</p>
                </div>
                <button onclick="document.getElementById('adm-alert-box').classList.add('hidden')" class="text-xs font-bold cursor-pointer">✕</button>
            `;
            box.classList.remove('hidden');
        }

        function switchAdminTab(tab) {
            ['rooms', 'addons', 'bookings', 'calendar'].forEach(t => {
                const sec = document.getElementById(`adm-tab-${t}`);
                const btn = document.getElementById(`adm-tab-btn-${t}`);
                if (t === tab) {
                    sec.classList.remove('hidden');
                    btn.className = 'px-4 py-2.5 rounded-xl text-sm font-bold transition bg-slate-900 text-white shadow-xs cursor-pointer';
                } else {
                    sec.classList.add('hidden');
                    btn.className = 'px-4 py-2.5 rounded-xl text-sm font-bold transition bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 cursor-pointer';
                }
            });
            if (tab === 'calendar') loadAdminCalendar();
        }

        async function loadAdminRooms() {
            const res = await fetch('/api/rooms');
            const json = await res.json();
            adminState.rooms = json.data || [];

            const activeCount = adminState.rooms.filter(r => r.is_active).length;
            document.getElementById('adm-stat-rooms').textContent = `${adminState.rooms.length} Ruang`;
            document.getElementById('adm-stat-rooms-sub').textContent = `${activeCount} Aktif • ${adminState.rooms.length - activeCount} Nonaktif`;

            const roomSelects = ['adm-bl-room', 'adm-cal-room'];
            roomSelects.forEach(id => {
                const el = document.getElementById(id);
                const cur = el.value;
                el.innerHTML = '<option value="">Semua Ruang</option>';
                adminState.rooms.forEach(r => {
                    const opt = document.createElement('option');
                    opt.value = r.id;
                    opt.textContent = r.name;
                    el.appendChild(opt);
                });
                el.value = cur;
            });

            const tbody = document.getElementById('adm-rooms-tbody');
            tbody.innerHTML = adminState.rooms.map(r => `
                <tr class="hover:bg-slate-50 transition">
                    <td class="py-3.5 px-4 font-mono-code text-xs font-bold text-slate-500">#${r.id}</td>
                    <td class="py-3.5 px-4 font-bold text-slate-900">${r.name}</td>
                    <td class="py-3.5 px-4 text-xs font-semibold">${r.capacity} orang</td>
                    <td class="py-3.5 px-4 font-mono-code text-xs font-bold text-indigo-700">${formatRupiah(r.price_per_hour)}</td>
                    <td class="py-3.5 px-4 text-xs text-slate-600">${(r.facilities || []).join(', ')}</td>
                    <td class="py-3.5 px-4">
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold ${r.is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'}">
                            ${r.is_active ? 'AKTIF' : 'NONAKTIF'}
                        </span>
                    </td>
                    <td class="py-3.5 px-4 text-right space-x-1.5">
                        <button onclick="editRoom(${r.id})" class="px-3 py-1 rounded-lg bg-slate-100 hover:bg-indigo-600 text-slate-700 hover:text-white text-xs font-bold transition cursor-pointer">Edit</button>
                        <button onclick="deleteOrDeactivateRoom(${r.id})" class="px-3 py-1 rounded-lg bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white text-xs font-bold transition cursor-pointer">Hapus</button>
                    </td>
                </tr>
            `).join('');
        }

        function editRoom(id) {
            const room = adminState.rooms.find(r => r.id === id);
            if (!room) return;
            document.getElementById('rf-id').value = room.id;
            document.getElementById('rf-name').value = room.name;
            document.getElementById('rf-capacity').value = room.capacity;
            document.getElementById('rf-price').value = room.price_per_hour;
            document.getElementById('rf-facilities').value = (room.facilities || []).join(', ');
            document.getElementById('rf-active').checked = !!room.is_active;
            document.getElementById('rf-title').textContent = `Edit Ruang #${room.id}`;
            document.getElementById('rf-cancel-btn').classList.remove('hidden');
        }

        function resetRoomForm() {
            document.getElementById('rf-id').value = '';
            document.getElementById('rf-name').value = '';
            document.getElementById('rf-capacity').value = '';
            document.getElementById('rf-price').value = '';
            document.getElementById('rf-facilities').value = '';
            document.getElementById('rf-active').checked = true;
            document.getElementById('rf-title').textContent = 'Tambah Ruang Baru';
            document.getElementById('rf-cancel-btn').classList.add('hidden');
        }

        async function submitRoomForm(e) {
            e.preventDefault();
            const id = document.getElementById('rf-id').value;
            const facilities = document.getElementById('rf-facilities').value.split(',').map(s => s.trim()).filter(Boolean);
            const payload = {
                name: document.getElementById('rf-name').value,
                capacity: Number(document.getElementById('rf-capacity').value),
                price_per_hour: Number(document.getElementById('rf-price').value),
                facilities,
                is_active: document.getElementById('rf-active').checked,
            };

            const res = await fetch(id ? `/api/rooms/${id}` : '/api/rooms', {
                method: id ? 'PUT' : 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify(payload),
            });
            const json = await res.json();

            if (res.ok) {
                showAdminAlert('success', id ? 'Ruang Diperbarui' : 'Ruang Ditambahkan', json.message, res.status);
                resetRoomForm();
                refreshAdminData();
            } else {
                showAdminAlert('error', 'Gagal Menyimpan Ruang', json.message, res.status);
            }
        }

        async function deleteOrDeactivateRoom(id) {
            const room = adminState.rooms.find(r => r.id === id);
            if (!confirm(`Hapus atau nonaktifkan "${room ? room.name : id}"?`)) return;

            const res = await fetch(`/api/rooms/${id}`, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json' },
            });
            const json = await res.json();

            if (res.ok) {
                showAdminAlert(
                    json.action === 'deactivated' ? 'warning' : 'success',
                    json.action === 'deactivated' ? 'Ruang Dinonaktifkan (FR-04)' : 'Ruang Dihapus Permanen',
                    json.message,
                    res.status
                );
                refreshAdminData();
            } else {
                showAdminAlert('error', 'Gagal Menghapus Ruang', json.message, res.status);
            }
        }

        async function loadAdminAddons() {
            const res = await fetch('/api/additional-facilities');
            const json = await res.json();
            adminState.addons = json.data || [];

            const tbody = document.getElementById('adm-addons-tbody');
            if (!tbody) return;

            if (!adminState.addons.length) {
                tbody.innerHTML = '<tr><td colspan="6" class="py-8 text-center text-slate-400">Belum ada data fasilitas tambahan.</td></tr>';
                return;
            }

            tbody.innerHTML = adminState.addons.map(a => `
                <tr class="hover:bg-slate-50 transition">
                    <td class="py-3.5 px-4 font-mono-code text-xs font-bold text-slate-500">#${a.id}</td>
                    <td class="py-3.5 px-4">
                        <p class="font-bold text-slate-900">${a.name}</p>
                        ${a.description ? `<p class="text-xs text-slate-500">${a.description}</p>` : ''}
                    </td>
                    <td class="py-3.5 px-4 font-mono-code text-xs font-bold text-indigo-700">${formatRupiah(a.price)}</td>
                    <td class="py-3.5 px-4">${formatPricingSchemeBadge(a.pricing_type)}</td>
                    <td class="py-3.5 px-4">
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold ${a.is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'}">
                            ${a.is_active ? 'AKTIF' : 'NONAKTIF'}
                        </span>
                    </td>
                    <td class="py-3.5 px-4 text-right space-x-1.5">
                        <button onclick="editAddon(${a.id})" class="px-3 py-1 rounded-lg bg-slate-100 hover:bg-indigo-600 text-slate-700 hover:text-white text-xs font-bold transition cursor-pointer">Edit</button>
                        <button onclick="deleteOrDeactivateAddon(${a.id})" class="px-3 py-1 rounded-lg bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white text-xs font-bold transition cursor-pointer">Hapus</button>
                    </td>
                </tr>
            `).join('');
        }

        function editAddon(id) {
            const addon = adminState.addons.find(a => a.id === id);
            if (!addon) return;
            document.getElementById('af-id').value = addon.id;
            document.getElementById('af-name').value = addon.name;
            document.getElementById('af-price').value = addon.price;
            document.getElementById('af-pricing-type').value = addon.pricing_type || 'PER_BOOKING';
            document.getElementById('af-description').value = addon.description || '';
            document.getElementById('af-active').checked = !!addon.is_active;
            document.getElementById('af-title').textContent = `Edit Fasilitas #${addon.id}`;
            document.getElementById('af-cancel-btn').classList.remove('hidden');
        }

        function resetAddonForm() {
            document.getElementById('af-id').value = '';
            document.getElementById('af-name').value = '';
            document.getElementById('af-price').value = '';
            document.getElementById('af-pricing-type').value = 'PER_BOOKING';
            document.getElementById('af-description').value = '';
            document.getElementById('af-active').checked = true;
            document.getElementById('af-title').textContent = 'Tambah Fasilitas Tambahan';
            document.getElementById('af-cancel-btn').classList.add('hidden');
        }

        async function submitAddonForm(e) {
            e.preventDefault();
            const id = document.getElementById('af-id').value;
            const payload = {
                name: document.getElementById('af-name').value,
                price: Number(document.getElementById('af-price').value),
                pricing_type: document.getElementById('af-pricing-type').value,
                description: document.getElementById('af-description').value,
                is_active: document.getElementById('af-active').checked,
            };

            const res = await fetch(id ? `/api/additional-facilities/${id}` : '/api/additional-facilities', {
                method: id ? 'PUT' : 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify(payload),
            });
            const json = await res.json();

            if (res.ok) {
                showAdminAlert('success', id ? 'Fasilitas Tambahan Diperbarui' : 'Fasilitas Tambahan Ditambahkan', json.message, res.status);
                resetAddonForm();
                refreshAdminData();
            } else {
                showAdminAlert('error', 'Gagal Menyimpan Fasilitas Tambahan', json.message, res.status);
            }
        }

        async function deleteOrDeactivateAddon(id) {
            const addon = adminState.addons.find(a => a.id === id);
            if (!confirm(`Hapus atau nonaktifkan fasilitas tambahan "${addon ? addon.name : id}"?`)) return;

            const res = await fetch(`/api/additional-facilities/${id}`, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json' },
            });
            const json = await res.json();

            if (res.ok) {
                showAdminAlert(
                    json.action === 'deactivated' ? 'warning' : 'success',
                    json.action === 'deactivated' ? 'Fasilitas Tambahan Dinonaktifkan' : 'Fasilitas Tambahan Dihapus Permanen',
                    json.message,
                    res.status
                );
                refreshAdminData();
            } else {
                showAdminAlert('error', 'Gagal Menghapus Fasilitas Tambahan', json.message, res.status);
            }
        }

        async function loadAdminBookings() {
            const status = document.getElementById('adm-bl-status').value;
            const roomId = document.getElementById('adm-bl-room').value;
            const search = document.getElementById('adm-bl-search').value;

            const params = new URLSearchParams();
            if (status) params.set('status', status);
            if (roomId) params.set('room_id', roomId);
            if (search) params.set('booker_name', search);

            const res = await fetch(`/api/bookings?${params.toString()}`);
            const json = await res.json();
            const list = json.data || [];

            if (!status && !roomId && !search) {
                const confirmed = list.filter(b => b.status === 'CONFIRMED');
                const cancelled = list.filter(b => b.status === 'CANCELLED');
                const revenue = confirmed.reduce((acc, b) => acc + Number(b.total_price || 0), 0);
                const penalty = cancelled.reduce((acc, b) => acc + Number(b.penalty_amount || 0), 0);

                document.getElementById('adm-stat-confirmed').textContent = `${confirmed.length} Booking`;
                document.getElementById('adm-stat-cancelled').textContent = `${cancelled.length} Dibatalkan`;
                document.getElementById('adm-stat-revenue').textContent = formatRupiah(revenue);
                document.getElementById('adm-stat-penalty').textContent = formatRupiah(penalty);
            }

            const tbody = document.getElementById('adm-bookings-tbody');
            if (!list.length) {
                tbody.innerHTML = '<tr><td colspan="8" class="py-8 text-center text-slate-400">Tidak ada data booking.</td></tr>';
                return;
            }

            tbody.innerHTML = list.map(b => {
                const isConfirmed = b.status === 'CONFIRMED';
                const isPaid = b.payment_status === 'PAID';
                const addonsList = b.additional_facilities || [];
                const addonsHtml = addonsList.length
                    ? `<div class="flex flex-wrap gap-1 mt-1">${addonsList.map(a => `<span class="px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 border border-indigo-200/70 text-[10px] font-semibold">+ ${a.name} (x${a.quantity})</span>`).join('')}</div>`
                    : '';
                const paymentBadge = isPaid
                    ? `<span class="inline-flex items-center px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-extrabold mt-1">QRIS · LUNAS</span>`
                    : `<span class="inline-flex items-center px-2 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200 text-[10px] font-extrabold mt-1">QRIS · UNPAID</span>`;
                return `
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-3.5 px-4 font-mono-code text-xs">
                            <span class="font-extrabold text-indigo-700 block">${b.booking_code || '#' + b.id}</span>
                            <span class="text-[10px] text-slate-400">ID #${b.id}</span>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="font-bold text-slate-900 block">${b.room ? b.room.name : 'Ruang #' + b.room_id}</span>
                            ${addonsHtml}
                        </td>
                        <td class="py-3.5 px-4">
                            <p class="font-bold text-slate-900">${b.booker_name}</p>
                            <p class="text-xs text-slate-500">${b.booker_contact}</p>
                        </td>
                        <td class="py-3.5 px-4 font-mono-code text-xs">
                            <span class="font-bold block">${b.date}</span>
                            <span class="text-indigo-600 font-semibold">${b.start_hour} – ${b.end_hour} WIB</span>
                        </td>
                        <td class="py-3.5 px-4 text-xs font-semibold">${b.participants} orang</td>
                        <td class="py-3.5 px-4 font-mono-code text-xs">
                            <span class="font-bold block">${formatRupiah(b.total_price)}</span>
                            ${b.additional_facilities_price > 0 ? `<span class="text-[10px] text-slate-500 block">Add-on: ${formatRupiah(b.additional_facilities_price)}</span>` : ''}
                            ${paymentBadge}
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold ${isConfirmed ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'}">
                                ${b.status}
                            </span>
                            ${b.penalty_amount > 0 ? `<p class="text-[11px] font-mono-code font-bold text-amber-700 mt-1">Denda: ${formatRupiah(b.penalty_amount)}</p>` : ''}
                        </td>
                        <td class="py-3.5 px-4 text-right space-y-1">
                            ${isConfirmed && !isPaid ? `
                                <button onclick="adminVerifyQris(${b.id})" class="w-full px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 text-xs font-bold transition cursor-pointer">
                                    Verifikasi QRIS
                                </button>
                            ` : ''}
                            ${isConfirmed ? `
                                <button onclick="adminCancelBooking(${b.id})" class="w-full px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white border border-rose-200 text-xs font-bold transition cursor-pointer">
                                    Batalkan Booking
                                </button>
                            ` : '<span class="text-xs text-slate-400">Selesai/Batal</span>'}
                        </td>
                    </tr>
                `;
            }).join('');
        }

        async function adminVerifyQris(id) {
            const res = await fetch(`/api/bookings/${id}/pay`, {
                method: 'POST',
                headers: { 'Accept': 'application/json' },
            });
            const json = await res.json();
            if (res.ok) {
                showAdminAlert('success', `Pembayaran QRIS Booking #${id} Lunas`, json.message, res.status);
                refreshAdminData();
            } else {
                showAdminAlert('error', 'Gagal Verifikasi QRIS', json.message, res.status);
            }
        }

        async function adminCancelBooking(id) {
            if (!confirm(`Batalkan Booking #${id}? Sistem akan menghitung denda pembatalan sesuai aturan BR-05.`)) return;

            const res = await fetch(`/api/bookings/${id}/cancel`, {
                method: 'POST',
                headers: { 'Accept': 'application/json' },
            });
            const json = await res.json();

            if (res.ok) {
                showAdminAlert(
                    json.data.penalty_amount > 0 ? 'warning' : 'success',
                    `Booking #${id} Dibatalkan`,
                    `Denda pembatalan: ${formatRupiah(json.data.penalty_amount)}.`,
                    res.status
                );
                refreshAdminData();
            } else {
                showAdminAlert('error', 'Gagal Membatalkan Booking', json.message, res.status);
            }
        }

        async function loadAdminCalendar() {
            const date = document.getElementById('adm-cal-date').value || getTodayWibString();
            const roomId = document.getElementById('adm-cal-room').value;
            const view = document.getElementById('adm-cal-view').value;

            const params = new URLSearchParams({ date, view });
            if (roomId) params.set('room_id', roomId);

            const res = await fetch(`/api/calendar?${params.toString()}`);
            const json = await res.json();
            const container = document.getElementById('adm-calendar-container');

            if (!res.ok || !json.data) return;

            container.innerHTML = (json.data.days || []).map(dayObj => `
                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 rounded-full bg-slate-900 text-white font-mono-code text-xs font-bold">${dayObj.date}</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        ${(dayObj.rooms || []).map(r => `
                            <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50 space-y-2">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-extrabold text-sm text-slate-900">${r.room_name}</h4>
                                    <span class="text-xs text-slate-500">Kapasitas ${r.capacity} org</span>
                                </div>
                                ${(r.bookings || []).length ? r.bookings.map(b => `
                                    <div class="p-2.5 rounded-xl bg-white border border-indigo-200 flex items-center justify-between text-xs">
                                        <div>
                                            <span class="font-mono-code font-bold text-indigo-700">${b.start_hour} – ${b.end_hour} WIB</span>
                                            <p class="font-bold text-slate-900">${b.booker_name} (${b.participants} org)</p>
                                        </div>
                                        <span class="font-mono-code font-bold">${formatRupiah(b.total_price)}</span>
                                    </div>
                                `).join('') : '<p class="text-xs text-slate-400 italic">Tidak ada jadwal booking.</p>'}
                            </div>
                        `).join('')}
                    </div>
                </div>
            `).join('');
        }

        async function refreshAdminData() {
            await Promise.all([
                loadAdminRooms(),
                loadAdminAddons(),
                loadAdminBookings(),
            ]);
            if (!document.getElementById('adm-tab-calendar').classList.contains('hidden')) {
                await loadAdminCalendar();
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('adm-cal-date').value = getTodayWibString();
            refreshAdminData();
        });
    </script>
</body>
</html>
