<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@danusan.com'],
            [
                'name' => 'Admin Utama Danusan',
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'phone' => '081234567890',
                'is_active' => true,
            ]
        );

        // 2. Seed Cashiers (Multiple Cashier Accounts)
        $cashiers = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@danusan.com',
                'username' => 'kasir_budi',
                'password' => Hash::make('kasir123'),
                'phone' => '081211112222',
            ],
            [
                'name' => 'Siti Rahma',
                'email' => 'siti@danusan.com',
                'username' => 'kasir_siti',
                'password' => Hash::make('kasir123'),
                'phone' => '081233334444',
            ],
            [
                'name' => 'Dimas Pratama',
                'email' => 'dimas@danusan.com',
                'username' => 'kasir_dimas',
                'password' => Hash::make('kasir123'),
                'phone' => '081255556666',
            ],
        ];

        $cashierUsers = [];
        foreach ($cashiers as $c) {
            $user = User::updateOrCreate(
                ['email' => $c['email']],
                [
                    'name' => $c['name'],
                    'username' => $c['username'],
                    'password' => $c['password'],
                    'role' => 'kasir',
                    'phone' => $c['phone'],
                    'is_active' => true,
                ]
            );
            $cashierUsers[] = $user;
        }

        // 3. Seed Products (Danusan favorites)
        $products = [
            [
                'name' => 'Ayam Geprek Sambal Bawang',
                'category' => 'Makanan',
                'price' => 15000,
                'description' => 'Ayam goreng crispy digeprek dengan sambal bawang pedas gurih sedap.',
                'image' => 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=500&q=80',
                'stock' => 50,
                'is_available' => true,
                'status' => 'ready',
            ],
            [
                'name' => 'Chicken Katsu Crispy',
                'category' => 'Makanan',
                'price' => 18000,
                'description' => 'Fillet ayam katsu renyah khas Jepang disajikan dengan saus mayo & saus tonkatsu.',
                'image' => 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?auto=format&fit=crop&w=500&q=80',
                'stock' => 40,
                'is_available' => true,
                'status' => 'ready',
            ],
            [
                'name' => 'Strawberry Coklat Kunafa',
                'category' => 'Dessert',
                'price' => 25000,
                'description' => 'Kunafa pastry renyah khas Timur Tengah berpadu saus coklat leleh dan strawberry segar.',
                'image' => 'https://images.unsplash.com/photo-1587314168485-3236d6710814?auto=format&fit=crop&w=500&q=80',
                'stock' => 35,
                'is_available' => true,
                'status' => 'po',
            ],
            [
                'name' => 'Kunafa Pistachio Coklat Lumer',
                'category' => 'Dessert',
                'price' => 28000,
                'description' => 'Dessert kunafa viral dengan pasta pistachio premium dan siraman coklat tebal.',
                'image' => 'https://images.unsplash.com/photo-1579372786545-d24232daf58c?auto=format&fit=crop&w=500&q=80',
                'stock' => 30,
                'is_available' => true,
                'status' => 'po',
            ],
            [
                'name' => 'Kebab Daging Sapi Spesial',
                'category' => 'Snack',
                'price' => 15000,
                'description' => 'Tortilla hangat dengan isian irisan daging sapi panggang melimpah, sayuran, dan keju mayo.',
                'image' => 'https://images.unsplash.com/photo-1529006557810-274b9b2fc783?auto=format&fit=crop&w=500&q=80',
                'stock' => 60,
                'is_available' => true,
                'status' => 'ready',
            ],
            [
                'name' => 'Classic Beef Burger Cheese',
                'category' => 'Makanan',
                'price' => 16000,
                'description' => 'Bun lembut dengan beef patty gurih juicy, keju cheddar melt, dan saus spesial.',
                'image' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?auto=format&fit=crop&w=500&q=80',
                'stock' => 45,
                'is_available' => true,
                'status' => 'ready',
            ],
            [
                'name' => 'Es Teh Solo Manis Jumbo',
                'category' => 'Minuman',
                'price' => 5000,
                'description' => 'Teh racikan khas Solo wangi sepet manis dingin segar.',
                'image' => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?auto=format&fit=crop&w=500&q=80',
                'stock' => 100,
                'is_available' => true,
                'status' => 'ready',
            ],
            [
                'name' => 'Es Kopi Susu Gula Aren',
                'category' => 'Minuman',
                'price' => 10000,
                'description' => 'Kopi house blend susu aren dingin mantap.',
                'image' => 'https://images.unsplash.com/photo-1517256064527-09c73fc73e38?auto=format&fit=crop&w=500&q=80',
                'stock' => 50,
                'is_available' => true,
                'status' => 'ready',
            ],
        ];

        $createdProducts = [];
        foreach ($products as $p) {
            $createdProducts[] = Product::updateOrCreate(
                ['name' => $p['name']],
                $p
            );
        }

        // 4. Seed Settings (KEDAIPHORIA, KBN JRK)
        Setting::set('store_name', 'KEDAIPHORIA, KBN JRK');
        Setting::set('merchant_name', 'KEDAIPHORIA, KBN JRK');
        Setting::set('merchant_city', 'JAKARTA BARAT');
        Setting::set('qris_static_payload', '00020101021126610014COM.GO-JEK.WWW01189360091431783159330210G1783159330303UMI51440014ID.CO.QRIS.WWW0215ID10265737707360303UMI5204581253033605802ID5920KEDAIPhoria, KBN JRK6013JAKARTA BARAT61051152062070703A01630415E5');

        // 5. Seed Initial Sample Transactions (if not already seeded)
        if (!Transaction::where('invoice_number', 'DNS-' . date('Ymd') . '-0001')->exists()) {
            $tx1 = Transaction::create([
                'invoice_number' => 'DNS-' . date('Ymd') . '-0001',
                'type' => 'di_tempat',
                'cashier_id' => $cashierUsers[0]->id,
                'customer_name' => 'Kak Andi (Fak. Teknik)',
                'customer_phone' => null,
                'notes' => 'Langsung santap di stand',
                'total_amount' => 30000,
                'payment_method' => 'qris',
                'payment_status' => 'paid',
                'payment_proof' => null,
                'po_token' => null,
                'pickup_status' => 'none',
            ]);

            TransactionItem::create([
                'transaction_id' => $tx1->id,
                'product_id' => $createdProducts[0]->id, // Ayam Geprek
                'product_name' => $createdProducts[0]->name,
                'price' => 15000,
                'quantity' => 2,
                'subtotal' => 30000,
            ]);
        }

        if (!Transaction::where('invoice_number', 'DNS-' . date('Ymd') . '-0002')->exists()) {
            $poToken = 'PO-' . strtoupper(Str::random(6));
            $tx2 = Transaction::create([
                'invoice_number' => 'DNS-' . date('Ymd') . '-0002',
                'type' => 'po',
                'cashier_id' => $cashierUsers[1]->id,
                'customer_name' => 'Kak Nabila Putri',
                'customer_phone' => '081298765432',
                'notes' => 'Ambil jam 14:00 di Selasar Gedung B',
                'total_amount' => 50000,
                'payment_method' => 'qris',
                'payment_status' => 'paid',
                'payment_proof' => null,
                'po_token' => $poToken,
                'pickup_status' => 'menunggu_pengambilan',
            ]);

            TransactionItem::create([
                'transaction_id' => $tx2->id,
                'product_id' => $createdProducts[2]->id, // Strawberry Coklat Kunafa
                'product_name' => $createdProducts[2]->name,
                'price' => 25000,
                'quantity' => 2,
                'subtotal' => 50000,
            ]);
        }
    }
}
