@extends('layouts.app')

@section('title', 'Kelola Akun Kasir - Danusan')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    
    <!-- Header & Add Button -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-orange-600 uppercase tracking-wider mb-1">
                <i data-lucide="users" class="w-4 h-4"></i>
                <span>Manajemen Tim & Kasir</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Akun Kasir Danusan</h1>
            <p class="text-xs sm:text-sm text-slate-500">Kelola akun dan nama-nama kasir yang bertugas di lapangan.</p>
        </div>

        <button type="button" onclick="openAddCashierModal()"
            class="px-4 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs transition shadow-md shadow-orange-600/20 flex items-center gap-2 self-start sm:self-auto">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            <span>Tambah Akun Kasir</span>
        </button>
    </div>

    <!-- Cashiers Cards / Table Grid -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4">Nama Kasir</th>
                        <th class="py-3.5 px-4">Username & Email</th>
                        <th class="py-3.5 px-4">Kontak (No HP)</th>
                        <th class="py-3.5 px-4">Transaksi Ditangani</th>
                        <th class="py-3.5 px-4">Total Omset</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($cashiers as $kasir)
                    <tr class="hover:bg-slate-50/80 transition">
                        
                        <!-- Name & Avatar -->
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-700 font-extrabold flex items-center justify-center text-sm border border-orange-200">
                                    {{ strtoupper(substr($kasir->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900 text-sm">{{ $kasir->name }}</h4>
                                    <span class="text-[10px] text-slate-400">Kasir Danus</span>
                                </div>
                            </div>
                        </td>

                        <!-- Username & Email -->
                        <td class="py-3.5 px-4">
                            <p class="font-mono font-bold text-slate-800 text-xs">{{ '@' . $kasir->username }}</p>
                            <p class="text-[11px] text-slate-400">{{ $kasir->email }}</p>
                        </td>

                        <!-- Phone -->
                        <td class="py-3.5 px-4 font-medium text-slate-700">
                            {{ $kasir->phone ?? '-' }}
                        </td>

                        <!-- Transactions Count -->
                        <td class="py-3.5 px-4 font-bold text-slate-900">
                            {{ $kasir->transactions_count }} transaksi
                        </td>

                        <!-- Total Revenue Handled -->
                        <td class="py-3.5 px-4 font-black text-orange-600 text-sm">
                            Rp {{ number_format($kasir->transactions_sum_total_amount ?? 0, 0, ',', '.') }}
                        </td>

                        <!-- Status -->
                        <td class="py-3.5 px-4">
                            @if($kasir->is_active)
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Aktif
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200">
                                    Dinonaktifkan
                                </span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="py-3.5 px-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <button type="button" onclick="openEditCashierModal({{ json_encode($kasir) }})"
                                    class="p-2 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition" title="Edit Akun">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </button>
                                
                                <form action="{{ route('admin.cashiers.toggle', $kasir) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="p-2 rounded-lg text-slate-400 hover:text-orange-600 hover:bg-orange-50 transition" title="{{ $kasir->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}">
                                        <i data-lucide="{{ $kasir->is_active ? 'user-x' : 'user-check' }}" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400">Belum ada akun kasir terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ================= MODAL: ADD / EDIT CASHIER ================= -->
<div id="cashierModal" class="fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 relative animate-in fade-in zoom-in-95 duration-200">
        
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <h3 class="font-extrabold text-base text-slate-900" id="cashierModalTitle">Tambah Akun Kasir Baru</h3>
            <button type="button" onclick="closeCashierModal()" class="text-slate-400 hover:text-slate-600 p-1">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="cashierForm" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="_method" id="cashierFormMethod" value="POST">

            <!-- Name -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap Kasir <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="cName" required placeholder="Contoh: Budi Santoso"
                    class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
            </div>

            <!-- Username & Phone -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Username <span class="text-rose-500">*</span></label>
                    <input type="text" name="username" id="cUsername" required placeholder="kasir_budi"
                        class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">No WhatsApp / HP</label>
                    <input type="text" name="phone" id="cPhone" placeholder="08123456789"
                        class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                </div>
            </div>

            <!-- Email -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Email <span class="text-rose-500">*</span></label>
                <input type="email" name="email" id="cEmail" required placeholder="budi@danusan.com"
                    class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
            </div>

            <!-- Password -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1" id="cPasswordLabel">Password <span class="text-rose-500">*</span></label>
                <input type="password" name="password" id="cPassword" placeholder="Minimal 6 karakter"
                    class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                <p class="text-[10px] text-slate-400 mt-1" id="cPasswordHelp">Gunakan password yang mudah diingat kasir.</p>
            </div>

            <!-- Active Checkbox -->
            <div id="cActiveRow" class="hidden">
                <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-700">
                    <input type="checkbox" name="is_active" id="cIsActive" value="1" class="w-4 h-4 rounded text-orange-600 focus:ring-orange-500 border-slate-300">
                    <span>Akun Aktif (Bisa Login)</span>
                </label>
            </div>

            <!-- Buttons -->
            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeCashierModal()" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-extrabold text-xs transition shadow-md shadow-orange-600/20">
                    Simpan Kasir
                </button>
            </div>
        </form>

    </div>
</div>
@endsection

@push('scripts')
<script>
    function openAddCashierModal() {
        document.getElementById('cashierModalTitle').textContent = 'Tambah Akun Kasir Baru';
        document.getElementById('cashierForm').action = "{{ route('admin.cashiers.store') }}";
        document.getElementById('cashierFormMethod').value = 'POST';

        document.getElementById('cName').value = '';
        document.getElementById('cUsername').value = '';
        document.getElementById('cPhone').value = '';
        document.getElementById('cEmail').value = '';
        document.getElementById('cPassword').value = '';
        document.getElementById('cPassword').required = true;
        document.getElementById('cPasswordLabel').innerHTML = 'Password <span class="text-rose-500">*</span>';
        document.getElementById('cPasswordHelp').textContent = 'Password awal untuk login kasir.';
        document.getElementById('cActiveRow').classList.add('hidden');

        document.getElementById('cashierModal').classList.remove('hidden');
    }

    function openEditCashierModal(kasir) {
        document.getElementById('cashierModalTitle').textContent = 'Edit Akun: ' + kasir.name;
        document.getElementById('cashierForm').action = "{{ url('/admin/cashiers') }}/" + kasir.id;
        document.getElementById('cashierFormMethod').value = 'PUT';

        document.getElementById('cName').value = kasir.name;
        document.getElementById('cUsername').value = kasir.username;
        document.getElementById('cPhone').value = kasir.phone || '';
        document.getElementById('cEmail').value = kasir.email;
        document.getElementById('cPassword').value = '';
        document.getElementById('cPassword').required = false;
        document.getElementById('cPasswordLabel').textContent = 'Ganti Password (Kosongkan jika tidak diubah)';
        document.getElementById('cPasswordHelp').textContent = 'Kosongkan jika kasir tidak ingin ganti password.';
        
        document.getElementById('cActiveRow').classList.remove('hidden');
        document.getElementById('cIsActive').checked = Boolean(kasir.is_active);

        document.getElementById('cashierModal').classList.remove('hidden');
    }

    function closeCashierModal() {
        document.getElementById('cashierModal').classList.add('hidden');
    }
</script>
@endpush
