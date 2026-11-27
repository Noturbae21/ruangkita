<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'RuangKita — Portal Pemesanan Ruang Meeting')</title>

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
            background: rgba(255, 255, 255, 0.90);
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

    <!-- Header / Navbar (Portal Pemesan Multi-Halaman — Opsi A: 1 Baris Simetris) -->
    <header class="sticky top-0 z-30 glass-panel border-b border-slate-200/80 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 grid grid-cols-2 md:grid-cols-12 items-center gap-4">
            <!-- Left (3 cols): Brand Identity -->
            <div class="md:col-span-3 flex items-center">
                <a href="{{ route('booking.portal') }}" class="flex items-center gap-3 shrink-0">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-indigo-600 to-violet-600 flex items-center justify-center text-white shadow-md shadow-indigo-500/20 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="leading-tight">
                        <div class="flex items-center gap-2 whitespace-nowrap">
                            <span class="text-lg font-extrabold tracking-tight bg-gradient-to-r from-slate-900 via-indigo-950 to-indigo-700 bg-clip-text text-transparent">RuangKita</span>
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200/60">Penyewa</span>
                        </div>
                        <p class="text-[11px] text-slate-500 hidden lg:block whitespace-nowrap mt-0.5">Pemesanan Ruang Rapat</p>
                    </div>
                </a>
            </div>

            <!-- Center (6 cols): Exact Centered Capsule Pill Navigation -->
            <div class="hidden md:col-span-6 md:flex items-center justify-center">
                <nav class="inline-flex items-center bg-slate-200/80 p-1 rounded-full border border-slate-300/70 shadow-inner">
                    <a href="{{ route('booking.portal') }}"
                        class="px-5 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all duration-200 {{ request()->routeIs('booking.portal') ? 'bg-white text-slate-950 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                        Pesan Ruangan
                    </a>

                    <a href="{{ route('customer.calendar') }}"
                        class="px-5 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all duration-200 {{ request()->routeIs('customer.calendar') ? 'bg-white text-slate-950 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                        Kalender Ruang
                    </a>

                    <a href="{{ route('customer.history') }}"
                        class="px-5 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all duration-200 {{ request()->routeIs('customer.history') ? 'bg-white text-slate-950 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                        Riwayat Pesanan
                    </a>
                </nav>
            </div>

            <!-- Right (3 cols): Customer Auth Controls -->
            <div class="md:col-span-3 flex items-center justify-end gap-2 shrink-0">
                @if (session()->has('customer_id'))
                    <a href="{{ route('customer.history') }}"
                        class="flex items-center gap-2 pl-1.5 pr-3.5 py-1.5 rounded-full bg-indigo-50 hover:bg-indigo-100 border border-indigo-200/80 text-xs font-bold text-indigo-900 transition whitespace-nowrap">
                        <span class="w-6 h-6 rounded-full bg-indigo-600 text-white text-[11px] font-extrabold flex items-center justify-center shrink-0">
                            {{ strtoupper(substr((string) session('customer_name', 'U'), 0, 1)) }}
                        </span>
                        <span class="max-w-[120px] truncate">{{ session('customer_name') }}</span>
                    </a>
                    <form method="POST" action="{{ route('customer.logout') }}">
                        @csrf
                        <button type="submit"
                            class="px-3.5 py-1.5 rounded-full bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white border border-rose-200 text-xs font-bold transition cursor-pointer whitespace-nowrap">
                            Keluar
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Mobile Sub-Navbar -->
        <div class="md:hidden border-t border-slate-200/80 bg-white px-4 py-2 flex items-center justify-center">
            <div class="inline-flex items-center bg-slate-200/80 p-1 rounded-full border border-slate-300/70 w-full justify-between">
                <a href="{{ route('booking.portal') }}"
                    class="flex-1 text-center px-3 py-1.5 rounded-full text-xs font-bold whitespace-nowrap {{ request()->routeIs('booking.portal') ? 'bg-white text-slate-950 shadow-xs' : 'text-slate-600' }}">
                    Pesan Ruangan
                </a>
                <a href="{{ route('customer.calendar') }}"
                    class="flex-1 text-center px-3 py-1.5 rounded-full text-xs font-bold whitespace-nowrap {{ request()->routeIs('customer.calendar') ? 'bg-white text-slate-950 shadow-xs' : 'text-slate-600' }}">
                    Kalender Ruang
                </a>
                <a href="{{ route('customer.history') }}"
                    class="flex-1 text-center px-3 py-1.5 rounded-full text-xs font-bold whitespace-nowrap {{ request()->routeIs('customer.history') ? 'bg-white text-slate-950 shadow-xs' : 'text-slate-600' }}">
                    Riwayat Pesanan
                </a>
            </div>
        </div>
    </header>

    <!-- Floating Toast Notification -->
    <div id="toast-container" class="fixed bottom-5 right-5 z-50 max-w-md w-full px-4 sm:px-0 pointer-events-none space-y-2"></div>

    @yield('hero')

    <main class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1 space-y-6">
        <!-- Alert / Notification Banner -->
        <div id="alert-box" class="hidden rounded-2xl p-4 border shadow-sm transition-all"></div>

        @yield('content')
    </main>

    @include('customer.partials.qris-modal')

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

    <!-- Shared Helper & QRIS Modal Script -->
    <script>
        const loggedInCustomer = {
            id: @json(session('customer_id')),
            name: @json(session('customer_name', '')),
            email: @json(session('customer_email', '')),
            phone: @json(session('customer_phone', '')),
        };

        function formatRupiah(amount) {
            return 'Rp ' + Number(amount || 0).toLocaleString('id-ID');
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

        function updateWibClock() {
            const el = document.getElementById('wib-clock');
            if (!el) return;
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
            el.textContent = formatted + ' WIB';
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
            if (!box) return;
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

            const ar = size - 9, ac = size - 9;
            for (let r = -2; r <= 2; r++) {
                for (let c = -2; c <= 2; c++) {
                    reserved[ar + r][ac + c] = true;
                    if (Math.abs(r) === 2 || Math.abs(c) === 2 || (r === 0 && c === 0)) {
                        matrix[ar + r][ac + c] = 1;
                    }
                }
            }

            for (let i = 8; i < size - 8; i++) {
                reserved[6][i] = true;
                reserved[i][6] = true;
                matrix[6][i] = i % 2 === 0 ? 1 : 0;
                matrix[i][6] = i % 2 === 0 ? 1 : 0;
            }

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
                document.getElementById('qm-reference').textContent = `Ref: ${q.payment_reference} • NMID: ${q.nmid || 'ID1026496073473'}`;

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
                        paidTimestamp.textContent = `Diverifikasi oleh Admin pada: ${q.paid_at ? q.paid_at.replace('T', ' ').substring(0, 19) + ' WIB' : nowFormatted}`;
                    }
                    if (payBtn) payBtn.classList.add('hidden');
                } else if (isCancelled) {
                    statusBadge.textContent = 'DIBATALKAN';
                    statusBadge.className = 'px-2.5 py-0.5 rounded-md bg-rose-500 text-white text-[10px] font-extrabold uppercase';
                    paidBanner.classList.add('hidden');
                    if (unpaidBanner) unpaidBanner.classList.add('hidden');
                    if (payBtn) payBtn.classList.add('hidden');
                } else {
                    statusBadge.textContent = 'MENUNGGU VERIFIKASI ADMIN';
                    statusBadge.className = 'px-2.5 py-0.5 rounded-md bg-amber-400 text-slate-950 text-[10px] font-extrabold uppercase';
                    paidBanner.classList.add('hidden');
                    if (unpaidBanner) unpaidBanner.classList.remove('hidden');
                    if (payBtn) payBtn.classList.remove('hidden');
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
                    if (typeof onQrisPaymentCompleted === 'function') {
                        await onQrisPaymentCompleted();
                    }
                    await openQrisModal(currentQrisBookingId);
                } else {
                    showAlert('error', 'Gagal Memproses Pembayaran QRIS', json.message || 'Terjadi kesalahan.', res.status);
                }
            } catch (e) {
                showAlert('error', 'Kesalahan Jaringan', e.message);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            updateWibClock();
            setInterval(updateWibClock, 1000);

            @if (session('auth_success'))
                showAlert('success', 'Autentikasi Berhasil', @json(session('auth_success')));
            @endif
        });
    </script>

    @yield('scripts')
</body>
</html>
