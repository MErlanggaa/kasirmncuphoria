@extends('layouts.app')

@section('title', 'Katalog Menu & Kode QR - KEDAIPHORIA')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    
    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Katalog Menu & Kode QR</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola daftar menu, harga satuan, stok, serta kode QR spesifik untuk setiap item.</p>
        </div>

        <button type="button" onclick="openAddProductModal()"
            class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition shadow-sm flex items-center gap-2 self-start sm:self-auto">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Tambah Menu Baru</span>
        </button>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">Menu</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4">Harga Satuan</th>
                        <th class="py-3 px-4">Stok</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($products as $product)
                    <tr class="hover:bg-slate-50/70 transition">
                        
                        <!-- Name & Image -->
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-slate-100 overflow-hidden flex-shrink-0 border border-slate-200">
                                    @if($product->image)
                                        <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-400">
                                            <i data-lucide="utensils" class="w-4 h-4"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900 text-xs">{{ $product->name }}</h4>
                                    <p class="text-[10px] text-slate-400 line-clamp-1">{{ $product->description ?? '-' }}</p>
                                </div>
                            </div>
                        </td>



                        <!-- Category -->
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700">
                                {{ $product->category }}
                            </span>
                        </td>

                        <!-- Price -->
                        <td class="py-3 px-4 font-bold text-slate-900 text-xs">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </td>

                        <!-- Stock -->
                        <td class="py-3 px-4 text-slate-600 font-mono">
                            {{ $product->stock }}
                        </td>

                        <!-- Status -->
                        <td class="py-3 px-4">
                            @if($product->status === 'ready')
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Beli di Tempat
                                </span>
                            @elseif($product->status === 'po')
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    Pre-Order (PO)
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                    Stok Habis
                                </span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="py-3 px-4 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button type="button" onclick="openEditProductModal({{ json_encode($product) }})"
                                    class="p-1.5 rounded-lg text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition" title="Edit">
                                    <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                                </button>
                                
                                <form action="{{ route('admin.products.delete', $product) }}" method="POST" onsubmit="return confirm('Hapus menu {{ addslashes($product->name) }}?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400">Belum ada menu produk. Klik "Tambah Menu Baru".</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
        <div class="p-3 border-t border-slate-100">
            {{ $products->links() }}
        </div>
        @endif
    </div>

</div>

<!-- ================= MODAL: ADD / EDIT PRODUCT ================= -->
<div id="productModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-5 shadow-xl border border-slate-200 relative animate-in fade-in zoom-in-95 duration-150">
        
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
            <h3 class="font-black text-sm text-slate-900" id="productModalTitle">Tambah Menu Baru</h3>
            <button type="button" onclick="closeProductModal()" class="text-slate-400 hover:text-slate-600 p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="productForm" method="POST" enctype="multipart/form-data" class="space-y-3">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <!-- Name -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Menu <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="prodName" required placeholder="Contoh: Ayam Geprek Sambal Bawang"
                    class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition">
            </div>

            <!-- Kategori -->
            <div class="mb-3">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kategori <span class="text-rose-500">*</span></label>
                <select name="category" id="prodCategory" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition">
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Harga & Stok -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Harga Satuan (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" name="price" id="prodPrice" required min="0" step="500" placeholder="15000"
                        class="w-full px-3 py-2 text-xs font-mono rounded-xl border border-slate-300 focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Stok Tersedia</label>
                    <input type="number" name="stock" id="prodStock" min="0" value="50"
                        class="w-full px-3 py-2 text-xs font-mono rounded-xl border border-slate-300 focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition">
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Keterangan / Deskripsi</label>
                <textarea name="description" id="prodDescription" rows="2" placeholder="Porsi, bumbu, atau saus..."
                    class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition"></textarea>
            </div>

            <!-- Image File Upload -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Foto Menu (Opsional)</label>
                <input type="file" name="image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-800 hover:file:bg-slate-200">
            </div>

            <!-- Status -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tipe Penjualan <span class="text-rose-500">*</span></label>
                <select name="status" id="prodStatus" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition">
                    <option value="ready">Beli di Tempat</option>
                    <option value="po">Pre-Order (PO)</option>
                </select>
                <p class="text-[10px] text-slate-400 mt-0.5">Status "Stok Habis" otomatis diset saat stok mencapai 0.</p>
                <input type="hidden" name="is_available" value="1">
            </div>

            <!-- Buttons -->
            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeProductModal()" class="px-3.5 py-2 rounded-xl border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition shadow-xs">
                    Simpan Menu
                </button>
            </div>
        </form>

    </div>
</div>
@endsection

@push('scripts')
<script>
    function openAddProductModal() {
        document.getElementById('productModalTitle').textContent = 'Tambah Menu Baru';
        document.getElementById('productForm').action = "{{ route('admin.products.store') }}";
        document.getElementById('formMethod').value = 'POST';
        
        document.getElementById('prodName').value = '';
        document.getElementById('prodPrice').value = '';
        document.getElementById('prodStock').value = '50';
        document.getElementById('prodDescription').value = '';
        document.getElementById('prodStatus').value = 'ready';
        
        document.getElementById('productModal').classList.remove('hidden');
    }

    function openEditProductModal(product) {
        document.getElementById('productModalTitle').textContent = 'Edit Menu: ' + product.name;
        document.getElementById('productForm').action = "{{ url('/admin/products') }}/" + product.id;
        document.getElementById('formMethod').value = 'PUT';
        
        document.getElementById('prodName').value = product.name;
        document.getElementById('prodCategory').value = product.category;
        document.getElementById('prodPrice').value = product.price;
        document.getElementById('prodStock').value = product.stock;
        document.getElementById('prodDescription').value = product.description || '';
        document.getElementById('prodStatus').value = product.status || 'ready';
        
        document.getElementById('productModal').classList.remove('hidden');
    }

    function closeProductModal() {
        document.getElementById('productModal').classList.add('hidden');
    }
</script>
@endpush
