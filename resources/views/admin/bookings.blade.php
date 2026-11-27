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

        document.addEventListener('DOMContentLoaded', async () => {
            await loadRoomsForBookingFilter();
            await loadAdminBookings();
        });
    </script>
@endsection
