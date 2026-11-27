<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard Admin — RuangKita Coworking Space')</title>

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

    <!-- Admin Navbar (Opsi A: 1 Baris Simetris dengan Kapsul di Tengah) -->
    <header class="sticky top-0 z-30 bg-slate-900 text-white border-b border-slate-800 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 grid grid-cols-2 md:grid-cols-12 items-center gap-4">
            <!-- Left (3 cols): Admin Identity -->
            <div class="md:col-span-3 flex items-center">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 shrink-0">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white font-extrabold shadow-md shrink-0">
                        A
                    </div>
                    <div class="leading-tight">
                        <div class="flex items-center gap-2 whitespace-nowrap">
                            <span class="text-lg font-extrabold tracking-tight text-white">RuangKita</span>
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-400/30">
                                Admin
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-400 hidden lg:block whitespace-nowrap mt-0.5">{{ $adminName }}</p>
                    </div>
                </a>
            </div>

            <!-- Center (6 cols): Capsule Pill Navigation Bar -->
            <div class="hidden md:col-span-6 md:flex items-center justify-center">
                <nav class="inline-flex items-center bg-slate-800/90 p-1 rounded-full border border-slate-700/80 shadow-inner">
                    <a href="{{ route('admin.dashboard') }}"
                        class="px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-white text-slate-950 shadow-sm' : 'text-slate-300 hover:text-white' }}">
                        Manajemen Ruang
                    </a>

                    <a href="{{ route('admin.facilities') }}"
                        class="px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all duration-200 {{ request()->routeIs('admin.facilities') ? 'bg-white text-slate-950 shadow-sm' : 'text-slate-300 hover:text-white' }}">
                        Fasilitas Tambahan
                    </a>

                    <a href="{{ route('admin.bookings') }}"
                        class="px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all duration-200 {{ request()->routeIs('admin.bookings') ? 'bg-white text-slate-950 shadow-sm' : 'text-slate-300 hover:text-white' }}">
                        Kelola Booking
                    </a>

                    <a href="{{ route('admin.calendar') }}"
                        class="px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all duration-200 {{ request()->routeIs('admin.calendar') ? 'bg-white text-slate-950 shadow-sm' : 'text-slate-300 hover:text-white' }}">
                        Kalender Ruang
                    </a>
                </nav>
            </div>

            <!-- Right (3 cols): Admin Actions -->
            <div class="md:col-span-3 flex items-center justify-end gap-2 shrink-0">
                <span class="hidden xl:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-slate-800 border border-slate-700 text-xs font-bold text-slate-200 whitespace-nowrap">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <span>{{ $adminEmail }}</span>
                </span>

                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit"
                        class="px-4 py-1.5 rounded-full bg-rose-600 hover:bg-rose-500 text-xs font-bold text-white transition cursor-pointer whitespace-nowrap">
                        Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Mobile Sub-Navbar -->
        <div class="md:hidden border-t border-slate-800 bg-slate-900 px-4 py-2 flex items-center justify-center">
            <div class="inline-flex items-center bg-slate-800 p-1 rounded-full border border-slate-700 w-full justify-between overflow-x-auto">
                <a href="{{ route('admin.dashboard') }}"
                    class="flex-1 text-center px-3 py-1.5 rounded-full text-xs font-bold whitespace-nowrap {{ request()->routeIs('admin.dashboard') ? 'bg-white text-slate-950' : 'text-slate-300' }}">
                    Ruangan
                </a>
                <a href="{{ route('admin.facilities') }}"
                    class="flex-1 text-center px-3 py-1.5 rounded-full text-xs font-bold whitespace-nowrap {{ request()->routeIs('admin.facilities') ? 'bg-white text-slate-950' : 'text-slate-300' }}">
                    Fasilitas
                </a>
                <a href="{{ route('admin.bookings') }}"
                    class="flex-1 text-center px-3 py-1.5 rounded-full text-xs font-bold whitespace-nowrap {{ request()->routeIs('admin.bookings') ? 'bg-white text-slate-950' : 'text-slate-300' }}">
                    Booking
                </a>
                <a href="{{ route('admin.calendar') }}"
                    class="flex-1 text-center px-3 py-1.5 rounded-full text-xs font-bold whitespace-nowrap {{ request()->routeIs('admin.calendar') ? 'bg-white text-slate-950' : 'text-slate-300' }}">
                    Kalender
                </a>
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

        @yield('content')

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200/80 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <span class="font-bold text-slate-800">RuangKita Coworking Space • Panel Administrasi</span>
            <span>Login: {{ $adminName }} ({{ $adminEmail }})</span>
        </div>
    </footer>

    <!-- Shared Admin Helper & KPI Script -->
    <script>
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
            if (dateStr === today) return `Hari Ini • ${formatted}`;
            return formatted;
        }

        function showAdminAlert(type, title, message, status = null) {
            const box = document.getElementById('adm-alert-box');
            if (!box) return;
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

        async function loadAdminKpiStats() {
            try {
                const [roomsRes, bookingsRes] = await Promise.all([
                    fetch('/api/rooms'),
                    fetch('/api/bookings'),
                ]);
                const roomsJson = await roomsRes.json();
                const bookingsJson = await bookingsRes.json();

                const rooms = roomsJson.data || [];
                const bookings = bookingsJson.data || [];

                const activeCount = rooms.filter(r => r.is_active).length;
                const statRooms = document.getElementById('adm-stat-rooms');
                const statRoomsSub = document.getElementById('adm-stat-rooms-sub');
                if (statRooms) statRooms.textContent = `${rooms.length} Ruang`;
                if (statRoomsSub) statRoomsSub.textContent = `${activeCount} Aktif • ${rooms.length - activeCount} Nonaktif`;

                const confirmed = bookings.filter(b => b.status === 'CONFIRMED');
                const cancelled = bookings.filter(b => b.status === 'CANCELLED');
                const revenue = confirmed.reduce((acc, b) => acc + Number(b.total_price || 0), 0);
                const penalty = cancelled.reduce((acc, b) => acc + Number(b.penalty_amount || 0), 0);

                const statConf = document.getElementById('adm-stat-confirmed');
                const statCanc = document.getElementById('adm-stat-cancelled');
                const statRev = document.getElementById('adm-stat-revenue');
                const statPen = document.getElementById('adm-stat-penalty');

                if (statConf) statConf.textContent = `${confirmed.length} Booking`;
                if (statCanc) statCanc.textContent = `${cancelled.length} Dibatalkan`;
                if (statRev) statRev.textContent = formatRupiah(revenue);
                if (statPen) statPen.textContent = formatRupiah(penalty);
            } catch (e) {
                console.error(e);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadAdminKpiStats();
        });
    </script>

    @yield('scripts')
</body>
</html>
