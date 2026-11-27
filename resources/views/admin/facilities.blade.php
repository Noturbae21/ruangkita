@extends('admin.layout')

@section('title', 'Master Fasilitas Tambahan — RuangKita Admin')

@section('content')
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <div class="lg:col-span-4 bg-white rounded-3xl border border-slate-200/90 shadow-2xs p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">Master Add-on</span>
                    <h1 id="af-title" class="text-lg font-extrabold text-slate-900">Tambah Fasilitas Tambahan</h1>
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
                <button type="button" onclick="loadAdminAddons()" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition cursor-pointer">
                    Muat Ulang
                </button>
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
@endsection

@section('scripts')
    <script>
        let addonsList = [];

        function formatPricingSchemeBadge(type) {
            if (type === 'PER_HOUR') return '<span class="px-2 py-0.5 rounded-md bg-sky-50 text-sky-700 border border-sky-200 text-[11px] font-bold">Per Jam</span>';
            if (type === 'PER_PARTICIPANT') return '<span class="px-2 py-0.5 rounded-md bg-violet-50 text-violet-700 border border-violet-200 text-[11px] font-bold">Per Peserta</span>';
            return '<span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 border border-slate-200 text-[11px] font-bold">Per Booking</span>';
        }

        async function loadAdminAddons() {
            const res = await fetch('/api/additional-facilities');
            const json = await res.json();
            addonsList = json.data || [];

            const tbody = document.getElementById('adm-addons-tbody');
            if (!tbody) return;

            if (!addonsList.length) {
                tbody.innerHTML = '<tr><td colspan="6" class="py-8 text-center text-slate-400">Belum ada data fasilitas tambahan. Silakan tambahkan melalui formulir di sebelah kiri.</td></tr>';
                return;
            }

            tbody.innerHTML = addonsList.map(a => `
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
            const addon = addonsList.find(a => a.id === id);
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
                loadAdminAddons();
            } else {
                showAdminAlert('error', 'Gagal Menyimpan Fasilitas Tambahan', json.message, res.status);
            }
        }

        async function deleteOrDeactivateAddon(id) {
            const addon = addonsList.find(a => a.id === id);
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
                loadAdminAddons();
            } else {
                showAdminAlert('error', 'Gagal Menghapus Fasilitas Tambahan', json.message, res.status);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadAdminAddons();
        });
    </script>
@endsection
