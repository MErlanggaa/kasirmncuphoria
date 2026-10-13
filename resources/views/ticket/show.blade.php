@extends('layouts.app')

@section('title', 'E-Tiket Pre-Order ' . $transaction->po_token)

@section('content')
<div class="min-h-[85vh] flex items-center justify-center py-8 px-4 sm:px-6">
    <div class="max-w-md w-full">
        
        <!-- Ticket Container -->
        <div class="bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden relative">
            
            <!-- Top Orange Accent Bar -->
            <div class="h-3 bg-gradient-to-r from-orange-500 via-amber-500 to-orange-600"></div>

            <div class="p-6 text-center">
                
                <!-- Store Brand -->
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-orange-50 text-orange-700 text-xs font-bold mb-3">
                    <i data-lucide="shopping-bag" class="w-3.5 h-3.5"></i>
                    <span>{{ $storeName }}</span>
                </div>

                <h1 class="text-xl font-black text-slate-900 tracking-tight">Tiket Pengambilan Pre-Order</h1>
                <p class="text-xs text-slate-500 mt-0.5">Tunjukkan Kode QR ini ke kasir/panitia saat mengambil barang.</p>

                <!-- Status Badge -->
                <div class="my-4">
                    @if($transaction->pickup_status === 'sudah_diambil')
                        <div class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-emerald-100 text-emerald-800 text-xs font-extrabold border border-emerald-300">
                            <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                            <span>BARANG SUDAH DIAMBIL</span>
                        </div>
                    @else
                        <div class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-amber-100 text-amber-800 text-xs font-extrabold border border-amber-300 animate-pulse">
                            <i data-lucide="clock" class="w-4 h-4"></i>
                            <span>MENUNGGU PENGAMBILAN</span>
                        </div>
                    @endif
                </div>

                <!-- Unique QR Code Container -->
                <div class="bg-slate-50 p-4 rounded-3xl border-2 border-dashed border-slate-300 inline-block my-2 shadow-inner">
                    <div id="customerQrTarget" class="flex items-center justify-center min-w-[200px] min-h-[200px]"></div>
                    <span class="block mt-2 font-mono text-sm font-black text-slate-800 tracking-wider bg-white py-1 px-3 rounded-lg border border-slate-200">
                        {{ $transaction->po_token }}
                    </span>
                </div>

                <!-- Order Details Breakdown -->
                <div class="mt-5 text-left bg-slate-50 rounded-2xl p-4 border border-slate-200 text-xs space-y-2">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Nama Pemesan:</span>
                        <strong class="text-slate-900 text-sm">{{ $transaction->customer_name }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">No WhatsApp:</span>
                        <span class="text-slate-800 font-medium">{{ $transaction->customer_phone }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Nomor Invoice:</span>
                        <span class="font-mono text-slate-700">{{ $transaction->invoice_number }}</span>
                    </div>
                    @if($transaction->notes)
                    <div class="pt-2 border-t border-slate-200">
                        <span class="text-slate-500">Catatan:</span>
                        <p class="text-slate-800 italic font-medium mt-0.5">{{ $transaction->notes }}</p>
                    </div>
                    @endif

                    <!-- Items List -->
                    <div class="pt-3 border-t border-slate-200">
                        <span class="text-slate-500 font-bold uppercase tracking-wider text-[10px] block mb-1.5">Menu Pesanan:</span>
                        <div class="space-y-1.5">
                            @foreach($transaction->items as $item)
                            <div class="flex justify-between items-center bg-white p-2 rounded-xl border border-slate-100">
                                <div>
                                    <span class="font-bold text-slate-800">{{ $item->product_name }}</span>
                                    <span class="text-orange-600 font-semibold text-[11px]"> x{{ $item->quantity }}</span>
                                </div>
                                <span class="font-semibold text-slate-700">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-200 flex justify-between items-center text-sm font-black text-slate-900">
                        <span>Total (Lunas QRIS):</span>
                        <span class="text-orange-600 font-black text-base">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                    </div>

                    @if($transaction->picked_up_at)
                    <div class="pt-2 border-t border-slate-200 text-[11px] text-emerald-700">
                        <span>Diambil pada: {{ $transaction->picked_up_at->format('d M Y H:i') }}</span>
                    </div>
                    @endif
                </div>

                <!-- Action Button: Print / Screenshot Hint -->
                <div class="mt-4 flex gap-2">
                    <button type="button" onclick="window.print()" class="flex-1 py-2.5 px-4 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition flex items-center justify-center gap-1.5">
                        <i data-lucide="printer" class="w-4 h-4"></i>
                        <span>Cetak / Simpan PDF</span>
                    </button>
                </div>

            </div>

        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const qrTarget = document.getElementById('customerQrTarget');
        new QRCode(qrTarget, {
            text: "{{ $transaction->po_token }}",
            width: 200,
            height: 200,
            colorDark: "#0f172a",
            colorLight: "#f8fafc",
            correctLevel: QRCode.CorrectLevel.H
        });
    });
</script>
@endpush
