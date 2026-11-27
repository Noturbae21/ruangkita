@extends('admin.layout')

@section('title', 'Kelola Seluruh Booking — RuangKita Admin')

@section('content')
    <section class="bg-white rounded-3xl border border-slate-200/90 shadow-2xs p-6 space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">Manajemen Booking Admin</span>
                <h1 class="text-xl font-extrabold text-slate-900">Seluruh Data Booking Pemesan</h1>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <select id="adm-bl-payment" onchange="loadAdminBookings()"
                    class="rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700">
                    <option value="">Semua Pembayaran</option>
                    <option value="PAID">QRIS LUNAS (PAID)</option>
                    <option value="UNPAID">BELUM BAYAR (UNPAID)</option>
                </select>

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

                <button type="button" onclick="resetAdminBookingFilters()"
                    class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition cursor-pointer">
                    Reset
                </button>
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

    <!-- Admin Reschedule Modal -->
    <div id="adm-reschedule-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-950/75 backdrop-blur-xs p-4 overflow-y-auto">
        <div class="bg-white rounded-3xl max-w-xl w-full border border-slate-200 shadow-2xl overflow-hidden my-6">
            <div class="bg-slate-900 px-6 py-4 text-white flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-300 block">Ganti Waktu / Reschedule (Admin)</span>
                    <h3 id="adm-rs-title" class="text-base font-extrabold text-white">Ubah Jadwal Booking</h3>
                </div>
                <button type="button" onclick="closeAdminRescheduleModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white font-bold cursor-pointer">✕</button>
            </div>

            <form id="adm-reschedule-form" onsubmit="submitAdminReschedule(event)" class="p-6 space-y-5">
                <input type="hidden" id="adm-rs-booking-id" value="">

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs space-y-1.5">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Pemesan &amp; Ruangan:</span>
                        <span id="adm-rs-current-room" class="font-extrabold text-slate-900">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Jadwal Saat Ini:</span>
                        <span id="adm-rs-current-schedule" class="font-mono-code font-bold text-indigo-700">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Durasi Tetap:</span>
                        <span id="adm-rs-fixed-duration" class="font-extrabold text-emerald-700">-</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Tanggal Baru</label>
                        <input type="date" id="adm-rs-date" required onchange="onAdminRescheduleDateChange()"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-xs font-bold text-slate-900 focus:border-indigo-600 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Jam Mulai Baru</label>
                        <select id="adm-rs-start" required onchange="onAdminRescheduleStartChange()"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-xs font-mono-code font-bold text-slate-900 focus:border-indigo-600 focus:outline-none">
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Jam Selesai Baru</label>
                        <input type="text" id="adm-rs-end" readonly
                            class="w-full rounded-xl border border-slate-200 bg-slate-100 px-3 py-2.5 text-xs font-mono-code font-bold text-slate-700 cursor-not-allowed">
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-extrabold text-slate-700 uppercase tracking-wider text-[11px]">Peta Ketersediaan Ruang (Klik Slot Hijau)</span>
                        <div class="flex items-center gap-2.5 text-[11px]">
                            <span class="inline-flex items-center gap-1 text-indigo-700 font-bold"><span class="w-2.5 h-2.5 rounded-xs bg-indigo-600 inline-block"></span> Baru</span>
                            <span class="inline-flex items-center gap-1 text-emerald-700 font-semibold"><span class="w-2.5 h-2.5 rounded-xs bg-emerald-500 inline-block"></span> Kosong</span>
                            <span class="inline-flex items-center gap-1 text-rose-700 font-semibold"><span class="w-2.5 h-2.5 rounded-xs bg-rose-500 inline-block"></span> Terisi</span>
                        </div>
                    </div>
                    <div id="adm-rs-slots-grid" class="grid grid-cols-4 sm:grid-cols-7 gap-1.5"></div>
                </div>

                <p id="adm-rs-error-msg" class="hidden p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold"></p>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeAdminRescheduleModal()"
                        class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
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
        async function loadRoomsForBookingFilter() {
            const res = await fetch('/api/rooms');
            const json = await res.json();
            const rooms = json.data || [];
            const el = document.getElementById('adm-bl-room');
            const cur = el.value;
            el.innerHTML = '<option value="">Semua Ruang</option>';
            rooms.forEach(r => {
                const opt = document.createElement('option');
                opt.value = r.id;
                opt.textContent = r.name;
                el.appendChild(opt);
            });
            el.value = cur;
        }

        function resetAdminBookingFilters() {
            document.getElementById('adm-bl-payment').value = '';
            document.getElementById('adm-bl-status').value = '';
            document.getElementById('adm-bl-room').value = '';
            document.getElementById('adm-bl-search').value = '';
            loadAdminBookings();
        }

        async function loadAdminBookings() {
            const paymentStatus = document.getElementById('adm-bl-payment')?.value || '';
            const status = document.getElementById('adm-bl-status').value;
            const roomId = document.getElementById('adm-bl-room').value;
            const search = document.getElementById('adm-bl-search').value;

            const params = new URLSearchParams();
            if (paymentStatus) params.set('payment_status', paymentStatus);
            if (status) params.set('status', status);
            if (roomId) params.set('room_id', roomId);
            if (search) params.set('booker_name', search);

            const res = await fetch(`/api/bookings?${params.toString()}`);
            const json = await res.json();
            const list = json.data || [];
            window.adminBookingsCache = list;

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
                                <button onclick="openAdminRescheduleModal(${b.id})" class="w-full px-3 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-500 text-amber-800 hover:text-white border border-amber-200 text-xs font-bold transition cursor-pointer">
                                    Ubah Jadwal
                                </button>
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
                await Promise.all([loadAdminBookings(), loadAdminKpiStats()]);
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
                await Promise.all([loadAdminBookings(), loadAdminKpiStats()]);
            } else {
                showAdminAlert('error', 'Gagal Membatalkan Booking', json.message, res.status);
            }
        }

        let admActiveRescheduleBooking = null;
        let admRsAvailabilityData = null;

        function admTimeToMinutes(t) {
            const [h, m] = (t || '00:00').split(':').map(Number);
            return h * 60 + m;
        }

        function admMinutesToTimeStr(mins) {
            const h = String(Math.floor(mins / 60)).padStart(2, '0');
            const m = String(mins % 60).padStart(2, '0');
            return `${h}:${m}`;
        }

        function admGetCurrentWibMinutes() {
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

        function admIsSlotInPastWib(dateStr, startTimeStr) {
            const today = getTodayWibString();
            if (dateStr < today) return true;
            if (dateStr > today) return false;
            return admTimeToMinutes(startTimeStr) <= admGetCurrentWibMinutes();
        }

        function populateAdminRescheduleStartOptions(selectedDate, durationMins, preferredStart = null) {
            const startSelect = document.getElementById('adm-rs-start');
            startSelect.innerHTML = '';

            const maxStartMins = (21 * 60) - durationMins;
            let firstValid = null;

            for (let mins = 8 * 60; mins <= maxStartMins; mins += 30) {
                const t = admMinutesToTimeStr(mins);
                const isPast = admIsSlotInPastWib(selectedDate, t);
                const opt = document.createElement('option');
                opt.value = t;
                opt.disabled = isPast;
                opt.textContent = isPast ? `${t} WIB (Lewat)` : `${t} WIB`;
                if (!isPast && !firstValid) firstValid = t;
                startSelect.appendChild(opt);
            }

            if (preferredStart && !admIsSlotInPastWib(selectedDate, preferredStart) && admTimeToMinutes(preferredStart) <= maxStartMins) {
                startSelect.value = preferredStart;
            } else if (firstValid) {
                startSelect.value = firstValid;
            }
            updateAdminRescheduleEndTime();
        }

        function updateAdminRescheduleEndTime() {
            if (!admActiveRescheduleBooking) return;
            const startVal = document.getElementById('adm-rs-start').value;
            if (!startVal) {
                document.getElementById('adm-rs-end').value = '-';
                return;
            }
            const endMins = admTimeToMinutes(startVal) + Number(admActiveRescheduleBooking.duration_minutes || 60);
            document.getElementById('adm-rs-end').value = `${admMinutesToTimeStr(endMins)} WIB`;
        }

        async function openAdminRescheduleModal(bookingId) {
            const list = window.adminBookingsCache || [];
            const b = list.find(item => item.id === bookingId);
            if (!b) return;
            admActiveRescheduleBooking = b;

            document.getElementById('adm-rs-booking-id').value = b.id;
            document.getElementById('adm-rs-title').textContent = `Ubah Jadwal — ${b.booking_code || '#' + b.id}`;
            document.getElementById('adm-rs-current-room').textContent = `${b.booker_name} • ${b.room ? b.room.name : 'Ruang #' + b.room_id}`;
            document.getElementById('adm-rs-current-schedule').textContent = `${b.date} • ${b.start_hour} – ${b.end_hour} WIB`;
            document.getElementById('adm-rs-fixed-duration').textContent = `${b.duration_hours} Jam (${b.duration_minutes} menit)`;
            document.getElementById('adm-rs-error-msg').classList.add('hidden');

            const today = getTodayWibString();
            const dateInput = document.getElementById('adm-rs-date');
            dateInput.min = today;
            dateInput.value = b.date >= today ? b.date : today;

            populateAdminRescheduleStartOptions(dateInput.value, Number(b.duration_minutes || 60), b.start_hour);
            document.getElementById('adm-reschedule-modal').classList.remove('hidden');
            await loadAdminRescheduleAvailability();
        }

        function closeAdminRescheduleModal() {
            document.getElementById('adm-reschedule-modal').classList.add('hidden');
            admActiveRescheduleBooking = null;
        }

        async function onAdminRescheduleDateChange() {
            if (!admActiveRescheduleBooking) return;
            const dateVal = document.getElementById('adm-rs-date').value;
            const curStart = document.getElementById('adm-rs-start').value;
            populateAdminRescheduleStartOptions(dateVal, Number(admActiveRescheduleBooking.duration_minutes || 60), curStart);
            await loadAdminRescheduleAvailability();
        }

        function onAdminRescheduleStartChange() {
            updateAdminRescheduleEndTime();
            renderAdminRescheduleSlotsGrid();
        }

        function pickAdminRescheduleSlot(startTime) {
            if (!admActiveRescheduleBooking) return;
            const dateVal = document.getElementById('adm-rs-date').value;
            const durationMins = Number(admActiveRescheduleBooking.duration_minutes || 60);
            if (admIsSlotInPastWib(dateVal, startTime)) return;
            if (admTimeToMinutes(startTime) + durationMins > 21 * 60) {
                const errEl = document.getElementById('adm-rs-error-msg');
                errEl.textContent = `Durasi pesanan ${admActiveRescheduleBooking.duration_hours} jam, jam mulai maksimal pukul ${admMinutesToTimeStr(21 * 60 - durationMins)} WIB.`;
                errEl.classList.remove('hidden');
                return;
            }
            document.getElementById('adm-rs-error-msg').classList.add('hidden');
            document.getElementById('adm-rs-start').value = startTime;
            onAdminRescheduleStartChange();
        }

        async function loadAdminRescheduleAvailability() {
            if (!admActiveRescheduleBooking) return;
            const dateVal = document.getElementById('adm-rs-date').value;
            const res = await fetch(`/api/rooms/${admActiveRescheduleBooking.room_id}/availability?date=${encodeURIComponent(dateVal)}`);
            const json = await res.json();
            if (res.ok && json.data) {
                admRsAvailabilityData = json.data;
                renderAdminRescheduleSlotsGrid();
            }
        }

        function renderAdminRescheduleSlotsGrid() {
            const grid = document.getElementById('adm-rs-slots-grid');
            if (!grid || !admRsAvailabilityData || !admActiveRescheduleBooking) return;

            const dateVal = document.getElementById('adm-rs-date').value;
            const selectedStart = admTimeToMinutes(document.getElementById('adm-rs-start').value || '09:00');
            const selectedEnd = selectedStart + Number(admActiveRescheduleBooking.duration_minutes || 60);

            const ownBookingId = admActiveRescheduleBooking.id;
            const otherBookedIntervals = (admRsAvailabilityData.booked_slots || [])
                .filter(b => Number(b.booking_id) !== Number(ownBookingId))
                .map(b => ({ start: admTimeToMinutes(b.start_time), end: admTimeToMinutes(b.end_time) }));

            grid.innerHTML = (admRsAvailabilityData.slots || []).map(slot => {
                const sMins = admTimeToMinutes(slot.start_time);
                const eMins = admTimeToMinutes(slot.end_time);
                const isPast = admIsSlotInPastWib(dateVal, slot.start_time);
                const isOccupiedByOther = otherBookedIntervals.some(iv => sMins < iv.end && eMins > iv.start);
                const isSelected = !isPast && !isOccupiedByOther && sMins >= selectedStart && sMins < selectedEnd;

                if (isPast) {
                    return `<div class="py-1.5 px-1 rounded-lg bg-slate-100 border border-slate-200 text-slate-400 text-center text-[10px] font-mono-code line-through cursor-not-allowed">${slot.start_time}</div>`;
                }
                if (isOccupiedByOther) {
                    return `<div class="py-1.5 px-1 rounded-lg bg-rose-100 border border-rose-200 text-rose-800 text-center text-[10px] font-mono-code line-through cursor-not-allowed">${slot.start_time}</div>`;
                }
                if (isSelected) {
                    return `<button type="button" onclick="pickAdminRescheduleSlot('${slot.start_time}')" class="py-1.5 px-1 rounded-lg bg-indigo-600 border border-indigo-600 text-white text-center text-[10px] font-mono-code font-extrabold cursor-pointer">${slot.start_time} ✓</button>`;
                }
                return `<button type="button" onclick="pickAdminRescheduleSlot('${slot.start_time}')" class="py-1.5 px-1 rounded-lg bg-emerald-50 hover:bg-emerald-600 text-emerald-900 hover:text-white border border-emerald-200 text-center text-[10px] font-mono-code font-bold transition cursor-pointer">${slot.start_time}</button>`;
            }).join('');
        }

        async function submitAdminReschedule(e) {
            e.preventDefault();
            if (!admActiveRescheduleBooking) return;

            const bookingId = admActiveRescheduleBooking.id;
            const dateVal = document.getElementById('adm-rs-date').value;
            const startVal = document.getElementById('adm-rs-start').value;
            const endMins = admTimeToMinutes(startVal) + Number(admActiveRescheduleBooking.duration_minutes || 60);
            const endVal = admMinutesToTimeStr(endMins);
            const errEl = document.getElementById('adm-rs-error-msg');
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
                    closeAdminRescheduleModal();
                    showAdminAlert(
                        'success',
                        `Jadwal Booking #${updated.id} Diubah`,
                        `Jadwal baru: ${updated.date} pukul ${updated.start_hour} – ${updated.end_hour} WIB.`,
                        res.status
                    );
                    await Promise.all([loadAdminBookings(), loadAdminKpiStats()]);
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
            await loadRoomsForBookingFilter();
            await loadAdminBookings();
        });
    </script>
@endsection
