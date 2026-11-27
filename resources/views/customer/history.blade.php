@extends('customer.layout')

@section('title', 'Riwayat & Pembatalan Booking — RuangKita')

@section('content')
    <section class="space-y-6">
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs p-6 space-y-5">
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-4">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">Kelola Pesanan Akun Anda ({{ session('customer_name') }})</span>
                    <h1 class="text-xl font-extrabold text-slate-900">Pesanan Saya &amp; Pembatalan</h1>
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

        document.addEventListener('DOMContentLoaded', async () => {
            await loadRoomsForHistoryFilter();
            await loadBookingsList();
        });
    </script>
@endsection
