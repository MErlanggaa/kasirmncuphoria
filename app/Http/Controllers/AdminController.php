<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalRevenue = Transaction::sum('total_amount');
        $todayRevenue = Transaction::whereDate('created_at', today())->sum('total_amount');

        $totalTransactions = Transaction::count();
        $diTempatCount = Transaction::where('type', 'di_tempat')->count();
        $poCount = Transaction::where('type', 'po')->count();

        $poWaitingCount = Transaction::where('type', 'po')->where('pickup_status', 'menunggu_pengambilan')->count();
        $poPickedUpCount = Transaction::where('type', 'po')->where('pickup_status', 'sudah_diambil')->count();

        // Cashier performance breakdown
        $cashiers = User::where('role', 'kasir')
            ->withCount('transactions')
            ->withSum('transactions', 'total_amount')
            ->orderBy('transactions_sum_total_amount', 'desc')
            ->get();

        $recentTransactions = Transaction::with(['cashier', 'items'])
            ->latest()
            ->take(8)
            ->get();

        return view('admin.dashboard', compact(
            'totalRevenue',
            'todayRevenue',
            'totalTransactions',
            'diTempatCount',
            'poCount',
            'poWaitingCount',
            'poPickedUpCount',
            'cashiers',
            'recentTransactions'
        ));
    }

    /**
     * Product Management
     */
    public function products()
    {
        $products = Product::latest()->paginate(15);
        $categories = ['Makanan', 'Minuman', 'Snack', 'Paket', 'Lainnya'];
        return view('admin.products', compact('products', 'categories'));
    }

    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'category' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'is_available' => 'nullable|boolean',
            'status' => 'required|in:ready,po,habis',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        Product::create([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'price' => $validated['price'],
            'stock' => $validated['stock'] ?? 100,
            'description' => $validated['description'] ?? null,
            'image' => $imagePath ? asset('storage/' . $imagePath) : null,
            'is_available' => $request->has('is_available'),
            'status' => $validated['status'],
        ]);

        return back()->with('success', 'Produk "' . $validated['name'] . '" berhasil ditambahkan!');
    }

    public function updateProduct(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'category' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'is_available' => 'nullable|boolean',
            'status' => 'required|in:ready,po,habis',
        ]);

        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            $uploaded = $request->file('image')->store('products', 'public');
            $imagePath = asset('storage/' . $uploaded);
        }

        $product->update([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'price' => $validated['price'],
            'stock' => $validated['stock'] ?? $product->stock,
            'description' => $validated['description'] ?? null,
            'image' => $imagePath,
            'is_available' => $request->has('is_available'),
            'status' => $validated['status'],
        ]);

        return back()->with('success', 'Produk "' . $product->name . '" berhasil diperbarui!');
    }

    public function deleteProduct(Product $product)
    {
        $name = $product->name;
        $product->delete();
        return back()->with('success', 'Produk "' . $name . '" telah dihapus.');
    }

    /**
     * Cashier Account Management
     */
    public function cashiers()
    {
        $cashiers = User::where('role', 'kasir')
            ->withCount('transactions')
            ->withSum('transactions', 'total_amount')
            ->latest()
            ->get();

        return view('admin.cashiers', compact('cashiers'));
    }

    public function storeCashier(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:20',
        ]);

        User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'role' => 'kasir',
            'is_active' => true,
        ]);

        return back()->with('success', 'Akun Kasir "' . $validated['name'] . '" berhasil dibuat!');
    }

    public function updateCashier(Request $request, User $user)
    {
        if ($user->role !== 'kasir') {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username,' . $user->id,
            'email' => 'required|email|max:100|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
        ]);

        $userData = [
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'is_active' => $request->has('is_active'),
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);

        return back()->with('success', 'Data Kasir "' . $user->name . '" berhasil diperbarui!');
    }

    public function toggleCashierStatus(User $user)
    {
        if ($user->role !== 'kasir') {
            abort(403);
        }

        $user->update(['is_active' => !$user->is_active]);

        $statusStr = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', 'Akun Kasir "' . $user->name . '" telah ' . $statusStr . '.');
    }

    /**
     * Transactions Report & History
     */
    public function transactions(Request $request)
    {
        $query = Transaction::with(['cashier', 'items', 'pickedUpByUser'])->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('cashier_id')) {
            $query->where('cashier_id', $request->cashier_id);
        }

        if ($request->filled('pickup_status')) {
            $query->where('pickup_status', $request->pickup_status);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('invoice_number', 'like', "%{$s}%")
                    ->orWhere('customer_name', 'like', "%{$s}%")
                    ->orWhere('customer_phone', 'like', "%{$s}%")
                    ->orWhere('po_token', 'like', "%{$s}%");
            });
        }

        $transactions = $query->paginate(20)->withQueryString();
        $cashiers = User::where('role', 'kasir')->get();

        return view('admin.transactions', compact('transactions', 'cashiers'));
    }
    /**
     * Admin Settings Page
     */
    public function settings()
    {
        $settings = [
            'store_name' => Setting::get('store_name', 'Danusan Store'),
            'merchant_name' => Setting::get('merchant_name', 'DANUSAN'),
            'qris_static_payload' => Setting::get('qris_static_payload', ''),
        ];

        return view('admin.settings', compact('settings'));
    }

    public function saveSettings(Request $request)
    {
        $request->validate([
            'store_name' => 'required|string|max:100',
            'merchant_name' => 'nullable|string|max:100',
            'qris_static_payload' => 'required|string|min:10',
        ]);

        Setting::set('store_name', $request->input('store_name'));
        Setting::set('merchant_name', $request->input('merchant_name') ?: $request->input('store_name'));
        Setting::set('qris_static_payload', trim($request->input('qris_static_payload')));

        return back()->with('success', 'Pengaturan berhasil disimpan!');
    }
}
