@extends('customer.layout')

@section('title', 'Riwayat & Pembatalan Booking — RuangKita')

@section('content')
    <section class="space-y-6">
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs p-6 space-y-5">
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-4">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">Kelola Pesanan Akun Anda ({{ session('customer_name') }})</span>
                    <h1 class="text-xl font-extrabold text-slate-900">Pesanan Saya, Ubah Jadwal &amp; Pembatalan</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Ubah jadwal (reschedule) ke tanggal/jam lain dengan durasi yang sama, atau batalkan pesanan (&ge; 2 jam sebelum mulai bebas denda; &lt; 2 jam denda 50%).</p>
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

    <!-- Reschedule / Ganti Waktu Modal -->
    <div id="reschedule-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-950/75 backdrop-blur-xs p-4 overflow-y-auto">
        <div class="bg-white rounded-3xl max-w-xl w-full border border-slate-200 shadow-2xl overflow-hidden my-6">
            <div class="bg-slate-900 px-6 py-4 text-white flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-indigo-300 block">Ganti Waktu / Reschedule</span>
                    <h3 id="rs-modal-title" class="text-base font-extrabold text-white">Ubah Jadwal Rapat</h3>
                </div>
                <button type="button" onclick="closeRescheduleModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white font-bold cursor-pointer">✕</button>
            </div>

            <form id="reschedule-form" onsubmit="submitReschedule(event)" class="p-6 space-y-5">
                <input type="hidden" id="rs-booking-id" value="">

                <!-- Current Booking Summary -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs space-y-1.5">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Kode &amp; Ruangan:</span>
                        <span id="rs-current-room" class="font-extrabold text-slate-900">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Jadwal Saat Ini:</span>
                        <span id="rs-current-schedule" class="font-mono-code font-bold text-indigo-700">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Durasi Tetap:</span>
                        <span id="rs-fixed-duration-label" class="font-extrabold text-emerald-700">-</span>
                    </div>
                </div>

                <!-- New Date & Time Inputs -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Tanggal Baru</label>
                        <input type="date" id="rs-date" required onchange="onRescheduleDateChange()"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-xs font-bold text-slate-900 focus:border-indigo-600 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Jam Mulai Baru</label>
                        <select id="rs-start" required onchange="onRescheduleStartChange()"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-xs font-mono-code font-bold text-slate-900 focus:border-indigo-600 focus:outline-none">
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Jam Selesai Baru</label>
                        <input type="text" id="rs-end" readonly
                            class="w-full rounded-xl border border-slate-200 bg-slate-100 px-3 py-2.5 text-xs font-mono-code font-bold text-slate-700 cursor-not-allowed">
                    </div>
                </div>

                <!-- Mini Slot Availability Map for Selected Date -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-extrabold text-slate-700 uppercase tracking-wider text-[11px]">Peta Ketersediaan Ruang (Klik Slot Hijau)</span>
                        <div class="flex items-center gap-2.5 text-[11px]">
                            <span class="inline-flex items-center gap-1 text-indigo-700 font-bold"><span class="w-2.5 h-2.5 rounded-xs bg-indigo-600 inline-block"></span> Baru</span>
                            <span class="inline-flex items-center gap-1 text-emerald-700 font-semibold"><span class="w-2.5 h-2.5 rounded-xs bg-emerald-500 inline-block"></span> Kosong</span>
                            <span class="inline-flex items-center gap-1 text-rose-700 font-semibold"><span class="w-2.5 h-2.5 rounded-xs bg-rose-500 inline-block"></span> Terisi</span>
                        </div>
                    </div>
                    <div id="rs-slots-grid" class="grid grid-cols-4 sm:grid-cols-7 gap-1.5"></div>
                </div>

                <p id="rs-error-msg" class="hidden p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold"></p>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeRescheduleModal()"
                        class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" id="rs-submit-btn"
                        class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs shadow-md shadow-indigo-600/20 transition cursor-pointer">
                        Simpan Jadwal Baru
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let bookingsList = [];

        async function loadRoomsForHistoryFilter() {
            const res = await fetch('/api/rooms');
            const json = await res.json();
            const rooms = json.data || [];
            const blRoom = document.getElementById('bl-room-filter');
            const currentVal = blRoom.value;
            blRoom.innerHTML = '<option value="">Semua Ruang</option>';
            rooms.forEach(room => {
                const opt = document.createElement('option');
                opt.value = room.id;
                opt.textContent = room.name;
                blRoom.appendChild(opt);
            });
            blRoom.value = currentVal;
        }

        function resetBookingsFilter() {
            document.getElementById('bl-payment-filter').value = '';
            document.getElementById('bl-status-filter').value = '';
            document.getElementById('bl-room-filter').value = '';
            document.getElementById('bl-search').value = '';
            loadBookingsList();
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
            if (loggedInCustomer?.id) {
                params.set('user_id', loggedInCustomer.id);
                if (loggedInCustomer.phone) {
                    params.set('customer_contact', loggedInCustomer.phone);
                }
            }

            const res = await fetch(`/api/bookings?${params.toString()}`);
            const json = await res.json();
            bookingsList = json.data || [];

            const tbody = document.getElementById('bookings-table-body');
            if (!bookingsList.length) {
                tbody.innerHTML = `<tr><td colspan="8" class="py-8 text-center text-slate-400 text-sm">Belum ada riwayat booking yang ditemukan.</td></tr>`;
                return;
            }

            tbody.innerHTML = bookingsList.map(b => {
                const roomName = b.room ? b.room.name : `Ruang #${b.room_id}`;
                const isConfirmed = b.status === 'CONFIRMED';
                const isPaid = b.payment_status === 'PAID';
                const addonsList = b.additional_facilities || [];
                const addonsBadges = addonsList.length
                    ? `<div class="flex flex-wrap gap-1 mt-1.5">${addonsList.map(a => `<span class="px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 border border-indigo-200/70 text-[10px] font-semibold">+ ${a.name} (x${a.quantity}) · ${formatRupiah(a.subtotal)}</span>`).join('')}</div>`
                    : `<span class="text-[11px] text-slate-400 block mt-0.5">Tanpa fasilitas tambahan</span>`;

                const paymentBadge = isPaid
                    ? `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-extrabold mt-1">✓ QRIS · LUNAS</span>`
                    : `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200 text-[10px] font-extrabold mt-1">⏳ MENUNGGU VERIFIKASI ADMIN</span>`;

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
                                ${isPaid ? 'Lihat / Cetak Nota' : 'Lihat QRIS & Nota'}
                            </button>
                            ${isConfirmed ? `
                                <button onclick="openRescheduleModal(${b.id})"
                                    class="w-full px-3 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-500 text-amber-800 hover:text-white border border-amber-200 text-xs font-bold transition cursor-pointer">
                                    Ubah Jadwal
                                </button>
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

        async function cancelBooking(bookingId) {
            const booking = bookingsList.find(b => b.id === bookingId);
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
                    loadBookingsList();
                } else {
                    showAlert('error', `Gagal Membatalkan Booking`, json.message, res.status);
                }
            } catch (e) {
                showAlert('error', 'Kesalahan Jaringan', e.message);
            }
        }

        async function onQrisPaymentCompleted() {
            await loadBookingsList();
        }

        let activeRescheduleBooking = null;
        let rsAvailabilityData = null;

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
            const today = getTodayWibString();
            if (dateStr < today) return true;
            if (dateStr > today) return false;
            return timeToMinutes(startTimeStr) <= getCurrentWibMinutes();
        }

        function populateRescheduleStartOptions(selectedDate, durationMins, preferredStart = null) {
            const startSelect = document.getElementById('rs-start');
            startSelect.innerHTML = '';

            const maxStartMins = (21 * 60) - durationMins;
            let firstValid = null;

            for (let mins = 8 * 60; mins <= maxStartMins; mins += 30) {
                const t = minutesToTimeStr(mins);
                const isPast = isSlotInPastWib(selectedDate, t);
                const opt = document.createElement('option');
                opt.value = t;
                opt.disabled = isPast;
                opt.textContent = isPast ? `${t} WIB (Lewat)` : `${t} WIB`;
                if (!isPast && !firstValid) firstValid = t;
                startSelect.appendChild(opt);
            }

            if (preferredStart && !isSlotInPastWib(selectedDate, preferredStart) && timeToMinutes(preferredStart) <= maxStartMins) {
                startSelect.value = preferredStart;
            } else if (firstValid) {
                startSelect.value = firstValid;
            }
            updateRescheduleEndTime();
        }

        function updateRescheduleEndTime() {
            if (!activeRescheduleBooking) return;
            const startVal = document.getElementById('rs-start').value;
            if (!startVal) {
                document.getElementById('rs-end').value = '-';
                return;
            }
            const endMins = timeToMinutes(startVal) + Number(activeRescheduleBooking.duration_minutes || 60);
            document.getElementById('rs-end').value = `${minutesToTimeStr(endMins)} WIB`;
        }

        async function openRescheduleModal(bookingId) {
            const b = bookingsList.find(item => item.id === bookingId);
            if (!b) return;
            activeRescheduleBooking = b;

            document.getElementById('rs-booking-id').value = b.id;
            document.getElementById('rs-modal-title').textContent = `Ubah Jadwal — ${b.booking_code || '#' + b.id}`;
            document.getElementById('rs-current-room').textContent = `${b.room ? b.room.name : 'Ruang #' + b.room_id} (${b.participants} peserta)`;
            document.getElementById('rs-current-schedule').textContent = `${b.date} • ${b.start_hour} – ${b.end_hour} WIB`;
            document.getElementById('rs-fixed-duration-label').textContent = `${b.duration_hours} Jam (${b.duration_minutes} menit)`;
            document.getElementById('rs-error-msg').classList.add('hidden');

            const today = getTodayWibString();
            const dateInput = document.getElementById('rs-date');
            dateInput.min = today;
            dateInput.value = b.date >= today ? b.date : today;

            populateRescheduleStartOptions(dateInput.value, Number(b.duration_minutes || 60), b.start_hour);
            document.getElementById('reschedule-modal').classList.remove('hidden');
            await loadRescheduleAvailability();
        }

        function closeRescheduleModal() {
            document.getElementById('reschedule-modal').classList.add('hidden');
            activeRescheduleBooking = null;
        }

        async function onRescheduleDateChange() {
            if (!activeRescheduleBooking) return;
            const dateVal = document.getElementById('rs-date').value;
            const curStart = document.getElementById('rs-start').value;
            populateRescheduleStartOptions(dateVal, Number(activeRescheduleBooking.duration_minutes || 60), curStart);
            await loadRescheduleAvailability();
        }

        function onRescheduleStartChange() {
            updateRescheduleEndTime();
            renderRescheduleSlotsGrid();
        }

        function pickRescheduleSlot(startTime) {
            if (!activeRescheduleBooking) return;
            const dateVal = document.getElementById('rs-date').value;
            const durationMins = Number(activeRescheduleBooking.duration_minutes || 60);
            if (isSlotInPastWib(dateVal, startTime)) return;
            if (timeToMinutes(startTime) + durationMins > 21 * 60) {
                const errEl = document.getElementById('rs-error-msg');
                errEl.textContent = `Durasi pesanan Anda ${activeRescheduleBooking.duration_hours} jam, sehingga jam mulai maksimal pukul ${minutesToTimeStr(21 * 60 - durationMins)} WIB.`;
                errEl.classList.remove('hidden');
                return;
            }
            document.getElementById('rs-error-msg').classList.add('hidden');
            document.getElementById('rs-start').value = startTime;
            onRescheduleStartChange();
        }

        async function loadRescheduleAvailability() {
            if (!activeRescheduleBooking) return;
            const dateVal = document.getElementById('rs-date').value;
            const res = await fetch(`/api/rooms/${activeRescheduleBooking.room_id}/availability?date=${encodeURIComponent(dateVal)}`);
            const json = await res.json();
            if (res.ok && json.data) {
                rsAvailabilityData = json.data;
                renderRescheduleSlotsGrid();
            }
        }

        function renderRescheduleSlotsGrid() {
            const grid = document.getElementById('rs-slots-grid');
            if (!grid || !rsAvailabilityData || !activeRescheduleBooking) return;

            const dateVal = document.getElementById('rs-date').value;
            const selectedStart = timeToMinutes(document.getElementById('rs-start').value || '09:00');
            const selectedEnd = selectedStart + Number(activeRescheduleBooking.duration_minutes || 60);

            // Check if a slot belongs to the current booking itself (so it can still be reused)
            const ownBookingId = activeRescheduleBooking.id;
            const otherBookedIntervals = (rsAvailabilityData.booked_slots || [])
                .filter(b => Number(b.booking_id) !== Number(ownBookingId))
                .map(b => ({ start: timeToMinutes(b.start_time), end: timeToMinutes(b.end_time) }));

            grid.innerHTML = (rsAvailabilityData.slots || []).map(slot => {
                const sMins = timeToMinutes(slot.start_time);
                const eMins = timeToMinutes(slot.end_time);
                const isPast = isSlotInPastWib(dateVal, slot.start_time);
                const isOccupiedByOther = otherBookedIntervals.some(iv => sMins < iv.end && eMins > iv.start);
                const isSelected = !isPast && !isOccupiedByOther && sMins >= selectedStart && sMins < selectedEnd;

                if (isPast) {
                    return `<div class="py-1.5 px-1 rounded-lg bg-slate-100 border border-slate-200 text-slate-400 text-center text-[10px] font-mono-code line-through cursor-not-allowed">${slot.start_time}</div>`;
                }
                if (isOccupiedByOther) {
                    return `<div class="py-1.5 px-1 rounded-lg bg-rose-100 border border-rose-200 text-rose-800 text-center text-[10px] font-mono-code line-through cursor-not-allowed">${slot.start_time}</div>`;
                }
                if (isSelected) {
                    return `<button type="button" onclick="pickRescheduleSlot('${slot.start_time}')" class="py-1.5 px-1 rounded-lg bg-indigo-600 border border-indigo-600 text-white text-center text-[10px] font-mono-code font-extrabold cursor-pointer">${slot.start_time} ✓</button>`;
                }
                return `<button type="button" onclick="pickRescheduleSlot('${slot.start_time}')" class="py-1.5 px-1 rounded-lg bg-emerald-50 hover:bg-emerald-600 text-emerald-900 hover:text-white border border-emerald-200 text-center text-[10px] font-mono-code font-bold transition cursor-pointer">${slot.start_time}</button>`;
            }).join('');
        }

        async function submitReschedule(e) {
            e.preventDefault();
            if (!activeRescheduleBooking) return;

            const bookingId = activeRescheduleBooking.id;
            const dateVal = document.getElementById('rs-date').value;
            const startVal = document.getElementById('rs-start').value;
            const endMins = timeToMinutes(startVal) + Number(activeRescheduleBooking.duration_minutes || 60);
            const endVal = minutesToTimeStr(endMins);
            const errEl = document.getElementById('rs-error-msg');
            errEl.classList.add('hidden');

            try {
                const res = await fetch(`/api/bookings/${bookingId}/reschedule`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({
                        date: dateVal,
                        start_time: startVal,
                        end_time: endVal,
                    }),
                });
                const json = await res.json();

                if (res.ok) {
                    const updated = json.data;
                    closeRescheduleModal();
                    showAlert(
                        'success',
                        `Jadwal Berhasil Diubah (${updated.booking_code})`,
                        `Jadwal rapat berhasil dipindahkan ke ${updated.date} pukul ${updated.start_hour} – ${updated.end_hour} WIB.`,
                        res.status
                    );
                    await loadBookingsList();
                } else {
                    errEl.textContent = json.message || 'Gagal mengubah jadwal booking.';
                    errEl.classList.remove('hidden');
                }
            } catch (err) {
                errEl.textContent = err.message;
                errEl.classList.remove('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', async () => {
            await loadRoomsForHistoryFilter();
            await loadBookingsList();
        });
    </script>
@endsection
