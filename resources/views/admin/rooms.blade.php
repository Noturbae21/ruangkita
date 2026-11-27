@extends('admin.layout')

@section('title', 'Manajemen Ruang Meeting — RuangKita Admin')

@section('content')
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <div class="lg:col-span-4 bg-white rounded-3xl border border-slate-200/90 shadow-2xs p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">Manajemen Ruangan</span>
                    <h1 id="rf-title" class="text-lg font-extrabold text-slate-900">Tambah Ruang Baru</h1>
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

                <div class="grid grid-cols-3 gap-2.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Panjang (m)</label>
                        <input type="number" id="rf-length" min="2" max="50" step="0.5" value="6.0" required
                            class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm font-medium focus:border-indigo-600 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Lebar (m)</label>
                        <input type="number" id="rf-width" min="2" max="50" step="0.5" value="4.0" required
                            class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm font-medium focus:border-indigo-600 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tipe Layout</label>
                        <select id="rf-layout"
                            class="w-full rounded-xl border border-slate-300 px-2.5 py-2 text-xs font-semibold focus:border-indigo-600 focus:outline-none">
                            <option value="BOARDROOM">Boardroom</option>
                            <option value="U_SHAPE">U-Shape</option>
                            <option value="CLASSROOM">Classroom</option>
                            <option value="POD">Huddle Pod</option>
                        </select>
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
                <p class="font-bold">Proteksi Data Ruangan:</p>
                <p class="leading-relaxed">Ruang yang masih memiliki booking mendatang tidak akan terhapus permanen, melainkan otomatis dinonaktifkan.</p>
            </div>
        </div>

        <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200/90 shadow-2xs p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">Katalog Ruangan</span>
                    <h2 class="text-lg font-extrabold text-slate-900">Daftar Ruang Meeting Coworking</h2>
                </div>
                <button type="button" onclick="loadAdminRooms()" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition cursor-pointer">
                    Muat Ulang
                </button>
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
@endsection

@section('scripts')
    <script>
        let roomsList = [];

        async function loadAdminRooms() {
            const res = await fetch('/api/rooms');
            const json = await res.json();
            roomsList = json.data || [];

            const tbody = document.getElementById('adm-rooms-tbody');
            if (!roomsList.length) {
                tbody.innerHTML = '<tr><td colspan="7" class="py-8 text-center text-slate-400">Belum ada ruang meeting yang terdaftar. Silakan tambahkan ruang baru melalui formulir di sebelah kiri.</td></tr>';
                return;
            }

            tbody.innerHTML = roomsList.map(r => `
                <tr class="hover:bg-slate-50 transition">
                    <td class="py-3.5 px-4 font-mono-code text-xs font-bold text-slate-500">#${r.id}</td>
                    <td class="py-3.5 px-4">
                        <span class="font-bold text-slate-900 block">${r.name}</span>
                        <span class="text-[11px] font-mono-code text-indigo-600 font-semibold">${r.dimensions_label || ((r.length_m || 6) + 'm × ' + (r.width_m || 4) + 'm')} (${r.area_sqm || 24} m²) · ${r.layout_label || r.layout_type || 'Boardroom'}</span>
                    </td>
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
            const room = roomsList.find(r => r.id === id);
            if (!room) return;
            document.getElementById('rf-id').value = room.id;
            document.getElementById('rf-name').value = room.name;
            document.getElementById('rf-capacity').value = room.capacity;
            document.getElementById('rf-length').value = room.length_m || 6.0;
            document.getElementById('rf-width').value = room.width_m || 4.0;
            document.getElementById('rf-layout').value = room.layout_type || 'BOARDROOM';
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
            document.getElementById('rf-length').value = '6.0';
            document.getElementById('rf-width').value = '4.0';
            document.getElementById('rf-layout').value = 'BOARDROOM';
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
                length_m: Number(document.getElementById('rf-length').value || 6.0),
                width_m: Number(document.getElementById('rf-width').value || 4.0),
                layout_type: document.getElementById('rf-layout').value || 'BOARDROOM',
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
                await Promise.all([loadAdminRooms(), loadAdminKpiStats()]);
            } else {
                showAdminAlert('error', 'Gagal Menyimpan Ruang', json.message, res.status);
            }
        }

        async function deleteOrDeactivateRoom(id) {
            const room = roomsList.find(r => r.id === id);
            if (!confirm(`Hapus atau nonaktifkan "${room ? room.name : id}"?`)) return;

            const res = await fetch(`/api/rooms/${id}`, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json' },
            });
            const json = await res.json();

            if (res.ok) {
                showAdminAlert(
                    json.action === 'deactivated' ? 'warning' : 'success',
                    json.action === 'deactivated' ? 'Ruang Dinonaktifkan' : 'Ruang Dihapus Permanen',
                    json.message,
                    res.status
                );
                await Promise.all([loadAdminRooms(), loadAdminKpiStats()]);
            } else {
                showAdminAlert('error', 'Gagal Menghapus Ruang', json.message, res.status);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadAdminRooms();
        });
    </script>
@endsection
