@extends('customer.layout')

@section('title', 'Kalender Pemakaian Ruang — RuangKita')

@section('content')
    <section class="space-y-6">
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs p-6 space-y-5">
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-4">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">Jadwal Pemakaian Ruang</span>
                    <h1 class="text-xl font-extrabold text-slate-900">Kalender Pemakaian Ruang (Harian &amp; Mingguan)</h1>
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
@endsection

@section('scripts')
    <script>
        let calendarView = 'day';

        async function loadRoomsForCalendarFilter() {
            const res = await fetch('/api/rooms');
            const json = await res.json();
            const rooms = json.data || [];
            const calRoom = document.getElementById('cal-room-filter');
            const currentVal = calRoom.value;
            calRoom.innerHTML = '<option value="">Semua Ruang</option>';
            rooms.forEach(room => {
                const opt = document.createElement('option');
                opt.value = room.id;
                opt.textContent = room.name;
                calRoom.appendChild(opt);
            });
            calRoom.value = currentVal;
        }

        function setCalendarView(view) {
            calendarView = view;
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
            const step = calendarView === 'week' ? deltaDays * 7 : deltaDays;
            calDateEl.value = addDaysToDateString(calDateEl.value || getTodayWibString(), step);
            loadCalendar();
        }

        function resetCalendarToToday() {
            const calDateEl = document.getElementById('cal-date');
            if (!calDateEl) return;
            calDateEl.value = getTodayWibString();
            loadCalendar();
        }

        function goToBookRoomOnDate(roomId, dateStr) {
            const params = new URLSearchParams({ room_id: roomId, date: dateStr });
            window.location.href = `{{ route('booking.portal') }}?${params.toString()}`;
        }

        async function loadCalendar() {
            const date = document.getElementById('cal-date').value || getTodayWibString();
            const roomId = document.getElementById('cal-room-filter').value;
            const activeDateLabel = document.getElementById('cal-active-date-label');
            if (activeDateLabel) {
                activeDateLabel.textContent = `Menampilkan jadwal mulai: ${formatFriendlyDateWib(date)}`;
            }

            const params = new URLSearchParams({ date, view: calendarView });
            if (roomId) params.set('room_id', roomId);

            const res = await fetch(`/api/calendar?${params.toString()}`);
            const json = await res.json();
            const container = document.getElementById('calendar-container');

            if (!res.ok || !json.data) {
                container.innerHTML = '<p class="text-sm text-rose-600">Gagal memuat kalender pemakaian.</p>';
                return;
            }

            const days = json.data.days || [];
            if (!days.length || days.every(d => !d.rooms || !d.rooms.length)) {
                container.innerHTML = `
                    <div class="p-8 rounded-2xl border border-dashed border-slate-300 bg-slate-50 text-center space-y-2">
                        <p class="text-sm font-bold text-slate-700">Belum ada ruangan yang terdaftar di sistem.</p>
                        <p class="text-xs text-slate-500">Admin dapat menambahkan ruang meeting melalui Dashboard Admin.</p>
                    </div>
                `;
                return;
            }

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
                                        <button type="button" onclick="goToBookRoomOnDate(${r.room_id}, '${dayObj.date}')"
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

        document.addEventListener('DOMContentLoaded', async () => {
            document.getElementById('cal-date').value = getTodayWibString();
            await loadRoomsForCalendarFilter();
            await loadCalendar();
        });
    </script>
@endsection
