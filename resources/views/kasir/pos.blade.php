@extends('layouts.app')

@section('title', 'Kasir POS - Penjualan Danus')

@section('content')
<div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-6 pb-28 lg:pb-12">
    
    <!-- Top Cashier Header & Stats Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-950 rounded-2xl sm:rounded-3xl p-4 sm:p-6 text-white shadow-xl shadow-slate-900/10 mb-4 sm:mb-6 relative overflow-hidden">
        <div class="absolute -right-8 -bottom-8 w-48 h-48 bg-orange-500/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-orange-500/20 border border-orange-500/30 flex items-center justify-center text-orange-400 font-extrabold text-xl sm:text-2xl shadow-inner flex-shrink-0">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] sm:text-xs uppercase tracking-wider font-semibold text-orange-400 bg-orange-500/10 px-2 py-0.5 rounded-full border border-orange-500/20">Kasir Bertugas</span>
                        <span class="text-[10px] sm:text-xs text-slate-400">{{ date('d M Y') }}</span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black tracking-tight text-white mt-0.5">{{ Auth::user()->name }}</h1>
                    <p class="text-xs text-orange-300 font-semibold">{{ $storeName }}</p>
                </div>
            </div>

            <!-- Cashier Today Shift Stats -->
            <div class="flex items-center justify-between sm:justify-start gap-3 sm:gap-6 bg-white/5 border border-white/10 rounded-2xl p-3 sm:px-5">
                <div>
                    <p class="text-[10px] sm:text-[11px] text-slate-400 uppercase tracking-wider font-medium">Omset Shift Anda</p>
                    <p class="text-base sm:text-xl font-black text-amber-400">Rp {{ number_format($todayTotal, 0, ',', '.') }}</p>
                </div>
                <div class="h-8 w-px bg-white/10"></div>
                <div>
                    <p class="text-[10px] sm:text-[11px] text-slate-400 uppercase tracking-wider font-medium">Transaksi</p>
                    <p class="text-base sm:text-xl font-bold text-white">{{ $todayCount }} <span class="text-[11px] text-slate-400 font-normal">({{ $todayDiTempatCount }} Langsung / {{ $todayPoCount }} PO)</span></p>
                </div>
                <div class="h-8 w-px bg-white/10 hidden sm:block"></div>
                <a href="{{ route('kasir.pickup') }}" class="hidden sm:flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold transition shadow-md shadow-orange-500/20">
                    <i data-lucide="scan-line" class="w-4 h-4"></i>
                    <span>Scan Ambil PO</span>
                </a>
            </div>

        </div>
    </div>

    <!-- MAIN TWO OPTIONS SWITCHER: BELI DI TEMPAT vs PRE-ORDER (PO) -->
    <div class="bg-white rounded-2xl p-1.5 sm:p-2 shadow-xs border border-slate-200 mb-4 sm:mb-6 flex gap-1.5 sm:gap-2">
        <button type="button" id="tabDiTempat" onclick="switchOrderType('di_tempat')"
            class="flex-1 py-2.5 sm:py-3 px-3 sm:px-4 rounded-xl font-extrabold text-xs sm:text-sm transition flex items-center justify-center gap-1.5 sm:gap-2.5 bg-orange-500 text-white shadow-md shadow-orange-500/20">
            <i data-lucide="shopping-bag" class="w-4 h-4 sm:w-5 sm:h-5"></i>
            <span>1. Beli di Tempat</span>
            <span class="text-[10px] font-semibold bg-white/20 px-1.5 py-0.5 rounded-full hidden sm:inline">Menu & Nama</span>
        </button>

        <button type="button" id="tabPO" onclick="switchOrderType('po')"
            class="flex-1 py-2.5 sm:py-3 px-3 sm:px-4 rounded-xl font-bold text-xs sm:text-sm transition flex items-center justify-center gap-1.5 sm:gap-2.5 text-slate-600 hover:bg-slate-100">
            <i data-lucide="clipboard-list" class="w-4 h-4 sm:w-5 sm:h-5"></i>
            <span>2. Pre-Order (PO)</span>
            <span class="text-[10px] font-semibold bg-slate-200 text-slate-700 px-1.5 py-0.5 rounded-full hidden sm:inline">Form & QR Unik</span>
        </button>
    </div>

    <!-- MAIN GRID: CATALOG (LEFT) & ORDER DRAWER (RIGHT) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6 items-start">
        
        <!-- LEFT: MENU CATALOG -->
        <div class="lg:col-span-7 xl:col-span-8 space-y-4">
            
            <!-- Category Filter Tabs & Search Bar -->
            <div class="bg-white p-3 sm:p-4 rounded-2xl border border-slate-200 shadow-xs space-y-3 sticky top-16 z-20">
                <div class="flex gap-2 items-center justify-between">
                    <!-- Search Input -->
                    <div class="relative flex-1">
                        <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" id="menuSearch" oninput="filterMenuItems()"
                            placeholder="Cari menu (Risol, Es Teh, Dimsum)..."
                            class="w-full pl-9 pr-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                    </div>

                    <!-- Clear Search -->
                    <button type="button" onclick="document.getElementById('menuSearch').value=''; filterMenuItems();" class="text-xs text-slate-500 hover:text-slate-800 whitespace-nowrap px-2 py-1">
                        Reset
                    </button>
                </div>

                <!-- Category Pills -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 no-scrollbar" id="categoryPills">
                    <button type="button" onclick="filterCategory('ALL')" class="category-pill active-pill px-3 py-1.5 rounded-xl text-xs font-bold transition bg-orange-500 text-white whitespace-nowrap">
                        Semua ({{ $products->count() }})
                    </button>
                    @foreach($categories as $cat)
                        <button type="button" onclick="filterCategory('{{ $cat }}')" class="category-pill px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition whitespace-nowrap">
                            {{ $cat }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Product Cards Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-2.5 sm:gap-4" id="productsGrid">
                @forelse($products as $product)
                <div class="product-card bg-white rounded-2xl border border-slate-200 p-2.5 sm:p-3 shadow-xs hover:shadow-md transition flex flex-col justify-between group"
                    data-id="{{ $product->id }}"
                    data-name="{{ strtolower($product->name) }}"
                    data-category="{{ $product->category }}"
                    data-price="{{ $product->price }}"
                    data-status="{{ $product->status }}">
                    
                    <div>
                        <!-- Product Image -->
                        <div class="relative aspect-square rounded-xl overflow-hidden bg-slate-100 mb-2">
                            @if($product->image)
                                <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" loading="lazy">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-300">
                                    <i data-lucide="utensils" class="w-8 h-8"></i>
                                </div>
                            @endif
                            <span class="absolute top-1.5 left-1.5 bg-slate-900/80 backdrop-blur-xs text-white text-[9px] sm:text-[10px] font-bold px-1.5 py-0.5 rounded">
                                {{ $product->category }}
                            </span>
                        </div>

                        <!-- Product Info -->
                        <h3 class="font-bold text-xs sm:text-sm text-slate-900 line-clamp-2 leading-snug">{{ $product->name }}</h3>
                        @if($product->description)
                            <p class="text-[10px] sm:text-[11px] text-slate-500 line-clamp-1 mt-0.5">{{ $product->description }}</p>
                        @endif
                    </div>

                    <div class="mt-2.5 pt-2 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-xs sm:text-sm font-black text-orange-600">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </span>

                        <!-- Add to Cart Button -->
                        <button type="button" onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }})"
                            class="w-8 h-8 rounded-xl bg-orange-500 hover:bg-orange-600 text-white flex items-center justify-center transition active:scale-95 shadow-xs" title="Tambah ke keranjang">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                        </button>
                    </div>

                </div>
                @empty
                <div class="col-span-full py-12 text-center text-slate-400 bg-white rounded-2xl border border-slate-200">
                    <i data-lucide="inbox" class="w-12 h-12 mx-auto text-slate-300 mb-2"></i>
                    <p class="font-bold text-slate-700">Belum ada menu tersedia</p>
                    <p class="text-xs">Silakan minta Admin untuk menambahkan menu terlebih dahulu.</p>
                </div>
                @endforelse
            </div>

        </div>

        <!-- RIGHT: ORDER FORM & CART SUMMARY -->
        <div id="orderFormSection" class="lg:col-span-5 xl:col-span-4 sticky top-20">
            <div class="bg-white rounded-3xl border border-slate-200 p-4 sm:p-5 shadow-lg shadow-slate-200/50 space-y-4">
                
                <!-- Order Mode Header -->
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-orange-600 bg-orange-50 px-2 py-0.5 rounded-md" id="badgeOrderMode">
                            Beli di Tempat
                        </span>
                        <h2 class="text-base font-extrabold text-slate-900 mt-1">Form Data Pesanan</h2>
                    </div>

                    <button type="button" onclick="clearCart()" class="text-xs text-rose-500 hover:text-rose-700 font-semibold flex items-center gap-1">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                        <span>Kosongkan</span>
                    </button>
                </div>

                <!-- CUSTOMER INPUT SECTION -->
                <div class="space-y-3 bg-slate-50 p-3.5 rounded-2xl border border-slate-200">
                    
                    <!-- Nama Pembeli (Required for both) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Nama Pembeli <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <i data-lucide="user" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                            <input type="text" id="customerName" required placeholder="Contoh: Kak Arya / Dika"
                                class="w-full pl-9 pr-3 py-2 text-sm rounded-xl border border-slate-300 bg-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                        </div>
                    </div>

                    <!-- PO SPECIFIC FIELDS (Hidden if Beli di Tempat) -->
                    <div id="poExtraFields" class="space-y-3 hidden">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                No WhatsApp / Telepon <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <i data-lucide="phone" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                                <input type="tel" id="customerPhone" placeholder="081234567890"
                                    class="w-full pl-9 pr-3 py-2 text-sm rounded-xl border border-slate-300 bg-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                            </div>
                            <p class="text-[10px] text-slate-500 mt-1">Tiket QR pengambilan akan dikirim via WA.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Catatan / Jadwal Pengambilan PO
                            </label>
                            <input type="text" id="customerNotes" placeholder="Contoh: Ambil jam 13:00 di Selasar Gedung B"
                                class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 bg-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                        </div>
                    </div>

                </div>

                <!-- CART ITEMS LIST -->
                <div>
                    <div class="flex items-center justify-between text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        <span>Daftar Menu Dipilih</span>
                        <span id="cartCountBadge" class="text-slate-400 font-bold">0 Item</span>
                    </div>

                    <div id="cartItemsContainer" class="max-h-56 overflow-y-auto space-y-2 pr-1">
                        <div id="cartEmptyState" class="py-6 text-center text-slate-400 border border-dashed border-slate-200 rounded-2xl">
                            <i data-lucide="shopping-cart" class="w-7 h-7 mx-auto text-slate-300 mb-1"></i>
                            <p class="text-xs font-medium">Keranjang masih kosong</p>
                            <p class="text-[10px]">Klik tombol (+) pada menu produk</p>
                        </div>
                    </div>
                </div>

                <!-- ORDER TOTAL & CHECKOUT BUTTON -->
                <div class="pt-3 border-t border-slate-100 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Pembayaran</span>
                        <span class="text-xl font-black text-slate-900" id="cartTotalDisplay">Rp 0</span>
                    </div>

                    <button type="button" id="btnProceedPayment" onclick="openPaymentModal()"
                        class="w-full py-3.5 px-4 rounded-xl text-white font-extrabold bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-700 hover:to-amber-600 shadow-lg shadow-orange-500/25 transition transform active:scale-[0.99] flex items-center justify-center gap-2">
                        <i data-lucide="qr-code" class="w-5 h-5"></i>
                        <span>Lanjut ke QRIS Dinamis</span>
                    </button>
                </div>

            </div>
        </div>

    </div>

    <!-- FLOATING MOBILE BOTTOM BAR (FOR EASY CHECKOUT ON PHONES) -->
    <div id="mobileFloatingBar" class="lg:hidden fixed bottom-0 left-0 right-0 z-30 bg-white/95 backdrop-blur-md border-t border-slate-200 px-4 py-3 shadow-2xl flex items-center justify-between gap-3">
        <div>
            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider" id="mobileBarItemCount">0 Menu Dipilih</p>
            <p class="text-base font-black text-slate-900" id="mobileBarTotal">Rp 0</p>
        </div>
        <button type="button" onclick="scrollToOrderForm()" class="py-2.5 px-4 bg-orange-600 hover:bg-orange-700 text-white font-extrabold text-xs rounded-xl shadow-md shadow-orange-600/20 flex items-center gap-1.5 transition active:scale-95">
            <span>Bayar / Isi Form</span>
            <i data-lucide="arrow-down" class="w-3.5 h-3.5"></i>
        </button>
    </div>

