@extends('layouts.app')

@section('title', 'Pengaturan Sistem - Danusan')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    
    <!-- Header -->
    <div>
        <div class="flex items-center gap-2 text-xs font-bold text-orange-600 uppercase tracking-wider mb-1">
            <i data-lucide="settings-2" class="w-4 h-4"></i>
            <span>Konfigurasi Sistem</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Pengaturan Danusan</h1>
        <p class="text-xs sm:text-sm text-slate-500">Atur nama toko dan konfigurasi QRIS untuk pembayaran dinamis.</p>
    </div>

    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3.5 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-800 text-sm font-semibold">
        <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 flex-shrink-0"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <form action="{{ route('admin.settings.save') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf

        <!-- Store Info Card -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-5">
            <div class="pb-3 border-b border-slate-100">
                <h2 class="font-extrabold text-slate-900 flex items-center gap-2">
                    <i data-lucide="store" class="w-5 h-5 text-orange-600"></i>
                    Informasi Toko / Organisasi
                </h2>
                <p class="text-xs text-slate-500 mt-1">Nama ini tampil di tampilan kasir dan tiket PO pembeli.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Toko / Event <span class="text-rose-500">*</span></label>
                    <input type="text" name="store_name" value="{{ old('store_name', $settings['store_name'] ?? '') }}" required
                        placeholder="Contoh: KEDAIPHORIA, KBN JRK"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                    <p class="text-[10px] text-slate-400 mt-1">Tampil di header kasir & tiket pembeli</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Merchant QRIS</label>
                    <input type="text" name="merchant_name" value="{{ old('merchant_name', $settings['merchant_name'] ?? '') }}"
                        placeholder="Contoh: KEDAIPHORIA, KBN JRK"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                    <p class="text-[10px] text-slate-400 mt-1">Tampil di header QRIS modal kasir</p>
                </div>
            </div>
        </div>

        <!-- QRIS Settings Card -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-5">
            <div class="pb-3 border-b border-slate-100">
                <h2 class="font-extrabold text-slate-900 flex items-center gap-2">
                    <i data-lucide="qr-code" class="w-5 h-5 text-red-600"></i>
                    Konfigurasi QRIS Dinamis
                </h2>
                <p class="text-xs text-slate-500 mt-1">Masukkan string payload QRIS statis dari merchant. Sistem akan mengkonversinya menjadi QRIS dinamis dengan nominal tepat setiap transaksi.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Static QRIS Payload String <span class="text-rose-500">*</span></label>
                <textarea name="qris_static_payload" rows="4" required
                    placeholder="00020101021126610014COM.GO-JEK.WWW01189360091431783159330210G1783159330303UMI..."
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-mono focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition resize-y">{{ old('qris_static_payload', $settings['qris_static_payload'] ?? '') }}</textarea>
                <p class="text-[11px] text-slate-500 mt-1.5 leading-relaxed">
                    Salin string QRIS statis dari aplikasi merchant Anda (QRIS GoPay, BCA, BNI, OVO, dll). Pastikan adalah payload <strong>STATIS</strong>, bukan QR gambar.
                    Sistem akan otomatis mengubah ke QRIS dinamis dengan CRC yang valid.
                </p>
            </div>

            <!-- Current QRIS Preview -->
            @if(!empty($settings['qris_static_payload']))
            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                        <i data-lucide="eye" class="w-4 h-4 text-orange-500"></i>
                        Preview QRIS Saat Ini
                    </span>
                    <span class="text-[10px] text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full font-bold">Aktif</span>
                </div>
                <div class="flex flex-col sm:flex-row items-center gap-4">
                    <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-xs inline-block">
                        <div id="settingsQrPreview" class="flex items-center justify-center min-w-[120px] min-h-[120px]"></div>
                    </div>
                    <div class="text-xs space-y-1 text-slate-600">
                        <p class="font-bold text-slate-900">{{ $settings['merchant_name'] ?? $settings['store_name'] ?? 'N/A' }}</p>
                        <p class="font-mono text-[10px] text-slate-500 break-all max-w-xs">{{ Str::limit($settings['qris_static_payload'], 60) }}</p>
                        <span class="inline-block px-2 py-0.5 rounded bg-emerald-100 text-emerald-700 font-bold text-[10px]">QRIS Aktif — Dinamis siap digunakan</span>
                    </div>
                </div>
            </div>
            @endif

        </div>

        <!-- Save Button -->
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.dashboard') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold text-sm hover:bg-slate-50 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-extrabold text-sm transition shadow-lg shadow-orange-600/20 flex items-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i>
                Simpan Pengaturan
            </button>
        </div>

    </form>

</div>
@endsection

@push('scripts')
<script>
    @if(!empty($settings['qris_static_payload']))
    document.addEventListener('DOMContentLoaded', () => {
        try {
            new QRCode(document.getElementById('settingsQrPreview'), {
                text: "{{ addslashes($settings['qris_static_payload']) }}",
                width: 120,
                height: 120,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.L
            });
        } catch(e) {}
    });
    @endif
</script>
@endpush
