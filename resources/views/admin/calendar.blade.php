@extends('admin.layout')

@section('title', 'Kalender Operasional Ruang — RuangKita Admin')

@section('content')
    <section class="bg-white rounded-3xl border border-slate-200/90 shadow-2xs p-6 space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">Monitoring Jadwal</span>
                <h1 class="text-xl font-extrabold text-slate-900">Kalender Operasional Seluruh Ruang</h1>
                <p id="adm-cal-friendly-label" class="text-xs font-semibold text-slate-500 mt-0.5"></p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <select id="adm-cal-room" onchange="loadAdminCalendar()"
                    class="rounded-xl border border-slate-300 bg-slate-50 px-3.5 py-2 text-xs font-semibold text-slate-800">
                    <option value="">Semua Ruang</option>
                </select>
                <div class="inline-flex items-center rounded-xl border border-slate-300 bg-slate-50 p-0.5">
                    <button type="button" onclick="shiftAdminCalendarDate(-1)" title="Hari Sebelumnya"
                        class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-slate-700 hover:bg-white transition cursor-pointer">&larr;</button>
                    <input type="date" id="adm-cal-date" onchange="loadAdminCalendar()"
                        class="bg-transparent px-2 py-1 text-xs font-bold text-slate-800 focus:outline-none">
                    <button type="button" onclick="shiftAdminCalendarDate(1)" title="Hari Berikutnya"
                        class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-slate-700 hover:bg-white transition cursor-pointer">&rarr;</button>
                </div>
                <button type="button" onclick="document.getElementById('adm-cal-date').value = getTodayWibString(); loadAdminCalendar();"
                    class="px-3 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 text-xs font-bold transition cursor-pointer">
                    Hari Ini
                </button>
                <select id="adm-cal-view" onchange="loadAdminCalendar()"
                    class="rounded-xl border border-slate-300 bg-slate-50 px-3.5 py-2 text-xs font-semibold text-slate-800">
                    <option value="day">Tampilan Harian</option>
                    <option value="week">Tampilan Mingguan (7 Hari)</option>
                </select>
            </div>
        </div>

        <div id="adm-calendar-container" class="space-y-6"></div>
    </section>
@endsection

@section('scripts')
    <script>
        async function loadRoomsForCalendarFilter() {
            const res = await fetch('/api/rooms');
            const json = await res.json();
            const rooms = json.data || [];
            const el = document.getElementById('adm-cal-room');
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

        function shiftAdminCalendarDate(deltaDays) {
            const calDateEl = document.getElementById('adm-cal-date');
            const viewEl = document.getElementById('adm-cal-view');
            if (!calDateEl) return;
            const step = (viewEl && viewEl.value === 'week') ? deltaDays * 7 : deltaDays;
            calDateEl.value = addDaysToDateString(calDateEl.value || getTodayWibString(), step);
            loadAdminCalendar();
        }

        async function loadAdminCalendar() {
            const date = document.getElementById('adm-cal-date').value || getTodayWibString();
            const roomId = document.getElementById('adm-cal-room').value;
            const view = document.getElementById('adm-cal-view').value;
            const friendlyLabel = document.getElementById('adm-cal-friendly-label');
            if (friendlyLabel) {
                friendlyLabel.textContent = `Menampilkan jadwal mulai: ${formatFriendlyDateWib(date)}`;
            }

            const params = new URLSearchParams({ date, view });
            if (roomId) params.set('room_id', roomId);

            const res = await fetch(`/api/calendar?${params.toString()}`);
            const json = await res.json();
            const container = document.getElementById('adm-calendar-container');

            if (!res.ok || !json.data) return;

            const days = json.data.days || [];
            if (!days.length || days.every(d => !d.rooms || !d.rooms.length)) {
                container.innerHTML = '<p class="text-sm text-slate-400 py-6 text-center">Belum ada ruang meeting yang terdaftar.</p>';
                return;
            }

            container.innerHTML = days.map(dayObj => `
                <div class="space-y-3">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-3 py-1 rounded-full bg-slate-900 text-white font-mono-code text-xs font-bold">${dayObj.date}</span>
                        <span class="text-xs font-bold text-indigo-700">${formatFriendlyDateWib(dayObj.date)}</span>
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
                                `).join('') : '<p class="text-xs text-emerald-700 font-medium">✓ Tersedia sepanjang hari (08:00 – 21:00 WIB)</p>'}
                            </div>
                        `).join('')}
                    </div>
                </div>
            `).join('');
        }

        document.addEventListener('DOMContentLoaded', async () => {
            document.getElementById('adm-cal-date').value = getTodayWibString();
            await loadRoomsForCalendarFilter();
            await loadAdminCalendar();
        });
    </script>
@endsection