</div>

<!-- HIDDEN NATIVE CAMERA & FILE INPUTS (FOR 100% RELIABILITY ON MOBILE BROWSERS & HTTP) -->
<input type="file" id="nativeCameraInput" accept="image/*" capture="environment" class="hidden" onchange="handleFileCapture(event)">
<input type="file" id="galleryFileInput" accept="image/*" class="hidden" onchange="handleFileCapture(event)">

<!-- ================= MODAL 1: QRIS DINAMIS ================= -->
<div id="modalQris" class="fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 hidden">
    <div class="bg-white rounded-3xl max-w-sm w-full p-5 sm:p-6 shadow-2xl border border-slate-100 relative animate-in fade-in zoom-in-95 duration-200 max-h-[92vh] overflow-y-auto">
        
        <!-- Close Button -->
        <button type="button" onclick="closeQrisModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 p-1 rounded-full">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>

        <!-- QRIS Card Content -->
        <div class="text-center">
            
            <!-- Official QRIS Header Matching Real User Poster -->
            <div class="flex items-center justify-between mb-2 border-b border-slate-100 pb-2">
                <div class="flex items-center gap-1.5">
                    <span class="text-red-600 font-black tracking-tighter text-2xl">QRIS</span>
                    <span class="text-[9px] text-slate-500 font-semibold leading-tight text-left">QR Code Standar<br>Pembayaran Nasional</span>
                </div>
                <div class="flex items-center gap-1 text-red-600 font-bold text-xs">
                    <i data-lucide="shield-check" class="w-4 h-4 text-red-600"></i>
                    <span>GPN</span>
                </div>
            </div>
            
            <div>
                <h3 class="text-base font-black text-slate-900 tracking-tight" id="qrisMerchantName">{{ $merchantName }}</h3>
                <p class="text-[11px] font-mono text-slate-500 font-bold" id="qrisMerchantNmid">{{ $qrisNmid ?? 'ID10265737707360' }}</p>
                <span class="inline-block text-[10px] font-mono font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded mt-0.5">QRIS</span>
            </div>

            <!-- Exact Nominal Badge -->
            <div class="my-3 py-2 px-3 bg-orange-50 border border-orange-200 rounded-2xl">
                <span class="text-[10px] text-orange-800 font-bold uppercase tracking-wider block">Total yang Harus Dibayar:</span>
                <span class="text-2xl sm:text-3xl font-black text-orange-600 tracking-tight" id="modalTotalNominal">Rp 0</span>
                <span class="text-[9px] text-orange-700 block mt-0.5 font-medium">Nominal terkunci otomatis di GoPay / BCA / ShopeePay / OVO / Dana</span>
            </div>

            <!-- Dynamic QR Code Container -->
            <div class="bg-white p-3 rounded-2xl border-2 border-slate-900 shadow-sm inline-block my-1">
                <div id="qrisCodeTarget" class="flex items-center justify-center min-w-[190px] min-h-[190px]">
                    <!-- QR code will be generated here -->
                </div>
            </div>

            <p class="text-[10px] text-slate-400 mt-1">SATU QRIS UNTUK SEMUA &bull; ASPI-QRIS.ID</p>

            <!-- Action Button: Open Camera for Proof -->
            <div class="mt-4 space-y-2">
                <button type="button" onclick="openCameraModal()"
                    class="w-full py-3.5 px-4 rounded-xl text-white font-extrabold bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 shadow-lg shadow-emerald-600/25 transition transform active:scale-[0.99] flex items-center justify-center gap-2">
                    <i data-lucide="camera" class="w-5 h-5"></i>
                    <span>Foto Bukti Transaksi QRIS</span>
                </button>
                <p class="text-[10px] text-slate-400">Kasir memfoto layar bukti bayar pembeli untuk konfirmasi</p>
            </div>

        </div>

    </div>
