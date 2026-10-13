@extends('layouts.app')

@section('title', 'Masuk ke Sistem Danusan')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        
        <!-- Header -->
        <div class="text-center">
            <div class="mx-auto w-16 h-16 rounded-2xl bg-gradient-to-tr from-orange-600 to-amber-500 flex items-center justify-center text-white shadow-xl shadow-orange-500/25 mb-4 transform hover:rotate-3 transition duration-300">
                <i data-lucide="store" class="w-8 h-8"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                Selamat Datang di <span class="text-orange-600">Danusan</span>
            </h2>
            <p class="mt-2 text-sm text-slate-500">
                Silakan masuk dengan akun <span class="font-semibold text-slate-700">Admin</span> atau <span class="font-semibold text-slate-700">Kasir</span> Anda
            </p>
        </div>

        <!-- Card Container -->
        <div class="bg-white p-8 rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100">
            
            <form action="{{ route('login.post') }}" method="POST" class="space-y-5" id="loginForm">
                @csrf

                <!-- Username / Email Field -->
                <div>
                    <label for="login" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Username / Email
                    </label>
                    <div class="relative rounded-xl shadow-xs">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="user" class="w-5 h-5"></i>
                        </div>
                        <input type="text" name="login" id="loginInput" required value="{{ old('login') }}"
                            placeholder="admin atau username kasir"
                            class="block w-full pl-11 pr-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm text-slate-900 placeholder-slate-400 transition">
                    </div>
                </div>

                <!-- Password Field -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Kata Sandi (Password)
                        </label>
                    </div>
                    <div class="relative rounded-xl shadow-xs">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="lock" class="w-5 h-5"></i>
                        </div>
                        <input type="password" name="password" id="passwordInput" required
                            placeholder="••••••••"
                            class="block w-full pl-11 pr-11 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm text-slate-900 placeholder-slate-400 transition">
                        <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                            <i data-lucide="eye" id="eyeIcon" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full py-3.5 px-4 rounded-xl text-white font-bold bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-700 hover:to-amber-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 shadow-lg shadow-orange-500/25 transition transform active:scale-[0.99] flex items-center justify-center gap-2">
                    <span>Masuk ke Aplikasi</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </form>

            <!-- Quick Demo Accounts Switcher -->
            <!-- <div class="mt-8 pt-6 border-t border-slate-100">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Pilih Akun Demo (1-Klik)</p>
                    <span class="text-[10px] bg-orange-100 text-orange-700 font-semibold px-2 py-0.5 rounded-full">Praktis</span>
                </div>

                 <div class="grid grid-cols-2 gap-2">
                    Admin Button
                    <button type="button" onclick="fillCredentials('admin', 'admin123')"
                        class="p-2.5 rounded-xl border border-indigo-100 bg-indigo-50/60 hover:bg-indigo-100 text-left transition group">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-md bg-indigo-600 text-white flex items-center justify-center text-xs font-bold">A</div>
                            <div>
                                <p class="text-xs font-bold text-indigo-900 leading-tight">Admin Utama</p>
                                <p class="text-[10px] text-indigo-600 font-mono">admin / admin123</p>
                            </div>
                        </div>
                    </button>

                    Kasir 1: Budi
                    <button type="button" onclick="fillCredentials('kasir_budi', 'kasir123')"
                        class="p-2.5 rounded-xl border border-orange-100 bg-orange-50/60 hover:bg-orange-100 text-left transition group">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-md bg-orange-500 text-white flex items-center justify-center text-xs font-bold">B</div>
                            <div>
                                <p class="text-xs font-bold text-orange-900 leading-tight">Kasir Budi</p>
                                <p class="text-[10px] text-orange-600 font-mono">kasir_budi</p>
                            </div>
                        </div>
                    </button>

                    Kasir 2: Siti
                    <button type="button" onclick="fillCredentials('kasir_siti', 'kasir123')"
                        class="p-2.5 rounded-xl border border-orange-100 bg-orange-50/60 hover:bg-orange-100 text-left transition group">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-md bg-orange-500 text-white flex items-center justify-center text-xs font-bold">S</div>
                            <div>
                                <p class="text-xs font-bold text-orange-900 leading-tight">Kasir Siti</p>
                                <p class="text-[10px] text-orange-600 font-mono">kasir_siti</p>
                            </div>
                        </div>
                    </button>

                    Kasir 3: Dimas
                    <button type="button" onclick="fillCredentials('kasir_dimas', 'kasir123')"
                        class="p-2.5 rounded-xl border border-orange-100 bg-orange-50/60 hover:bg-orange-100 text-left transition group">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-md bg-orange-500 text-white flex items-center justify-center text-xs font-bold">D</div>
                            <div>
                                <p class="text-xs font-bold text-orange-900 leading-tight">Kasir Dimas</p>
                                <p class="text-[10px] text-orange-600 font-mono">kasir_dimas</p>
                            </div>
                        </div>
                    </button>
                </div> 
            </div> -->

        </div>

        <div class="text-center text-xs text-slate-400">
            Danusan POS &bull; Dilengkapi QRIS Dinamis & Pengambilan PO Berbasis QR Unik
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function fillCredentials(username, password) {
        document.getElementById('loginInput').value = username;
        document.getElementById('passwordInput').value = password;
        
        // Highlight inputs briefly
        document.getElementById('loginInput').classList.add('ring-2', 'ring-orange-500');
        document.getElementById('passwordInput').classList.add('ring-2', 'ring-orange-500');
        setTimeout(() => {
            document.getElementById('loginInput').classList.remove('ring-2', 'ring-orange-500');
            document.getElementById('passwordInput').classList.remove('ring-2', 'ring-orange-500');
        }, 600);
    }

    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('passwordInput');
        const eyeIcon = document.getElementById('eyeIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.setAttribute('data-lucide', 'eye-off');
        } else {
            passwordInput.type = 'password';
            eyeIcon.setAttribute('data-lucide', 'eye');
        }
        lucide.createIcons();
    }
</script>
@endpush
