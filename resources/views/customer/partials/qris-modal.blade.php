<!-- QRIS Payment Gateway & Official Nota / Invoice Modal -->
<div id="qris-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-950/75 backdrop-blur-xs p-4 overflow-y-auto">
    <div id="qris-modal-card" class="bg-white rounded-3xl max-w-2xl w-full border border-slate-200 shadow-2xl overflow-hidden my-6">
        <!-- Top Action Bar (Hidden on Print) -->
        <div class="no-print bg-slate-900 px-6 py-3.5 text-white flex items-center justify-between border-b border-slate-800">
            <div class="flex items-center gap-2.5">
                <span class="px-2.5 py-0.5 rounded-md bg-indigo-500/20 border border-indigo-400/30 text-indigo-300 text-[10px] font-extrabold uppercase tracking-wider">Nota Resmi &amp; Bukti Reservasi</span>
                <span class="text-xs text-slate-400 hidden sm:inline">Siap dicetak atau disimpan sebagai PDF</span>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="printOfficialInvoice()"
                    class="px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold flex items-center gap-1.5 transition cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak Nota / PDF</span>
                </button>
                <button type="button" onclick="closeQrisModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white font-bold cursor-pointer">✕</button>
            </div>
        </div>

        <!-- Printable Official Invoice / Nota Sheet -->
        <div id="printable-invoice-sheet" class="p-6 sm:p-8 space-y-6 bg-white text-slate-900 relative">

            <!-- 1. Kop / Header Nota Perusahaan -->
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 border-b-2 border-slate-900 pb-5">
                <div class="flex items-start gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-600 flex items-center justify-center text-white font-extrabold shadow-sm shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-extrabold tracking-tight text-slate-900 uppercase">RUANGKITA COWORKING SPACE</h2>
                        <p class="text-xs font-medium text-slate-500">Merchant QRIS: DAVIS, SOFTWARE (GoPay Merchant)</p>
                        <p class="text-[11px] font-mono-code text-slate-500 mt-0.5">NMID QRIS: ID1026496073473 • TID: A01</p>
                    </div>
                </div>

                <div class="sm:text-right space-y-1">
                    <div class="inline-flex items-center gap-2">
                        <span class="text-[11px] font-extrabold uppercase tracking-widest text-slate-400">INVOICE / NOTA</span>
                        <span id="qm-status-badge" class="px-2.5 py-0.5 rounded-md bg-amber-400 text-slate-950 text-[10px] font-extrabold uppercase">MENUNGGU PEMBAYARAN</span>
                    </div>
                    <p id="qm-invoice-number" class="text-sm font-mono-code font-extrabold text-indigo-700">INV/RK/20261124/0001</p>
                    <p class="text-xs font-mono-code text-slate-600">Kode Booking: <strong id="qm-booking-code" class="text-slate-900">RK-20261125-0001</strong></p>
                    <p id="qm-issued-at" class="text-[11px] text-slate-400">Tanggal Cetak: -</p>
                </div>
            </div>

            <!-- 2. Grid Informasi Penyewa & Detail Jadwal Ruangan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/90 space-y-1.5 text-xs">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Ditagihkan Kepada (Penyewa)</span>
                    <p id="qm-booker-name" class="text-sm font-extrabold text-slate-900">-</p>
                    <p id="qm-booker-contact" class="text-xs font-mono-code text-slate-600">-</p>
                    <div class="pt-1.5 mt-1.5 border-t border-slate-200/70 flex items-center justify-between text-[11px]">
                        <span class="text-slate-500">Status Reservasi:</span>
                        <span id="qm-booking-status-text" class="font-extrabold text-emerald-700">CONFIRMED</span>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/90 space-y-1.5 text-xs">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Detail Ruang &amp; Jadwal Rapat</span>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Ruang Meeting:</span>
                        <span id="qm-room-info" class="font-extrabold text-slate-900 text-right">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Tanggal Rapat:</span>
                        <span id="qm-date-info" class="font-bold text-slate-900 text-right">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Waktu (WIB):</span>
                        <span id="qm-schedule-info" class="font-mono-code font-bold text-indigo-700 text-right">-</span>
                    </div>
                </div>
            </div>

            <!-- 3. Tabel Nota Rincian Layanan & Biaya -->
            <div class="rounded-2xl border border-slate-200 overflow-hidden">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-900 text-white text-[10px] font-extrabold uppercase tracking-wider">
                            <th class="py-2.5 px-3.5 w-10">No</th>
                            <th class="py-2.5 px-3.5">Deskripsi Layanan / Fasilitas</th>
                            <th class="py-2.5 px-3.5">Rincian Perhitungan</th>
                            <th class="py-2.5 px-3.5 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="qm-invoice-items-tbody" class="divide-y divide-slate-200/80 bg-white">
                    </tbody>
                    <tfoot class="bg-slate-50 border-t-2 border-slate-300 text-xs">
                        <tr>
                            <td colspan="3" class="py-2 px-3.5 text-right font-semibold text-slate-500">Subtotal Sewa Ruang:</td>
                            <td id="qm-room-price" class="py-2 px-3.5 text-right font-mono-code font-bold text-slate-800">Rp 0</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="py-2 px-3.5 text-right font-semibold text-slate-500">Subtotal Opsi Tambahan (Add-ons):</td>
                            <td id="qm-addons-subtotal" class="py-2 px-3.5 text-right font-mono-code font-bold text-indigo-700">Rp 0</td>
                        </tr>
                        <tr id="qm-penalty-row" class="hidden bg-amber-50/80">
                            <td colspan="3" class="py-2 px-3.5 text-right font-bold text-amber-800">Denda Pembatalan (&lt; 2 Jam Sebelum Mulai · 50%):</td>
                            <td id="qm-penalty-amount" class="py-2 px-3.5 text-right font-mono-code font-extrabold text-amber-800">Rp 0</td>
                        </tr>
                        <tr class="border-t border-slate-300 bg-indigo-50/60">
                            <td colspan="3" class="py-3 px-3.5 text-right font-extrabold uppercase tracking-wider text-indigo-950 text-sm">TOTAL TAGIHAN PEMBAYARAN:</td>
                            <td id="qm-total-amount" class="py-3 px-3.5 text-right font-mono-code font-extrabold text-indigo-700 text-base">Rp 0</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div id="qm-addons-breakdown" class="hidden"></div>

            <!-- 4. Bagian QRIS (Hanya Tampil di Layar, Tidak Ikut Tercetak di Nota / PDF) -->
            <div class="no-print grid grid-cols-1 sm:grid-cols-12 gap-4 items-center rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/70 p-4">
                <div class="sm:col-span-5 flex flex-col items-center text-center space-y-2">
                    <div class="flex items-center gap-1.5">
                        <span class="px-2 py-0.5 rounded bg-rose-600 text-white text-[10px] font-extrabold tracking-wider">QRIS</span>
                        <span class="text-[10px] font-extrabold uppercase text-slate-700">DAVIS, SOFTWARE</span>
                    </div>
                    <div id="qm-qr-container" class="w-44 h-44 bg-white p-2 rounded-2xl border border-slate-200 shadow-2xs flex items-center justify-center overflow-hidden">
                        <img src="{{ asset('images/qris-barcode.jpg') }}" alt="Barcode QRIS DAVIS, SOFTWARE" class="w-full h-full object-contain" />
                    </div>
                    <p id="qm-reference" class="text-[10px] font-mono-code text-slate-500">Ref: -</p>
                </div>

                <div class="sm:col-span-7 space-y-3 text-xs">
                    <div id="qm-paid-banner" class="hidden rounded-2xl bg-emerald-50 border-2 border-emerald-500/80 p-3.5 text-emerald-900 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-extrabold uppercase tracking-wider text-emerald-800">✓ PEMBAYARAN QRIS LUNAS (PAID)</span>
                            <span class="px-2 py-0.5 rounded bg-emerald-600 text-white font-mono-code text-[10px] font-bold">VERIFIED</span>
                        </div>
                        <p id="qm-paid-timestamp" class="text-[11px] font-mono-code text-emerald-700">Waktu Lunas: -</p>
                        <p class="text-[11px] text-emerald-800">Nota ini merupakan bukti pembayaran yang sah untuk penggunaan ruang rapat RuangKita.</p>
                    </div>

                    <div id="qm-unpaid-banner" class="rounded-2xl bg-amber-50 border border-amber-200 p-3.5 text-amber-900 space-y-1.5">
                        <div class="flex items-center justify-between">
                            <p class="font-extrabold text-xs">Menunggu Pembayaran &amp; Verifikasi Admin</p>
                            <span class="px-2 py-0.5 rounded bg-amber-200/80 text-amber-950 font-mono-code text-[10px] font-bold">PENDING VERIFIKASI</span>
                        </div>
                        <p class="text-[11px] leading-relaxed">1. Scan barcode QRIS di samping sesuai nominal <strong>Total Tagihan Pembayaran</strong> menggunakan m-Banking atau e-Wallet.</p>
                        <p class="text-[11px] leading-relaxed">2. Setelah melakukan pembayaran, status pesanan Anda akan diverifikasi menjadi <strong>LUNAS (PAID)</strong> oleh Admin Coworking.</p>
                    </div>

                    <div class="p-3 rounded-xl bg-white border border-slate-200/80 text-[11px] text-slate-500 space-y-1 leading-relaxed">
                        <p class="font-bold text-slate-700">Ketentuan Reservasi &amp; Pembatalan (BR-04 &amp; BR-05):</p>
                        <p>• Harap hadir 10 menit sebelum jadwal mulai dan tunjukkan kode booking ini kepada resepsionis.</p>
                        <p>• Pembatalan &ge; 2 jam sebelum jadwal mulai bebas denda (Rp 0). Pembatalan &lt; 2 jam dikenakan denda 50%.</p>
                    </div>
                </div>
            </div>

            <!-- 5. Action Buttons (Hidden on Print) -->
            <div class="no-print space-y-2.5 pt-2 border-t border-slate-100">
                <div class="flex flex-wrap items-center gap-2.5">
                    <button type="button" onclick="printOfficialInvoice()"
                        class="flex-1 py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-xs transition flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <span>Cetak Nota / Simpan PDF</span>
                    </button>
                    <button type="button" onclick="closeQrisModal()"
                        class="py-3 px-5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer">
                        Tutup Jendela
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