</div>

<!-- ================= MODAL 2: CAMERA SNAPSHOT (MOBILE OPTIMIZED) ================= -->
<div id="modalCamera" class="fixed inset-0 z-50 bg-slate-900/85 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4 hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-4 sm:p-6 shadow-2xl border border-slate-100 relative animate-in fade-in zoom-in-95 duration-200 max-h-[95vh] overflow-y-auto">
        
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center">
                    <i data-lucide="camera" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-sm text-slate-900">Foto Bukti Transaksi</h3>
                    <p class="text-[10px] text-slate-500">Pilih opsi foto bukti pembayaran di bawah</p>
                </div>
            </div>
            <button type="button" onclick="closeCameraModal()" class="text-slate-400 hover:text-slate-600 p-1">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- CAMERA VIEWPORT / LIVE PREVIEW / CAPTURED PREVIEW -->
        <div class="relative bg-slate-950 rounded-2xl overflow-hidden aspect-[4/3] flex items-center justify-center border border-slate-800">
            <!-- Video element for live feed (desktop or HTTPS) -->
            <video id="cameraVideo" autoplay playsinline class="hidden w-full h-full object-cover"></video>
            
            <!-- Snapshot Preview Canvas -->
            <canvas id="cameraCanvas" class="hidden w-full h-full object-cover"></canvas>
            
            <!-- Image element for captured / uploaded photo -->
            <img id="imagePreviewFallback" class="hidden w-full h-full object-cover" alt="Preview Bukti">

            <!-- Initial Idle View with Quick Action Button -->
            <div id="cameraIdleView" class="text-center p-6 space-y-3">
                <div class="w-14 h-14 rounded-2xl bg-white/10 text-orange-400 mx-auto flex items-center justify-center">
                    <i data-lucide="camera" class="w-7 h-7"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-white">Siap Mengambil Foto</p>
                    <p class="text-[10px] text-slate-400 mt-0.5">Gunakan kamera HP atau webcam laptop</p>
                </div>
            </div>

            <!-- Watermark Overlay (Visible on snap) -->
            <div id="cameraFrameOverlay" class="hidden absolute inset-3 border-2 border-dashed border-white/60 rounded-xl pointer-events-none flex flex-col justify-between p-2">
                <span class="text-[9px] text-white/90 bg-black/60 backdrop-blur-xs px-2 py-0.5 rounded self-start font-mono font-bold">KEDAIPHORIA BUKTI</span>
                <span class="text-[9px] text-white/90 bg-black/60 backdrop-blur-xs px-2 py-0.5 rounded self-end font-mono" id="overlayAmountStamp">LUNAS</span>
            </div>
        </div>

        <!-- CAMERA ACTION BUTTONS -->
        <div class="mt-4 space-y-2.5">
            
            <!-- Default / Idle Actions -->
            <div id="cameraInitialControls" class="space-y-2">
                
                <!-- 1. NATIVE MOBILE CAMERA (Primary & Most Reliable on Phone) -->
                <button type="button" onclick="triggerNativeCamera()"
                    class="w-full py-3.5 px-4 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-extrabold text-xs sm:text-sm transition shadow-lg shadow-orange-600/30 flex items-center justify-center gap-2">
                    <i data-lucide="smartphone" class="w-4 h-4"></i>
                    <span>Buka Kamera HP Langsung</span>
                </button>

                <!-- 2. LIVE STREAM WEBCAM (For Laptop / Desktop) -->
                <button type="button" onclick="startWebcamLiveStream()"
                    class="w-full py-2.5 px-3 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold text-xs transition flex items-center justify-center gap-2">
                    <i data-lucide="video" class="w-4 h-4 text-slate-500"></i>
                    <span>Buka Live Webcam</span>
                </button>

                <!-- 3. FILE / GALLERY & SIMULATOR ROW -->
                <div class="grid grid-cols-2 gap-2 pt-1">
                    <button type="button" onclick="triggerGalleryUpload()"
                        class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition flex items-center justify-center gap-1.5">
                        <i data-lucide="image" class="w-3.5 h-3.5 text-slate-500"></i>
                        <span>Pilih dari Galeri</span>
                    </button>

                    <button type="button" onclick="generateSimulatedProof()"
                        class="py-2 px-3 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 font-semibold text-xs transition flex items-center justify-center gap-1.5 border border-amber-200">
                        <i data-lucide="sparkles" class="w-3.5 h-3.5 text-amber-600"></i>
                        <span>Simulasi (Cepat)</span>
                    </button>
                </div>
            </div>

            <!-- Live Webcam Shutter Controls (Only visible when webcam stream is active) -->
            <div id="cameraLiveControls" class="hidden flex items-center gap-2">
                <button type="button" onclick="switchCameraFacing()" class="p-3 rounded-xl bg-slate-100 text-slate-700" title="Beralih Kamera">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                </button>
                <button type="button" onclick="takeWebcamSnapshot()"
                    class="flex-1 py-3 px-4 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-extrabold text-xs transition shadow-md flex items-center justify-center gap-2">
                    <i data-lucide="aperture" class="w-4 h-4"></i>
                    <span>Jepret Foto Sekarang</span>
                </button>
            </div>

            <!-- Preview Confirmation Controls (Shown after a photo is captured / chosen) -->
            <div id="cameraPreviewControls" class="hidden space-y-2">
                <div class="flex items-center gap-2">
                    <button type="button" onclick="retakePhoto()" class="flex-1 py-3 px-3 rounded-xl border border-slate-300 hover:bg-slate-100 text-slate-700 font-bold text-xs transition flex items-center justify-center gap-1.5">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                        <span>Foto Ulang</span>
                    </button>

                    <button type="button" id="btnSubmitOrder" onclick="submitCompleteTransaction()" class="flex-1 py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs transition shadow-md shadow-emerald-600/30 flex items-center justify-center gap-2">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span>Simpan & Selesai</span>
                    </button>
                </div>
            </div>

        </div>

    </div>
