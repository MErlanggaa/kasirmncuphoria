@extends('layouts.app')

@section('title', 'Admin Dashboard - Danusan')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    
    <!-- Welcome Header & Quick Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-indigo-600 uppercase tracking-wider mb-1">
                <i data-lucide="shield-check" class="w-4 h-4"></i>
                <span>Panel Manajemen Admin</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Ringkasan Penjualan Danusan</h1>
            <p class="text-xs sm:text-sm text-slate-500">Pantau performa kasir, perputaran kas, dan status serah terima PO secara real-time.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.pickup') }}" class="px-4 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs transition shadow-md shadow-orange-600/20 flex items-center gap-2">
                <i data-lucide="scan-line" class="w-4 h-4"></i>
                <span>Scan Pengambilan PO</span>
            </a>
            <a href="{{ route('admin.transactions') }}" class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs transition shadow-xs flex items-center gap-2">
                <i data-lucide="receipt" class="w-4 h-4"></i>
                <span>Semua Transaksi</span>
            </a>
        </div>
    </div>

    <!-- METRICS CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Total Omset -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Pendapatan</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="wallet" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-2xl font-black text-slate-900 mt-2">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
            <div class="mt-2 text-xs text-emerald-600 font-semibold flex items-center gap-1">
                <i data-lucide="trending-up" class="w-3.5 h-3.5"></i>
                <span>Hari ini: Rp {{ number_format($todayRevenue, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Total Transaksi -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Transaksi</span>
                <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center">
                    <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-2xl font-black text-slate-900 mt-2">{{ $totalTransactions }} <span class="text-xs text-slate-400 font-normal">order</span></p>
            <p class="mt-2 text-xs text-slate-500 font-medium">
                {{ $diTempatCount }} Beli di Tempat &bull; {{ $poCount }} PO
            </p>
        </div>

        <!-- PO Menunggu Pengambilan -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">PO Siap Diambil</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-2xl font-black text-amber-600 mt-2">{{ $poWaitingCount }} <span class="text-xs text-slate-400 font-normal">paket</span></p>
            <p class="mt-2 text-xs text-slate-500 font-medium">
                Menunggu pembeli scan tiket QR
            </p>
        </div>

        <!-- PO Sudah Diambil -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">PO Selesai Diserahkan</span>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <i data-lucide="package-check" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-2xl font-black text-indigo-600 mt-2">{{ $poPickedUpCount }} <span class="text-xs text-slate-400 font-normal">paket</span></p>
            <p class="mt-2 text-xs text-slate-500 font-medium">
                Telah terverifikasi serah terima
            </p>
        </div>

    </div>

    <!-- CASHIERS PERFORMANCE LEADERBOARD & RECENT TRANSACTIONS -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- CASHIERS LEADERBOARD -->
        <div class="lg:col-span-4 bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Performa Kasir</h3>
                    <p class="text-[11px] text-slate-500">Perolehan omset per akun kasir</p>
                </div>
                <a href="{{ route('admin.cashiers') }}" class="text-xs font-bold text-orange-600 hover:underline">Kelola &rarr;</a>
            </div>

            <div class="space-y-3">
                @forelse($cashiers as $kasir)
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-700 font-extrabold flex items-center justify-center text-sm">
                            {{ strtoupper(substr($kasir->name, 0, 1)) }}
                        </div>
                        <div>
                            <h4 class="font-bold text-xs text-slate-900">{{ $kasir->name }}</h4>
                            <p class="text-[10px] text-slate-500">{{ $kasir->transactions_count }} transaksi</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-black text-slate-900 block">Rp {{ number_format($kasir->transactions_sum_total_amount ?? 0, 0, ',', '.') }}</span>
                        <span class="text-[10px] font-semibold {{ $kasir->is_active ? 'text-emerald-600' : 'text-rose-500' }}">
                            {{ $kasir->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400 text-center py-4">Belum ada akun kasir.</p>
                @endforelse
            </div>
        </div>

        <!-- RECENT TRANSACTIONS TABLE -->
        <div class="lg:col-span-8 bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Transaksi Terbaru</h3>
                    <p class="text-[11px] text-slate-500">8 transaksi penjualan terakhir</p>
                </div>
                <a href="{{ route('admin.transactions') }}" class="text-xs font-bold text-orange-600 hover:underline">Lihat Semua &rarr;</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-3">Invoice</th>
                            <th class="py-3 px-3">Tipe</th>
                            <th class="py-3 px-3">Pembeli</th>
                            <th class="py-3 px-3">Kasir</th>
                            <th class="py-3 px-3">Total</th>
                            <th class="py-3 px-3">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentTransactions as $tx)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-3 font-mono font-bold text-slate-900">
                                {{ $tx->invoice_number }}
                                @if($tx->po_token)
                                    <span class="block text-[10px] text-indigo-600 font-mono">{{ $tx->po_token }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-3">
                                @if($tx->type === 'di_tempat')
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-orange-50 text-orange-700 border border-orange-200">
                                        Langsung
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        Pre-Order
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-3 font-medium text-slate-800">{{ $tx->customer_name }}</td>
                            <td class="py-3 px-3 text-slate-500">{{ $tx->cashier->name }}</td>
                            <td class="py-3 px-3 font-bold text-slate-900">Rp {{ number_format($tx->total_amount, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-slate-400">{{ $tx->created_at->format('d M H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">Belum ada transaksi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

    </div>

</div>
@endsection
