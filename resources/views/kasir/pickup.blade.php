@extends('layouts.app')

@section('title', 'Ambil Barang PO - Scan QR')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-2 text-xs font-bold text-orange-600 uppercase tracking-wider mb-1">
            <i data-lucide="scan-line" class="w-4 h-4"></i>
            <span>Verifikasi Pengambilan Pre-Order</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Scan Kode QR Pengambilan PO</h1>
        <p class="text-sm text-slate-500">Arahkan kamera ke Kode QR pada tiket pembeli atau ketikkan kode unik PO secara manual.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- LEFT: SCANNER & MANUAL INPUT -->
        <div class="lg:col-span-6 space-y-6">
            
            <!-- Camera Scanner Box -->
            <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-md">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                        <i data-lucide="camera" class="w-4 h-4 text-orange-600"></i> Kamera Scanner
                    </span>
                    <span class="text-[10px] bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded-full font-bold">Live Auto-Detect</span>
                </div>

                <div id="qrReader" class="overflow-hidden rounded-2xl bg-slate-900 aspect-square border border-slate-800">
                    <!-- Html5Qrcode renders here -->
                </div>

                <div class="mt-3 text-center space-y-2">
                    <p class="text-[11px] text-slate-500">Arahkan kamera ke QR tiket, atau pilih foto/screenshot QR dari HP:</p>
                    <div>
                        <label class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-orange-50 hover:bg-orange-100 text-orange-700 text-xs font-bold border border-orange-200 transition">
                            <i data-lucide="image" class="w-3.5 h-3.5"></i>
                            <span>Scan dari Foto / Screenshot Galeri</span>
                            <input type="file" id="qrFileInput" accept="image/*" class="hidden" onchange="scanQrFromImageFile(event)">
                        </label>
                    </div>
                </div>
            </div>

            <!-- Manual Input Alternative -->
            <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm">
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-2">
                    Atau Ketik Kode Unik Manual
                </span>
                <form id="manualTokenForm" onsubmit="handleManualSearch(event)" class="flex gap-2">
                    <div class="relative flex-1">
                        <input type="text" id="manualTokenInput" placeholder="Contoh: PO-8G2M7P"
                            class="w-full pl-3 pr-3 py-2.5 rounded-xl border border-slate-300 font-mono text-sm uppercase tracking-wider focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                    </div>
                    <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition flex items-center gap-1.5">
                        <i data-lucide="search" class="w-4 h-4"></i>
                        <span>Cek PO</span>
                    </button>
                </form>
            </div>

        </div>

        <!-- RIGHT: VERIFICATION & CLAIM CARD -->
        <div class="lg:col-span-6">
            
            <!-- Default Placeholder State -->
            <div id="poEmptyPlaceholder" class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm text-center py-16">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                    <i data-lucide="qr-code" class="w-8 h-8"></i>
                </div>
                <h3 class="font-bold text-base text-slate-800">Menunggu Hasil Scan...</h3>
                <p class="text-xs text-slate-500 max-w-xs mx-auto mt-1">
                    Detail pesanan, nama pemesan, dan tombol serah terima barang akan muncul di sini setelah kode QR berhasil terdeteksi.
                </p>
            </div>

            <!-- Verified Order Card (Initially Hidden) -->
            <div id="poOrderCard" class="hidden bg-white rounded-3xl p-6 border-2 border-slate-900 shadow-xl space-y-4 animate-in fade-in duration-200">
                
                <!-- Status Header Banner -->
                <div id="pickupStatusBanner" class="p-3.5 rounded-2xl flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i id="pickupStatusIcon" data-lucide="clock" class="w-5 h-5"></i>
                        <span id="pickupStatusText" class="text-xs font-black uppercase tracking-wider">Menunggu Pengambilan</span>
                    </div>
                    <span id="poCardToken" class="font-mono text-sm font-black px-2.5 py-0.5 rounded-lg bg-white/80 shadow-xs">PO-XXXXXX</span>
                </div>

                <!-- Customer Details -->
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-2 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 font-medium">Nama Pemesan:</span>
                        <strong id="poCustomerName" class="text-sm font-black text-slate-900">-</strong>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 font-medium">No WhatsApp:</span>
                        <a id="poCustomerPhoneLink" href="#" target="_blank" class="font-bold text-emerald-600 hover:underline flex items-center gap-1">
                            <span id="poCustomerPhone">-</span>
                            <i data-lucide="external-link" class="w-3 h-3"></i>
                        </a>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 font-medium">Invoice:</span>
                        <span id="poInvoiceNumber" class="font-mono text-slate-700">-</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 font-medium">Kasir Penerima PO:</span>
                        <span id="poCashierName" class="text-slate-700 font-semibold">-</span>
                    </div>
                    <div id="poNotesRow" class="pt-2 border-t border-slate-200">
                        <span class="text-slate-500 font-medium block">Catatan Pembeli:</span>
                        <p id="poNotes" class="italic text-slate-700 font-medium mt-0.5">-</p>
                    </div>
                </div>

                <!-- Items Ordered -->
                <div>
                    <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Item yang Harus Diserahkan:</h4>
                    <div id="poItemsContainer" class="space-y-2 max-h-48 overflow-y-auto">
                        <!-- Items rendered via JS -->
                    </div>
                    <div class="flex justify-between items-center pt-3 border-t border-slate-200 font-black text-sm text-slate-900 mt-2">
                        <span>Total Nilai Pesanan:</span>
                        <span id="poTotalAmount" class="text-orange-600 font-black">Rp 0</span>
                    </div>
                </div>

                <!-- Payment Proof Thumbnail -->
                <div id="poProofSection" class="p-3 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i data-lucide="image" class="w-4 h-4 text-slate-400"></i>
                        <span class="text-xs font-semibold text-slate-700">Foto Bukti Pembayaran QRIS</span>
                    </div>
                    <a id="poProofLink" href="#" target="_blank" class="text-xs font-bold text-orange-600 hover:underline">
                        Lihat Foto
                    </a>
                </div>

                <!-- Pickup Handover Action Button -->
                <div class="pt-2">
                    <button type="button" id="btnConfirmHandover" onclick="confirmPoHandover()"
                        class="w-full py-4 px-4 rounded-2xl text-white font-extrabold bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 shadow-xl shadow-emerald-600/30 transition transform active:scale-[0.99] flex items-center justify-center gap-2">
                        <i data-lucide="package-check" class="w-5 h-5"></i>
                        <span>Konfirmasi Serah Terima Barang</span>
                    </button>
                    
                    <div id="alreadyClaimedNotice" class="hidden text-center p-3 rounded-xl bg-slate-100 text-slate-600 text-xs font-medium">
                        Barang pesanan ini telah diambil.
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- RECENT PICKUPS TABLE -->
    <div class="mt-12 bg-white rounded-3xl p-6 border border-slate-200 shadow-sm">
        <h3 class="text-base font-extrabold text-slate-900 mb-4 flex items-center gap-2">
            <i data-lucide="history" class="w-5 h-5 text-slate-400"></i>
            <span>Riwayat Pengambilan Pre-Order Terakhir</span>
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">Kode PO</th>
                        <th class="py-3 px-4">Nama Pembeli</th>
                        <th class="py-3 px-4">Total</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Diserahkan Oleh</th>
                        <th class="py-3 px-4">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentPickups as $po)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="py-3 px-4 font-mono font-bold text-slate-900">{{ $po->po_token }}</td>
                        <td class="py-3 px-4 font-medium text-slate-800">{{ $po->customer_name }}</td>
                        <td class="py-3 px-4 font-bold text-orange-600">Rp {{ number_format($po->total_amount, 0, ',', '.') }}</td>
                        <td class="py-3 px-4">
                            @if($po->pickup_status === 'sudah_diambil')
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Sudah Diambil
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200">
                                    Menunggu
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-slate-500">
                            {{ $po->pickedUpByUser ? $po->pickedUpByUser->name : '-' }}
                        </td>
                        <td class="py-3 px-4 text-slate-400">
                            {{ $po->picked_up_at ? $po->picked_up_at->format('d M H:i') : $po->created_at->format('d M H:i') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400">Belum ada data pesanan PO.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    let activeTransaction = null;
    let html5QrcodeScanner = null;

    document.addEventListener('DOMContentLoaded', () => {
        initQrScanner();
    });

    function initQrScanner() {
        try {
            html5QrcodeScanner = new Html5QrcodeScanner(
                "qrReader",
                { 
                    fps: 10, 
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1.0,
                    showTorchButtonIfSupported: true
                },
                false
            );

            html5QrcodeScanner.render(onScanSuccess, onScanError);
        } catch (err) {
            console.warn('QR camera scanner init warning:', err);
        }
    }

    async function scanQrFromImageFile(event) {
        const file = event.target.files[0];
        if (!file) return;

        try {
            const html5QrCode = new Html5Qrcode("qrReader");
            const decodedText = await html5QrCode.scanFile(file, true);
            if (decodedText) {
                fetchAndVerifyPo(decodedText);
            }
        } catch (err) {
            console.warn('Error reading QR from image:', err);
            Swal.fire({
                icon: 'warning',
                title: 'QR Tidak Terbaca',
                text: 'Pastikan gambar/foto memuat Kode QR Tiket dengan jelas atau ketikkan kode PO secara manual.',
                confirmButtonColor: '#ea580c',
            });
        }
        event.target.value = '';
    }

    function onScanSuccess(decodedText, decodedResult) {
        console.log(`Scan matched: ${decodedText}`);
        fetchAndVerifyPo(decodedText);
    }

    function onScanError(errorMessage) {
        // Ignored for smooth loop
    }

    function handleManualSearch(e) {
        e.preventDefault();
        const token = document.getElementById('manualTokenInput').value.trim();
        if (!token) return;
        fetchAndVerifyPo(token);
    }

    async function fetchAndVerifyPo(token) {
        // Strip if full URL was scanned
        if (token.includes('/ticket/')) {
            token = token.split('/ticket/').pop();
        }

        try {
            const verifyUrl = "{{ url('/kasir/pickup/verify') }}/" + encodeURIComponent(token);
            const res = await fetch(verifyUrl);
            const data = await res.json();

            if (data.success) {
                activeTransaction = data.transaction;
                renderOrderCard(data.transaction);
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'PO Tidak Ditemukan',
                    text: data.message || 'Kode PO tidak valid.',
                    confirmButtonColor: '#ea580c',
                });
            }
        } catch (err) {
            console.error('Error verifying PO:', err);
            Swal.fire({
                icon: 'error',
                title: 'Kesalahan Sistem',
                text: 'Gagal menghubungi server verifikasi.',
            });
        }
    }

    function renderOrderCard(tx) {
        document.getElementById('poEmptyPlaceholder').classList.add('hidden');
        document.getElementById('poOrderCard').classList.remove('hidden');

        document.getElementById('poCardToken').textContent = tx.po_token;
        document.getElementById('poCustomerName').textContent = tx.customer_name;
        document.getElementById('poCustomerPhone').textContent = tx.customer_phone || '-';
        document.getElementById('poCustomerPhoneLink').href = `https://wa.me/${(tx.customer_phone || '').replace(/[^0-9]/g, '')}`;
        document.getElementById('poInvoiceNumber').textContent = tx.invoice_number;
        document.getElementById('poCashierName').textContent = tx.cashier_name;
        document.getElementById('poTotalAmount').textContent = tx.total_formatted;

        if (tx.notes) {
            document.getElementById('poNotesRow').classList.remove('hidden');
            document.getElementById('poNotes').textContent = tx.notes;
        } else {
            document.getElementById('poNotesRow').classList.add('hidden');
        }

        // Proof photo link
        if (tx.proof_image_url) {
            document.getElementById('poProofSection').classList.remove('hidden');
            document.getElementById('poProofLink').href = tx.proof_image_url;
        } else {
            document.getElementById('poProofSection').classList.add('hidden');
        }

        // Render items
        const itemsContainer = document.getElementById('poItemsContainer');
        itemsContainer.innerHTML = tx.items.map(it => `
            <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs">
                <div>
                    <span class="font-bold text-slate-800">${it.name}</span>
                    <span class="text-orange-600 font-extrabold ml-1.5">(${it.quantity} pcs)</span>
                </div>
                <span class="font-mono text-slate-600">${it.subtotal_formatted}</span>
            </div>
        `).join('');

        // Update status banner and buttons
        const banner = document.getElementById('pickupStatusBanner');
        const icon = document.getElementById('pickupStatusIcon');
        const text = document.getElementById('pickupStatusText');
        const btn = document.getElementById('btnConfirmHandover');
        const notice = document.getElementById('alreadyClaimedNotice');

        if (tx.pickup_status === 'menunggu_pengambilan') {
            banner.className = 'p-3.5 rounded-2xl flex items-center justify-between bg-amber-500 text-white shadow-md';
            icon.setAttribute('data-lucide', 'package');
            text.textContent = 'Siap Diambil (Belum Serah Terima)';
            btn.classList.remove('hidden');
            btn.disabled = false;
            notice.classList.add('hidden');
        } else {
            banner.className = 'p-3.5 rounded-2xl flex items-center justify-between bg-emerald-600 text-white shadow-md';
            icon.setAttribute('data-lucide', 'check-circle-2');
            text.textContent = `Sudah Diambil (${tx.picked_up_at || 'Sebelumnya'})`;
            btn.classList.add('hidden');
            notice.classList.remove('hidden');
            notice.textContent = `Barang telah diserahkan pada ${tx.picked_up_at || '-'} oleh kasir ${tx.picked_up_by_name || '-'}.`;
        }

        lucide.createIcons();
    }

    async function confirmPoHandover() {
        if (!activeTransaction) return;

        const btn = document.getElementById('btnConfirmHandover');
        btn.disabled = true;
        btn.innerHTML = `<span class="inline-block animate-spin mr-2">&#9696;</span> Memproses Serah Terima...`;

        try {
            const confirmUrl = "{{ url('/kasir/pickup/confirm') }}/" + activeTransaction.id;
            const res = await fetch(confirmUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });

            const data = await res.json();

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Serah Terima Berhasil!',
                    text: data.message,
                    confirmButtonColor: '#10b981',
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Konfirmasi',
                    text: data.message,
                });
                btn.disabled = false;
                btn.innerHTML = `<span>Konfirmasi Serah Terima Barang</span>`;
            }
        } catch (err) {
            console.error('Error confirming pickup:', err);
            btn.disabled = false;
            btn.innerHTML = `<span>Konfirmasi Serah Terima Barang</span>`;
        }
    }
</script>
@endpush
