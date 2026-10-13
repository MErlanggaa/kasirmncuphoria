@extends('layouts.app')

@section('title', 'Riwayat & Laporan Transaksi - Danusan')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    
    <!-- Header -->
    <div>
        <div class="flex items-center gap-2 text-xs font-bold text-orange-600 uppercase tracking-wider mb-1">
            <i data-lucide="receipt" class="w-4 h-4"></i>
            <span>Log Keuangan & Penjualan</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Riwayat Semua Transaksi</h1>
        <p class="text-xs sm:text-sm text-slate-500">Daftar transaksi Beli di Tempat dan Pre-Order lengkap dengan foto bukti pembayaran QRIS.</p>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.transactions') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            
            <!-- Search -->
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Pencarian</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Invoice / Nama / PO Token..."
                    class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
            </div>

            <!-- Type -->
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Tipe Pesanan</label>
                <select name="type" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                    <option value="">Semua Tipe</option>
                    <option value="di_tempat" {{ request('type') === 'di_tempat' ? 'selected' : '' }}>Beli di Tempat</option>
                    <option value="po" {{ request('type') === 'po' ? 'selected' : '' }}>Pre-Order (PO)</option>
                </select>
            </div>

            <!-- Cashier -->
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Kasir</label>
                <select name="cashier_id" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                    <option value="">Semua Kasir</option>
                    @foreach($cashiers as $c)
                        <option value="{{ $c->id }}" {{ request('cashier_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Pickup Status -->
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Status PO</label>
                <select name="pickup_status" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                    <option value="">Semua Status</option>
                    <option value="menunggu_pengambilan" {{ request('pickup_status') === 'menunggu_pengambilan' ? 'selected' : '' }}>Menunggu Ambil</option>
                    <option value="sudah_diambil" {{ request('pickup_status') === 'sudah_diambil' ? 'selected' : '' }}>Sudah Diambil</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-2 px-3 bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs rounded-xl transition flex items-center justify-center gap-1.5 shadow-sm">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                    <span>Filter</span>
                </button>
                <a href="{{ route('admin.transactions') }}" class="py-2 px-3 border border-slate-300 hover:bg-slate-50 text-slate-600 font-bold text-xs rounded-xl transition flex items-center justify-center">
                    Reset
                </a>
            </div>

        </form>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4">Invoice / Waktu</th>
                        <th class="py-3.5 px-4">Tipe</th>
                        <th class="py-3.5 px-4">Pembeli</th>
                        <th class="py-3.5 px-4">Kasir</th>
                        <th class="py-3.5 px-4">Total</th>
                        <th class="py-3.5 px-4">Bukti Foto</th>
                        <th class="py-3.5 px-4">Status PO / Serah Terima</th>
                        <th class="py-3.5 px-4 text-right">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transactions as $tx)
                    <tr class="hover:bg-slate-50/80 transition">
                        
                        <!-- Invoice & Date -->
                        <td class="py-3 px-4">
                            <span class="font-mono font-bold text-slate-900 block">{{ $tx->invoice_number }}</span>
                            <span class="text-[10px] text-slate-400">{{ $tx->created_at->format('d M Y H:i') }}</span>
                        </td>

                        <!-- Type -->
                        <td class="py-3 px-4">
                            @if($tx->type === 'di_tempat')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-orange-50 text-orange-700 border border-orange-200">
                                    Beli di Tempat
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    Pre-Order (PO)
                                </span>
                            @endif
                        </td>

                        <!-- Customer -->
                        <td class="py-3 px-4">
                            <span class="font-bold text-slate-900 block">{{ $tx->customer_name }}</span>
                            @if($tx->customer_phone)
                                <span class="text-[11px] text-slate-500 font-mono">{{ $tx->customer_phone }}</span>
                            @endif
                        </td>

                        <!-- Cashier -->
                        <td class="py-3 px-4 font-semibold text-slate-700">
                            {{ $tx->cashier->name }}
                        </td>

                        <!-- Total -->
                        <td class="py-3 px-4 font-black text-orange-600 text-sm">
                            Rp {{ number_format($tx->total_amount, 0, ',', '.') }}
                        </td>

                        <!-- Payment Proof Photo Thumbnail -->
                        <td class="py-3 px-4">
                            @if($tx->payment_proof)
                                <button type="button" onclick="previewProofImage('{{ asset('storage/' . $tx->payment_proof) }}', '{{ $tx->invoice_number }}')"
                                    class="w-10 h-10 rounded-lg overflow-hidden border border-slate-300 hover:scale-105 transition flex-shrink-0 block shadow-xs" title="Lihat Foto Bukti">
                                    <img src="{{ asset('storage/' . $tx->payment_proof) }}" class="w-full h-full object-cover" alt="Bukti">
                                </button>
                            @else
                                <span class="text-[10px] text-slate-400 italic">Tanpa foto</span>
                            @endif
                        </td>

                        <!-- PO Status -->
                        <td class="py-3 px-4">
                            @if($tx->type === 'po')
                                <div class="space-y-0.5">
                                    <span class="font-mono text-xs font-black text-indigo-900 block">{{ $tx->po_token }}</span>
                                    @if($tx->pickup_status === 'sudah_diambil')
                                        <span class="inline-block px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-emerald-100 text-emerald-800">
                                            Diambil: {{ $tx->picked_up_at ? $tx->picked_up_at->format('d/m H:i') : 'Ya' }}
                                        </span>
                                    @else
                                        <span class="inline-block px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-amber-100 text-amber-800">
                                            Menunggu Ambil
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span class="text-[10px] text-slate-400">-</span>
                            @endif
                        </td>

                        <!-- Actions / Detail -->
                        <td class="py-3 px-4 text-right">
                            <button type="button" onclick="showDetailModal({{ json_encode($tx) }})"
                                class="p-2 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition" title="Lihat Detail Item">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-400">Tidak ada transaksi yang cocok dengan filter.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>

</div>

<!-- ================= MODAL: TRANSACTION DETAIL ================= -->
<div id="detailModal" class="fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 relative animate-in fade-in zoom-in-95 duration-200">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
            <h3 class="font-extrabold text-base text-slate-900" id="detailModalInvoice">Detail Transaksi</h3>
            <button type="button" onclick="closeDetailModal()" class="text-slate-400 hover:text-slate-600 p-1">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <div class="space-y-3 text-xs">
            <div class="bg-slate-50 p-3 rounded-2xl border border-slate-200 space-y-1.5">
                <div class="flex justify-between">
                    <span class="text-slate-500">Tipe Transaksi:</span>
                    <strong class="text-slate-900" id="detailModalType">-</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Pembeli:</span>
                    <strong class="text-slate-900" id="detailModalCustomer">-</strong>
                </div>
                <div class="flex justify-between" id="detailPhoneRow">
                    <span class="text-slate-500">No Telepon:</span>
                    <strong class="text-slate-900" id="detailModalPhone">-</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Kasir:</span>
                    <span class="text-slate-800" id="detailModalCashier">-</span>
                </div>
            </div>

            <div>
                <span class="font-bold text-slate-700 uppercase tracking-wider text-[10px] block mb-2">Item Belanja:</span>
                <div id="detailModalItems" class="space-y-1.5 max-h-48 overflow-y-auto"></div>
                <div class="flex justify-between items-center pt-2 border-t border-slate-200 font-black text-sm text-slate-900 mt-2">
                    <span>Total Pembayaran:</span>
                    <span class="text-orange-600 font-black" id="detailModalTotal">Rp 0</span>
                </div>
            </div>

            <div id="detailPoTicketLink" class="pt-2 hidden">
                <a id="btnDetailTicketView" href="#" target="_blank" class="w-full py-2.5 px-3 rounded-xl bg-indigo-50 text-indigo-700 font-bold text-xs flex items-center justify-center gap-1.5 hover:bg-indigo-100 transition">
                    <i data-lucide="qr-code" class="w-4 h-4"></i>
                    <span>Buka Halaman E-Tiket QR Pembeli</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL: PROOF IMAGE PREVIEW ================= -->
<div id="proofImageModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full p-4 shadow-2xl relative">
        <div class="flex items-center justify-between pb-2 mb-2 border-b border-slate-100">
            <span class="text-xs font-bold text-slate-800" id="proofModalInvoice">Bukti Transaksi</span>
            <button type="button" onclick="closeProofImageModal()" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="rounded-2xl overflow-hidden bg-slate-900 aspect-[4/3] flex items-center justify-center">
            <img id="proofModalImg" src="" alt="Bukti Transaksi" class="w-full h-full object-contain">
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewProofImage(url, invoice) {
        document.getElementById('proofModalImg').src = url;
        document.getElementById('proofModalInvoice').textContent = 'Bukti Transaksi ' + invoice;
        document.getElementById('proofImageModal').classList.remove('hidden');
    }

    function closeProofImageModal() {
        document.getElementById('proofImageModal').classList.add('hidden');
    }

    function showDetailModal(tx) {
        document.getElementById('detailModalInvoice').textContent = 'Invoice: ' + tx.invoice_number;
        document.getElementById('detailModalType').textContent = tx.type === 'po' ? 'Pre-Order (PO)' : 'Beli di Tempat';
        document.getElementById('detailModalCustomer').textContent = tx.customer_name;
        document.getElementById('detailModalCashier').textContent = tx.cashier.name;
        document.getElementById('detailModalTotal').textContent = 'Rp ' + Number(tx.total_amount).toLocaleString('id-ID');

        const phoneRow = document.getElementById('detailPhoneRow');
        const poLinkBox = document.getElementById('detailPoTicketLink');

        if (tx.type === 'po') {
            phoneRow.classList.remove('hidden');
            document.getElementById('detailModalPhone').textContent = tx.customer_phone || '-';
            poLinkBox.classList.remove('hidden');
            document.getElementById('btnDetailTicketView').href = "{{ url('/ticket') }}/" + tx.po_token;
        } else {
            phoneRow.classList.add('hidden');
            poLinkBox.classList.add('hidden');
        }

        const itemsEl = document.getElementById('detailModalItems');
        itemsEl.innerHTML = tx.items.map(it => `
            <div class="flex justify-between items-center bg-slate-50 p-2 rounded-xl border border-slate-200">
                <div>
                    <span class="font-bold text-slate-800">${it.product_name}</span>
                    <span class="text-orange-600 font-bold"> (${it.quantity}x)</span>
                </div>
                <span class="font-mono text-slate-600">Rp ${Number(it.subtotal).toLocaleString('id-ID')}</span>
            </div>
        `).join('');

        document.getElementById('detailModal').classList.remove('hidden');
        lucide.createIcons();
    }

    function closeDetailModal() {
        document.getElementById('detailModal').classList.add('hidden');
    }
</script>
@endpush