</div>

<!-- ================= MODAL 3: TRANSAKSI BERHASIL (STRUK / TIKET PO QR) ================= -->
<div id="modalSuccess" class="fixed inset-0 z-50 bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4 hidden">
    <div class="bg-white rounded-3xl max-w-sm sm:max-w-md w-full p-5 sm:p-6 shadow-2xl border border-slate-100 relative animate-in fade-in zoom-in-95 duration-200 max-h-[92vh] overflow-y-auto">
        
        <div class="text-center space-y-3">
            
            <!-- Success Icon -->
            <div class="w-14 h-14 rounded-full bg-emerald-100 text-emerald-600 mx-auto flex items-center justify-center shadow-lg shadow-emerald-500/20">
                <i data-lucide="check-circle-2" class="w-8 h-8"></i>
            </div>

            <h3 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight">Transaksi Berhasil!</h3>
            <p class="text-xs text-slate-500" id="successSubtitle">Tersimpan ke database {{ $storeName }}.</p>

            <!-- PO UNIQUE QR CODE SECTION (Only for PO) -->
            <div id="poTicketSuccessSection" class="hidden bg-amber-50/70 border-2 border-dashed border-amber-300 rounded-2xl p-3 sm:p-4 text-center my-3 space-y-2.5">
                <span class="text-[9px] font-extrabold uppercase tracking-widest bg-amber-500 text-white px-2 py-0.5 rounded-full">
                    KODE UNIK PENGAMBILAN PO
                </span>
                
                <h4 class="text-xl sm:text-2xl font-black text-slate-900 font-mono tracking-wider" id="poTokenDisplay">PO-XXXXXX</h4>

                <!-- Unique QR code for pickup -->
                <div class="bg-white p-2.5 rounded-2xl border border-slate-200 inline-block shadow-xs">
                    <div id="poUniqueQrTarget" class="flex items-center justify-center min-w-[150px] min-h-[150px]"></div>
                </div>

                <p class="text-[10px] text-amber-900 font-medium">
                    Tunjukkan Kode QR ini saat mengambil barang di stand.
                </p>

                <!-- WhatsApp Share Button -->
                <div class="pt-1">
                    <a id="btnSendWa" href="#" target="_blank" class="w-full py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-md shadow-emerald-600/20">
                        <i data-lucide="message-circle" class="w-4 h-4"></i>
                        <span>Kirim Tiket ke WhatsApp Pembeli</span>
                    </a>
                </div>
            </div>

            <!-- Receipt Summary Card -->
            <div class="bg-slate-50 rounded-2xl p-3.5 text-left border border-slate-200 text-xs space-y-1.5">
                <div class="flex items-center justify-between font-mono text-[10px] text-slate-500 border-b border-slate-200 pb-1.5">
                    <span>Invoice: <strong id="successInvoice" class="text-slate-800">DNS-000</strong></span>
                    <span id="successDate">Hari ini</span>
                </div>

                <div class="space-y-1 text-slate-700">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Pembeli:</span>
                        <strong class="text-slate-900" id="successCustomerName">-</strong>
                    </div>
                    <div class="flex justify-between" id="successPhoneRow">
                        <span class="text-slate-500">WhatsApp:</span>
                        <strong class="text-slate-900" id="successCustomerPhone">-</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Kasir:</span>
                        <strong class="text-slate-900">{{ Auth::user()->name }}</strong>
                    </div>
                </div>

                <div class="border-t border-slate-200 pt-2 space-y-1" id="successItemsList">
                    <!-- Items breakdown -->
                </div>

                <div class="border-t border-slate-200 pt-2 flex justify-between font-bold text-sm text-slate-900">
                    <span>Total (Lunas QRIS):</span>
                    <span class="text-orange-600 font-black" id="successTotalAmount">Rp 0</span>
                </div>
            </div>

            <!-- Finished Button -->
            <div class="pt-2">
                <button type="button" onclick="finishAndResetOrder()"
                    class="w-full py-3 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs sm:text-sm transition">
                    Selesai & Transaksi Baru
                </button>
            </div>

        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    // State
    let currentOrderType = 'di_tempat';
    let cart = [];
    let capturedPhotoBase64 = null;
    let cameraStream = null;
    let currentFacingMode = 'environment';
    let currentDynamicPayload = '';

    // Switch order mode
    function switchOrderType(type) {
        currentOrderType = type;
        const tabDiTempat = document.getElementById('tabDiTempat');
        const tabPO = document.getElementById('tabPO');
        const poExtraFields = document.getElementById('poExtraFields');
        const badgeOrderMode = document.getElementById('badgeOrderMode');

        if (type === 'di_tempat') {
            tabDiTempat.className = 'flex-1 py-2.5 sm:py-3 px-3 sm:px-4 rounded-xl font-extrabold text-xs sm:text-sm transition flex items-center justify-center gap-1.5 sm:gap-2.5 bg-orange-500 text-white shadow-md shadow-orange-500/20';
            tabPO.className = 'flex-1 py-2.5 sm:py-3 px-3 sm:px-4 rounded-xl font-bold text-xs sm:text-sm transition flex items-center justify-center gap-1.5 sm:gap-2.5 text-slate-600 hover:bg-slate-100';
            poExtraFields.classList.add('hidden');
            badgeOrderMode.textContent = 'Beli di Tempat';
            badgeOrderMode.className = 'text-[10px] font-bold uppercase tracking-wider text-orange-600 bg-orange-50 px-2 py-0.5 rounded-md';
        } else {
            tabPO.className = 'flex-1 py-2.5 sm:py-3 px-3 sm:px-4 rounded-xl font-extrabold text-xs sm:text-sm transition flex items-center justify-center gap-1.5 sm:gap-2.5 bg-indigo-600 text-white shadow-md shadow-indigo-600/20';
            tabDiTempat.className = 'flex-1 py-2.5 sm:py-3 px-3 sm:px-4 rounded-xl font-bold text-xs sm:text-sm transition flex items-center justify-center gap-1.5 sm:gap-2.5 text-slate-600 hover:bg-slate-100';
            poExtraFields.classList.remove('hidden');
            badgeOrderMode.textContent = 'Pre-Order (PO)';
            badgeOrderMode.className = 'text-[10px] font-bold uppercase tracking-wider text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md';
        }
        // Reset category filter to "Semua" when switching mode
        document.querySelectorAll('.category-pill').forEach((btn, i) => {
            if (i === 0) {
                btn.className = 'category-pill active-pill px-3 py-1.5 rounded-xl text-xs font-bold transition bg-orange-500 text-white whitespace-nowrap';
            } else {
                btn.className = 'category-pill px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition whitespace-nowrap';
            }
        });
        lucide.createIcons();
        filterMenuItems();
    }

    // Scroll to order form on mobile
    function scrollToOrderForm() {
        const target = document.getElementById('orderFormSection');
        target.scrollIntoView({ behavior: 'smooth' });
        document.getElementById('customerName').focus();
    }

    // Add to cart
    function addToCart(id, name, price) {
        const existing = cart.find(item => item.id === id);
        if (existing) {
            existing.qty += 1;
        } else {
            cart.push({ id, name, price, qty: 1 });
        }
        renderCart();

        // Subtle mobile vibration if supported
        if (navigator.vibrate) navigator.vibrate(35);
    }

    function updateQty(id, delta) {
        const item = cart.find(it => it.id === id);
        if (!item) return;

        item.qty += delta;
        if (item.qty <= 0) {
            cart = cart.filter(it => it.id !== id);
        }
        renderCart();
    }

    function clearCart() {
        cart = [];
        renderCart();
    }

    function calculateTotal() {
        return cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
    }

    function renderCart() {
        const container = document.getElementById('cartItemsContainer');
        const countBadge = document.getElementById('cartCountBadge');
        const totalDisplay = document.getElementById('cartTotalDisplay');
        const mobileBarCount = document.getElementById('mobileBarItemCount');
        const mobileBarTotal = document.getElementById('mobileBarTotal');

        const total = calculateTotal();
        const totalItems = cart.reduce((sum, item) => sum + item.qty, 0);

        countBadge.textContent = `${totalItems} Item`;
        totalDisplay.textContent = 'Rp ' + total.toLocaleString('id-ID');
        mobileBarCount.textContent = `${totalItems} Menu Dipilih`;
        mobileBarTotal.textContent = 'Rp ' + total.toLocaleString('id-ID');

        if (cart.length === 0) {
            container.innerHTML = `
                <div id="cartEmptyState" class="py-6 text-center text-slate-400 border border-dashed border-slate-200 rounded-2xl">
                    <i data-lucide="shopping-cart" class="w-7 h-7 mx-auto text-slate-300 mb-1"></i>
                    <p class="text-xs font-medium">Keranjang masih kosong</p>
                    <p class="text-[10px]">Klik tombol (+) pada menu produk</p>
                </div>
            `;
            lucide.createIcons();
            return;
        }

        container.innerHTML = cart.map(item => `
            <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50 border border-slate-200">
                <div class="min-w-0 flex-1 pr-2">
                    <p class="text-xs font-bold text-slate-900 truncate">${item.name}</p>
                    <p class="text-[10px] text-orange-600 font-semibold">@ Rp ${item.price.toLocaleString('id-ID')}</p>
                </div>
                <div class="flex items-center gap-2">
                    <div class="flex items-center border border-slate-300 rounded-lg bg-white">
                        <button type="button" onclick="updateQty(${item.id}, -1)" class="w-6 h-6 flex items-center justify-center text-slate-600 hover:bg-slate-100 rounded-l-lg font-bold text-xs">-</button>
                        <span class="w-6 text-center text-xs font-bold text-slate-800">${item.qty}</span>
                        <button type="button" onclick="updateQty(${item.id}, 1)" class="w-6 h-6 flex items-center justify-center text-slate-600 hover:bg-slate-100 rounded-r-lg font-bold text-xs">+</button>
                    </div>
                    <span class="text-xs font-black text-slate-900 w-14 text-right">Rp ${(item.price * item.qty).toLocaleString('id-ID')}</span>
                </div>
            </div>
        `).join('');
    }

    function filterMenuItems() {
        const query = document.getElementById('menuSearch').value.toLowerCase().trim();
        const cards = document.querySelectorAll('.product-card');
        const activePill = document.querySelector('.category-pill.active-pill');
        // Get the raw text, strip the count in parentheses e.g. "Semua (8)" → "SEMUA"
        const activeCatRaw = activePill ? activePill.innerText.trim().replace(/\s*\(\d+\)/, '') : 'Semua';
        const activeCategory = activeCatRaw.toUpperCase();

        cards.forEach(card => {
            const name = card.getAttribute('data-name');
            const cat = card.getAttribute('data-category').toLowerCase();
            const status = card.getAttribute('data-status');
            
            // Mode filter: di_tempat shows 'ready', po shows 'po'
            const isMatchMode = (currentOrderType === 'di_tempat' && status === 'ready') || (currentOrderType === 'po' && status === 'po');
            const isMatchQuery = name.includes(query) || cat.includes(query);
            const isMatchCategory = activeCategory === 'SEMUA' || card.getAttribute('data-category').toUpperCase() === activeCategory;

            if (isMatchMode && isMatchQuery && isMatchCategory) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });
    }

    function filterCategory(cat) {
        document.querySelectorAll('.category-pill').forEach(btn => {
            btn.className = 'category-pill px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition whitespace-nowrap';
        });
        event.currentTarget.className = 'category-pill active-pill px-3 py-1.5 rounded-xl text-xs font-bold transition bg-orange-500 text-white whitespace-nowrap';

        filterMenuItems(); // Re-run filter logic
    }

    // ================= DYNAMIC QRIS MODAL =================
    async function openPaymentModal() {
        const customerName = document.getElementById('customerName').value.trim();
        if (!customerName) {
            Swal.fire({
                icon: 'warning',
                title: 'Nama Pembeli Kosong',
                text: 'Silakan isi nama pembeli terlebih dahulu.',
                confirmButtonColor: '#ea580c',
            });
            scrollToOrderForm();
            return;
        }

        if (currentOrderType === 'po') {
            const phone = document.getElementById('customerPhone').value.trim();
            if (!phone) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No WhatsApp Kosong',
                    text: 'Untuk pesanan Pre-Order, mohon isi nomor WhatsApp pembeli.',
                    confirmButtonColor: '#ea580c',
                });
                scrollToOrderForm();
                return;
            }
        }

        if (cart.length === 0) {
            Swal.fire({
                icon: 'info',
                title: 'Keranjang Kosong',
                text: 'Pilih minimal satu menu untuk checkout.',
                confirmButtonColor: '#ea580c',
            });
            return;
        }

        const total = calculateTotal();
        document.getElementById('modalTotalNominal').textContent = 'Rp ' + total.toLocaleString('id-ID');

        try {
            const res = await fetch(`{{ route('kasir.qris.dynamic') }}?amount=${total}`);
            const data = await res.json();
            
            if (data.success) {
                currentDynamicPayload = data.dynamic_payload;

                const targetEl = document.getElementById('qrisCodeTarget');
                targetEl.innerHTML = '';
                new QRCode(targetEl, {
                    text: data.dynamic_payload,
                    width: 190,
                    height: 190,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.M
                });

                document.getElementById('modalQris').classList.remove('hidden');
            }
        } catch (err) {
            console.error('Error generating dynamic QRIS:', err);
            Swal.fire({
                icon: 'error',
                title: 'Gagal Membuat QRIS',
                text: 'Terjadi kendala saat memuat QRIS dinamis.',
            });
        }
    }

    function closeQrisModal() {
        document.getElementById('modalQris').classList.add('hidden');
    }

    // ================= CAMERA CAPTURE (100% RELIABLE FOR MOBILE & PC) =================
    function openCameraModal() {
        closeQrisModal();
        resetCameraModal();
        document.getElementById('modalCamera').classList.remove('hidden');
        lucide.createIcons();
    }

    function closeCameraModal() {
        stopWebcamStream();
        document.getElementById('modalCamera').classList.add('hidden');
    }

    function resetCameraModal() {
        stopWebcamStream();
        document.getElementById('cameraVideo').classList.add('hidden');
        document.getElementById('cameraCanvas').classList.add('hidden');
        document.getElementById('imagePreviewFallback').classList.add('hidden');
        document.getElementById('cameraIdleView').classList.remove('hidden');
        document.getElementById('cameraFrameOverlay').classList.add('hidden');
        document.getElementById('cameraInitialControls').classList.remove('hidden');
        document.getElementById('cameraLiveControls').classList.add('hidden');
        document.getElementById('cameraPreviewControls').classList.add('hidden');
        capturedPhotoBase64 = null;
    }

    // 1. Native Mobile Camera Trigger (Works on HTTP/HTTPS on Android & iPhone)
    function triggerNativeCamera() {
        document.getElementById('nativeCameraInput').click();
    }

    // 2. Gallery Upload Trigger
    function triggerGalleryUpload() {
        document.getElementById('galleryFileInput').click();
    }

    // Handle photo returned from native camera or gallery
    function handleFileCapture(event) {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            applyCapturedPhoto(e.target.result);
        };
        reader.readAsDataURL(file);

        // Reset input value so same file can be re-selected if retrying
        event.target.value = '';
    }

    // 3. Live Webcam Stream (Laptop / HTTPS)
    async function startWebcamLiveStream() {
        // Safe check for navigator.mediaDevices
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            Swal.fire({
                icon: 'info',
                title: 'Gunakan Kamera HP Langsung',
                text: 'Fitur Live Stream video hanya aktif di localhost atau HTTPS. Silakan gunakan tombol "Buka Kamera HP Langsung" di atas yang berfungsi lancar!',
                confirmButtonColor: '#ea580c',
            });
            return;
        }

        const video = document.getElementById('cameraVideo');
        stopWebcamStream();

        try {
            cameraStream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: currentFacingMode,
                    width: { ideal: 1280 },
                    height: { ideal: 960 }
                },
                audio: false
            });

            video.srcObject = cameraStream;
            video.classList.remove('hidden');
            document.getElementById('cameraIdleView').classList.add('hidden');
            document.getElementById('cameraInitialControls').classList.add('hidden');
            document.getElementById('cameraLiveControls').classList.remove('hidden');
        } catch (err) {
            console.warn('Webcam stream error:', err);
            Swal.fire({
                icon: 'warning',
                title: 'Izin Kamera Dibutuhkan',
                text: 'Kamera webcam tidak dapat diakses atau izin ditolak. Silakan gunakan opsi "Kamera HP Langsung" atau "Simulasi".',
                confirmButtonColor: '#ea580c',
            });
        }
    }

    function stopWebcamStream() {
        if (cameraStream) {
            cameraStream.getTracks().forEach(track => track.stop());
            cameraStream = null;
        }
    }

    function switchCameraFacing() {
        currentFacingMode = (currentFacingMode === 'environment') ? 'user' : 'environment';
        startWebcamLiveStream();
    }

    function takeWebcamSnapshot() {
        const video = document.getElementById('cameraVideo');
        const canvas = document.getElementById('cameraCanvas');

        if (cameraStream && video.videoWidth) {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            const dataUrl = canvas.toDataURL('image/jpeg', 0.85);
            applyCapturedPhoto(dataUrl);
            stopWebcamStream();
        }
    }

    // 4. Simulated Quick Proof Generator (For Fast Testing)
    function generateSimulatedProof() {
        const canvas = document.getElementById('cameraCanvas');
        canvas.width = 640;
        canvas.height = 480;
        const ctx = canvas.getContext('2d');

        // Background
        ctx.fillStyle = '#0f172a';
        ctx.fillRect(0, 0, 640, 480);

        // Header
        ctx.fillStyle = '#ea580c';
        ctx.fillRect(0, 0, 640, 60);
        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 20px sans-serif';
        ctx.fillText('KEDAIPHORIA, KBN JRK - BUKTI TRANSAKSI', 30, 38);

        // Card
        ctx.fillStyle = '#1e293b';
        ctx.roundRect ? ctx.roundRect(40, 90, 560, 350, 16) : ctx.fillRect(40, 90, 560, 350);
        ctx.fill();

        ctx.fillStyle = '#10b981';
        ctx.font = 'bold 26px sans-serif';
        ctx.fillText('STATUS: LUNAS (QRIS)', 70, 150);

        ctx.fillStyle = '#f8fafc';
        ctx.font = 'bold 18px monospace';
        ctx.fillText('Total Nominal : Rp ' + calculateTotal().toLocaleString('id-ID'), 70, 200);
        ctx.fillText('Nama Pembeli  : ' + document.getElementById('customerName').value.trim(), 70, 240);
        ctx.fillText('Tipe Order    : ' + (currentOrderType === 'po' ? 'PRE-ORDER (PO)' : 'BELI DI TEMPAT'), 70, 280);
        ctx.fillText('Waktu / Kasir : ' + new Date().toLocaleTimeString('id-ID') + ' / {{ Auth::user()->name }}', 70, 320);

        ctx.fillStyle = '#64748b';
        ctx.font = '13px sans-serif';
        ctx.fillText('Foto bukti dibuat via Sistem Danusan POS', 70, 380);

        const dataUrl = canvas.toDataURL('image/jpeg', 0.85);
        applyCapturedPhoto(dataUrl);
    }

    function applyCapturedPhoto(dataUrl) {
        capturedPhotoBase64 = dataUrl;

        const previewImg = document.getElementById('imagePreviewFallback');
        previewImg.src = dataUrl;
        previewImg.classList.remove('hidden');

        document.getElementById('cameraVideo').classList.add('hidden');
        document.getElementById('cameraIdleView').classList.add('hidden');
        document.getElementById('cameraCanvas').classList.add('hidden');
        document.getElementById('cameraInitialControls').classList.add('hidden');
        document.getElementById('cameraLiveControls').classList.add('hidden');
        document.getElementById('cameraPreviewControls').classList.remove('hidden');

        document.getElementById('overlayAmountStamp').textContent = 'Rp ' + calculateTotal().toLocaleString('id-ID');
        document.getElementById('cameraFrameOverlay').classList.remove('hidden');
    }

    function retakePhoto() {
        resetCameraModal();
    }

    // ================= SUBMIT TRANSACTION TO BACKEND =================
    async function submitCompleteTransaction() {
        const btn = document.getElementById('btnSubmitOrder');
        btn.disabled = true;
        btn.innerHTML = `<span class="inline-block animate-spin mr-1.5">&#9696;</span> Menyimpan...`;

        const payload = {
            type: currentOrderType,
            customer_name: document.getElementById('customerName').value.trim(),
            customer_phone: document.getElementById('customerPhone').value.trim(),
            notes: document.getElementById('customerNotes').value.trim(),
            items: cart.map(item => ({ product_id: item.id, quantity: item.qty })),
            payment_proof: capturedPhotoBase64
        };

        try {
            const res = await fetch(`{{ route('kasir.transaction.store') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const data = await res.json();

            if (data.success) {
                closeCameraModal();
                showSuccessModal(data.transaction);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan Transaksi',
                    text: data.message || 'Periksa kembali data Anda.',
                    confirmButtonColor: '#ea580c',
                });
                btn.disabled = false;
                btn.innerHTML = `<i data-lucide="check" class="w-4 h-4"></i><span>Simpan & Selesai</span>`;
                lucide.createIcons();
            }
        } catch (err) {
            console.error('Error submitting transaction:', err);
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: 'Koneksi terputus saat menyimpan ke database.',
            });
            btn.disabled = false;
            btn.innerHTML = `<i data-lucide="check" class="w-4 h-4"></i><span>Simpan & Selesai</span>`;
            lucide.createIcons();
        }
    }

    // ================= SUCCESS MODAL (STRUK & UNIQUE PO QR TICKET) =================
    function showSuccessModal(tx) {
        document.getElementById('successInvoice').textContent = tx.invoice_number;
        document.getElementById('successDate').textContent = tx.created_at;
        document.getElementById('successCustomerName').textContent = tx.customer_name;
        document.getElementById('successTotalAmount').textContent = tx.total_formatted;

        const phoneRow = document.getElementById('successPhoneRow');
        const poSection = document.getElementById('poTicketSuccessSection');

        if (tx.type === 'po') {
            phoneRow.classList.remove('hidden');
            document.getElementById('successCustomerPhone').textContent = tx.customer_phone;
            poSection.classList.remove('hidden');
            document.getElementById('poTokenDisplay').textContent = tx.po_token;

            // Generate Unique PO Claim QR Code (encode full ticket URL so buyers can scan to open ticket)
            const qrTarget = document.getElementById('poUniqueQrTarget');
            qrTarget.innerHTML = '';
            // Use ticket_url if available, otherwise fallback to po_token
            const qrText = tx.ticket_url || tx.po_token;
            new QRCode(qrTarget, {
                text: qrText,
                width: 150,
                height: 150,
                colorDark: "#1e1b4b",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });

            if (tx.wa_link) {
                document.getElementById('btnSendWa').href = tx.wa_link;
            }
        } else {
            phoneRow.classList.add('hidden');
            poSection.classList.add('hidden');
        }

        const itemsList = document.getElementById('successItemsList');
        itemsList.innerHTML = tx.items.map(item => `
            <div class="flex justify-between text-slate-600">
                <span>${item.name} (${item.quantity}x)</span>
                <span>Rp ${(item.price * item.quantity).toLocaleString('id-ID')}</span>
            </div>
        `).join('');

        document.getElementById('modalSuccess').classList.remove('hidden');
        lucide.createIcons();
    }

    function finishAndResetOrder() {
        document.getElementById('modalSuccess').classList.add('hidden');
        clearCart();
        document.getElementById('customerName').value = '';
        document.getElementById('customerPhone').value = '';
        document.getElementById('customerNotes').value = '';
        capturedPhotoBase64 = null;
        window.location.reload();
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        filterMenuItems();
    });
</script>
@endpush
