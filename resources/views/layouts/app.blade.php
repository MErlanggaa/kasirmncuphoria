<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Danusan') - Sistem Penjualan Danus</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (via Vite & CDN fallback for rich utilities) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            200: '#fed7aa',
                            300: '#fdba74',
                            400: '#fb923c',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                            800: '#9a3412',
                            900: '#7c2d12',
                        },
                        danus: {
                            dark: '#0f172a',
                            card: '#1e293b',
                            accent: '#6366f1',
                            teal: '#0d9488',
                            emerald: '#10b981',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- QRCode & HTML5-QRCode Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        .pulse-subtle {
            animation: pulseSubtle 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulseSubtle {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.85; transform: scale(1.02); }
        }
    </style>

    @stack('styles')
</head>
<body class="min-h-screen flex flex-col bg-slate-50 antialiased selection:bg-orange-500 selection:text-white">

    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-slate-200 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <!-- Logo & Brand -->
                <div class="flex items-center gap-3">
                    <a href="{{ Auth::check() && Auth::user()->isAdmin() ? route('admin.dashboard') : route('kasir.pos') }}" class="flex items-center gap-2.5 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-orange-600 to-amber-500 flex items-center justify-center text-white shadow-md shadow-orange-500/20 group-hover:scale-105 transition-transform">
                            <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <span class="font-extrabold text-lg tracking-tight text-slate-900 flex items-center gap-1.5">
                                Danusan<span class="text-orange-600">POS</span>
                            </span>
                            <p class="text-[10px] text-slate-500 font-medium -mt-1 hidden sm:block">Sistem Kasir & Pre-Order</p>
                        </div>
                    </a>

                    @auth
                    <!-- Role Pill -->
                    <span class="ml-2 px-2.5 py-0.5 rounded-full text-xs font-semibold tracking-wide {{ Auth::user()->isAdmin() ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-orange-50 text-orange-700 border border-orange-200' }}">
                        {{ Auth::user()->isAdmin() ? '⚡ Admin' : '💳 Kasir' }}
                    </span>
                    @endauth
                </div>

                <!-- Navigation Links for Admin / Kasir -->
                @auth
                <nav class="hidden md:flex items-center gap-1">
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-orange-50 text-orange-600' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            <span class="flex items-center gap-1.5"><i data-lucide="layout-dashboard" class="w-4 h-4"></i> Dashboard</span>
                        </a>
                        <a href="{{ route('admin.products') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.products') ? 'bg-orange-50 text-orange-600' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            <span class="flex items-center gap-1.5"><i data-lucide="utensils" class="w-4 h-4"></i> Menu / Produk</span>
                        </a>
                        <a href="{{ route('admin.cashiers') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.cashiers') ? 'bg-orange-50 text-orange-600' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            <span class="flex items-center gap-1.5"><i data-lucide="users" class="w-4 h-4"></i> Akun Kasir</span>
                        </a>
                        <a href="{{ route('admin.transactions') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.transactions') ? 'bg-orange-50 text-orange-600' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            <span class="flex items-center gap-1.5"><i data-lucide="receipt" class="w-4 h-4"></i> Transaksi</span>
                        </a>
                        <a href="{{ route('admin.pickup') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.pickup') ? 'bg-orange-50 text-orange-600' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            <span class="flex items-center gap-1.5"><i data-lucide="scan-line" class="w-4 h-4"></i> Scan PO</span>
                        </a>
                        <a href="{{ route('admin.settings') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.settings') ? 'bg-orange-50 text-orange-600' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            <span class="flex items-center gap-1.5"><i data-lucide="settings-2" class="w-4 h-4"></i> Pengaturan</span>
                        </a>
                    @else
                        <a href="{{ route('kasir.pos') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('kasir.pos') ? 'bg-orange-50 text-orange-600' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            <span class="flex items-center gap-1.5"><i data-lucide="calculator" class="w-4 h-4"></i> Kasir POS</span>
                        </a>
                        <a href="{{ route('kasir.pickup') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('kasir.pickup') ? 'bg-orange-50 text-orange-600' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            <span class="flex items-center gap-1.5"><i data-lucide="scan-line" class="w-4 h-4"></i> Ambil Barang PO (Scan QR)</span>
                        </a>
                    @endif
                </nav>

                <!-- User Profile & Logout -->
                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-bold text-slate-800 leading-tight">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-slate-500 font-mono">{{ '@' . (Auth::user()->username ?? 'user') }}</p>
                    </div>

                    <div class="w-9 h-9 rounded-full bg-slate-100 border border-slate-300 flex items-center justify-center text-slate-700 font-bold text-sm shadow-xs">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>

                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" title="Keluar" class="p-2 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition" onclick="return confirm('Apakah Anda yakin ingin keluar?')">
                            <i data-lucide="log-out" class="w-5 h-5"></i>
                        </button>
                    </form>
                </div>
                @endauth

            </div>
        </div>

        <!-- Mobile sub-navigation for quick access -->
        @auth
        <div class="md:hidden border-t border-slate-200 bg-slate-50 px-4 py-2 flex items-center justify-around overflow-x-auto gap-2">
            @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="text-xs font-semibold px-2 py-1.5 rounded {{ request()->routeIs('admin.dashboard') ? 'bg-orange-500 text-white' : 'text-slate-600' }}">Dashboard</a>
                <a href="{{ route('admin.products') }}" class="text-xs font-semibold px-2 py-1.5 rounded {{ request()->routeIs('admin.products') ? 'bg-orange-500 text-white' : 'text-slate-600' }}">Menu</a>
                <a href="{{ route('admin.cashiers') }}" class="text-xs font-semibold px-2 py-1.5 rounded {{ request()->routeIs('admin.cashiers') ? 'bg-orange-500 text-white' : 'text-slate-600' }}">Kasir</a>
                <a href="{{ route('admin.transactions') }}" class="text-xs font-semibold px-2 py-1.5 rounded {{ request()->routeIs('admin.transactions') ? 'bg-orange-500 text-white' : 'text-slate-600' }}">Transaksi</a>
                <a href="{{ route('admin.pickup') }}" class="text-xs font-semibold px-2 py-1.5 rounded {{ request()->routeIs('admin.pickup') ? 'bg-orange-500 text-white' : 'text-slate-600' }}">Scan PO</a>
                <a href="{{ route('admin.settings') }}" class="text-xs font-semibold px-2 py-1.5 rounded {{ request()->routeIs('admin.settings') ? 'bg-orange-500 text-white' : 'text-slate-600' }}">Setting</a>
            @else
                <a href="{{ route('kasir.pos') }}" class="text-xs font-semibold px-3 py-1.5 rounded-lg {{ request()->routeIs('kasir.pos') ? 'bg-orange-500 text-white shadow-xs' : 'text-slate-600' }}">
                    <span class="flex items-center gap-1"><i data-lucide="calculator" class="w-3.5 h-3.5"></i> Kasir POS</span>
                </a>
                <a href="{{ route('kasir.pickup') }}" class="text-xs font-semibold px-3 py-1.5 rounded-lg {{ request()->routeIs('kasir.pickup') ? 'bg-orange-500 text-white shadow-xs' : 'text-slate-600' }}">
                    <span class="flex items-center gap-1"><i data-lucide="scan-line" class="w-3.5 h-3.5"></i> Scan PO</span>
                </a>
            @endif
        </div>
        @endauth
    </header>

    <!-- Main Content -->
    <main class="flex-1">
        <!-- Flash Alert Messages -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            @if(session('success'))
                <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2">
                        <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600"></i>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2">
                        <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600"></i>
                        <span class="text-sm font-medium">{{ session('error') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">&times;</button>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl shadow-xs">
                    <div class="flex items-center gap-2 font-semibold text-sm mb-1">
                        <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-600"></i>
                        <span>Mohon periksa data yang dimasukkan:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-0.5 ml-4">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-4 mt-12 text-center text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
            <div>
                <span class="font-semibold text-slate-700">Danusan POS</span> &bull; Solusi Penjualan Dana Usaha Mahasiswa & Organisasi
            </div>
            <div class="flex items-center gap-2 text-slate-400">
                <span>QRIS Dinamis Otomatis</span>
                <span>&bull;</span>
                <span>By Erlangga</span>
                <!-- <span>&bull;</span>
                <span>Klaim QR Pre-Order</span> -->
            </div>
        </div>
    </footer>

    <!-- Initialize Lucide Icons -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>

    @stack('scripts')
</body>
</html>
