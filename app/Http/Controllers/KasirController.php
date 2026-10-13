<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Services\QrisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KasirController extends Controller
{
    public function pos()
    {
        $user = Auth::user();

        // Get available products (only ready and po, not habis)
        $products = Product::whereIn('status', ['ready', 'po'])->orderBy('category')->orderBy('name')->get();
        $categories = Product::whereIn('status', ['ready', 'po'])->distinct()->pluck('category');

        // Settings
        $storeName = Setting::get('store_name', 'Danusan Store');
        $merchantName = Setting::get('merchant_name', 'DANUSAN');
        $qrisStatic = Setting::get('qris_static_payload', '');

        // Cashier shift statistics for today
        $todayTransactions = Transaction::where('cashier_id', $user->id)
            ->whereDate('created_at', today())
            ->get();

        $todayTotal = $todayTransactions->sum('total_amount');
        $todayCount = $todayTransactions->count();
        $todayDiTempatCount = $todayTransactions->where('type', 'di_tempat')->count();
        $todayPoCount = $todayTransactions->where('type', 'po')->count();

        return view('kasir.pos', compact(
            'products',
            'categories',
            'storeName',
            'merchantName',
            'qrisStatic',
            'todayTotal',
            'todayCount',
            'todayDiTempatCount',
            'todayPoCount'
        ));
    }

    /**
     * API to convert nominal to dynamic QRIS string
     */
    public function generateDynamicQris(Request $request)
    {
        $amount = (float) $request->input('amount', 0);
        $staticPayload = Setting::get('qris_static_payload', '');

        $dynamicPayload = QrisService::makeDynamic($staticPayload, $amount);

        return response()->json([
            'success' => true,
            'amount' => $amount,
            'dynamic_payload' => $dynamicPayload,
            'merchant_name' => Setting::get('merchant_name', 'DANUSAN'),
        ]);
    }

    /**
     * Store new transaction (Beli di Tempat or PO)
     */
    public function storeTransaction(Request $request)
    {
        $request->validate([
            'type' => 'required|in:di_tempat,po',
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'nullable|string|max:25',
            'notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_proof' => 'nullable|string', // base64 photo from camera
        ]);

        if ($request->input('type') === 'po' && empty($request->input('customer_phone'))) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor WhatsApp / HP wajib diisi untuk pesanan Pre-Order (PO).'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $cashier = Auth::user();
            $type = $request->input('type');
            $todayStr = date('Ymd');
            $uniqueCount = Transaction::whereDate('created_at', today())->count() + 1;
            $invoiceNumber = 'DNS-' . $todayStr . '-' . str_pad($uniqueCount, 4, '0', STR_PAD_LEFT);

            // Generate unique PO token if type is PO
            $poToken = null;
            $pickupStatus = 'none';

            if ($type === 'po') {
                do {
                    $candidateToken = 'PO-' . strtoupper(Str::random(6));
                } while (Transaction::where('po_token', $candidateToken)->exists());

                $poToken = $candidateToken;
                $pickupStatus = 'menunggu_pengambilan';
            }

            // Handle Camera snapshot image
            $proofImagePath = null;
            if ($request->filled('payment_proof')) {
                $base64Image = $request->input('payment_proof');
                // Remove base64 header if present
                if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $typeMatch)) {
                    $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
                    $extension = strtolower($typeMatch[1]);
                } else {
                    $extension = 'jpg';
                }

                $imageBinary = base64_decode($base64Image);
                if ($imageBinary !== false) {
                    $filename = 'proofs/' . $invoiceNumber . '_' . time() . '.' . $extension;
                    Storage::disk('public')->put($filename, $imageBinary);
                    $proofImagePath = $filename;
                }
            }

            // Create Transaction record
            $transaction = Transaction::create([
                'invoice_number' => $invoiceNumber,
                'type' => $type,
                'cashier_id' => $cashier->id,
                'customer_name' => $request->input('customer_name'),
                'customer_phone' => $request->input('customer_phone'),
                'notes' => $request->input('notes'),
                'total_amount' => 0, // will calculate below
                'payment_method' => 'qris',
                'payment_status' => 'paid',
                'payment_proof' => $proofImagePath,
                'po_token' => $poToken,
                'pickup_status' => $pickupStatus,
            ]);

            $totalAmount = 0;
            $itemDetails = [];

            foreach ($request->input('items') as $item) {
                $product = Product::find($item['product_id']);
                if ($product) {
                    $qty = (int) $item['quantity'];
                    $subtotal = $product->price * $qty;
                    $totalAmount += $subtotal;

                    $txItem = TransactionItem::create([
                        'transaction_id' => $transaction->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'price' => $product->price,
                        'quantity' => $qty,
                        'subtotal' => $subtotal,
                    ]);

                    $itemDetails[] = [
                        'name' => $product->name,
                        'price' => $product->price,
                        'quantity' => $qty,
                        'subtotal' => $subtotal,
                    ];

                    // Decrement stock
                    $product->stock = max(0, $product->stock - $qty);
                    if ($product->stock === 0) {
                        $product->status = 'habis';
                    }
                    $product->save();
                }
            }

            $transaction->update(['total_amount' => $totalAmount]);

            DB::commit();

            // WhatsApp Share link for PO
            $waLink = null;
            if ($type === 'po' && $transaction->customer_phone) {
                $cleanPhone = preg_replace('/[^0-9]/', '', $transaction->customer_phone);
                if (str_starts_with($cleanPhone, '0')) {
                    $cleanPhone = '62' . substr($cleanPhone, 1);
                }

                $itemsText = "";
                foreach ($itemDetails as $it) {
                    $itemsText .= "• " . $it['name'] . " (" . $it['quantity'] . "x) - Rp " . number_format($it['subtotal'], 0, ',', '.') . "\n";
                }

                $ticketUrl = route('ticket.show', ['token' => $poToken]);

                $msg = "Halo Kak *" . $transaction->customer_name . "*! Terima kasih sudah Pre-Order di " . Setting::get('store_name', 'Danusan') . ".\n\n"
                    . "*Detail Pesanan PO:*\n"
                    . "Kode Unik: *" . $poToken . "*\n"
                    . "Invoice: " . $invoiceNumber . "\n"
                    . $itemsText . "\n"
                    . "Total Bayar: *Rp " . number_format($totalAmount, 0, ',', '.') . " (LUNAS via QRIS)*\n\n"
                    . "Tunjukkan Kode QR/Tiket berikut saat pengambilan barang:\n"
                    . $ticketUrl . "\n\n"
                    . "Simpan link tiket ini ya! Terima kasih banyak!";

                $waLink = "https://wa.me/" . $cleanPhone . "?text=" . urlencode($msg);
            }

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan ke database!',
                'transaction' => [
                    'id' => $transaction->id,
                    'invoice_number' => $transaction->invoice_number,
                    'type' => $transaction->type,
                    'customer_name' => $transaction->customer_name,
                    'customer_phone' => $transaction->customer_phone,
                    'total_amount' => $transaction->total_amount,
                    'total_formatted' => 'Rp ' . number_format($transaction->total_amount, 0, ',', '.'),
                    'po_token' => $transaction->po_token,
                    'created_at' => $transaction->created_at->format('d M Y H:i'),
                    'cashier_name' => $cashier->name,
                    'proof_image_url' => $proofImagePath ? asset('storage/' . $proofImagePath) : null,
                    'wa_link' => $waLink,
                    'ticket_url' => $poToken ? route('ticket.show', ['token' => $poToken]) : null,
                    'items' => $itemDetails,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * PO Pickup & Scanner page
     */
    public function pickupScanner()
    {
        $recentPickups = Transaction::with(['cashier', 'pickedUpByUser', 'items'])
            ->where('type', 'po')
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get();

        return view('kasir.pickup', compact('recentPickups'));
    }

    /**
     * Verify PO Token (AJAX)
     */
    public function verifyPoToken(Request $request, $token)
    {
        $token = trim($token);

        $transaction = Transaction::with(['cashier', 'pickedUpByUser', 'items'])
            ->where('type', 'po')
            ->where('po_token', $token)
            ->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Kode PO "' . $token . '" tidak ditemukan. Mohon periksa kembali kode QR atau token.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'transaction' => [
                'id' => $transaction->id,
                'invoice_number' => $transaction->invoice_number,
                'po_token' => $transaction->po_token,
                'customer_name' => $transaction->customer_name,
                'customer_phone' => $transaction->customer_phone,
                'notes' => $transaction->notes,
                'total_amount' => $transaction->total_amount,
                'total_formatted' => 'Rp ' . number_format($transaction->total_amount, 0, ',', '.'),
                'pickup_status' => $transaction->pickup_status,
                'picked_up_at' => $transaction->picked_up_at ? $transaction->picked_up_at->format('d M Y H:i') : null,
                'picked_up_by_name' => $transaction->pickedUpByUser ? $transaction->pickedUpByUser->name : null,
                'cashier_name' => $transaction->cashier->name,
                'created_at' => $transaction->created_at->format('d M Y H:i'),
                'proof_image_url' => $transaction->payment_proof ? asset('storage/' . $transaction->payment_proof) : null,
                'items' => $transaction->items->map(function ($it) {
                    return [
                        'name' => $it->product_name,
                        'price' => $it->price,
                        'quantity' => $it->quantity,
                        'subtotal' => $it->subtotal,
                        'subtotal_formatted' => 'Rp ' . number_format($it->subtotal, 0, ',', '.'),
                    ];
                }),
            ]
        ]);
    }

    /**
     * Redeem / Confirm PO Pickup
     */
    public function confirmPickup(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);

        if ($transaction->type !== 'po') {
            return response()->json(['success' => false, 'message' => 'Bukan transaksi PO.'], 400);
        }

        if ($transaction->pickup_status === 'sudah_diambil') {
            return response()->json([
                'success' => false,
                'message' => 'Barang untuk pesanan ini sudah diambil sebelumnya pada ' . ($transaction->picked_up_at ? $transaction->picked_up_at->format('d M Y H:i') : '-')
            ], 400);
        }

        $user = Auth::user();
        $transaction->update([
            'pickup_status' => 'sudah_diambil',
            'picked_up_at' => now(),
            'picked_up_by' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil! Barang telah diserahkan kepada pemesan (' . $transaction->customer_name . ').',
            'picked_up_at' => now()->format('d M Y H:i'),
            'verifier' => $user->name,
        ]);
    }
}
