<?php

namespace Database\Seeders;

use App\Models\{Category, Product, Sale, SaleItem, User};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Users ──────────────────────────────────────────────────────────
        $superAdmin = User::create([
            'name'     => 'Super Admin',
            'email'    => 'admin@store.com',
            'password' => Hash::make('Admin@1234'),
            'role'     => 'super_admin',
            'phone'    => '+250788000001',
        ]);

        $shopOwner = User::create([
            'name'       => 'Shop Owner',
            'email'      => 'owner@store.com',
            'password'   => Hash::make('Owner@1234'),
            'role'       => 'shop_owner',
            'phone'      => '+250788000002',
            'created_by' => $superAdmin->id,
        ]);

        $seller1 = User::create([
            'name'       => 'John Seller',
            'email'      => 'seller1@store.com',
            'password'   => Hash::make('Seller@1234'),
            'role'       => 'seller',
            'phone'      => '+250788000003',
            'created_by' => $shopOwner->id,
        ]);

        $seller2 = User::create([
            'name'       => 'Jane Doe',
            'email'      => 'seller2@store.com',
            'password'   => Hash::make('Seller@1234'),
            'role'       => 'seller',
            'phone'      => '+250788000004',
            'created_by' => $shopOwner->id,
        ]);

        // ── Categories ─────────────────────────────────────────────────────
        $electronics = Category::create(['name' => 'Electronics', 'description' => 'Electronic gadgets and devices', 'created_by' => $shopOwner->id]);
        $clothing    = Category::create(['name' => 'Clothing', 'description' => 'Apparel and fashion items', 'created_by' => $shopOwner->id]);
        $food        = Category::create(['name' => 'Food & Beverages', 'description' => 'Consumable goods', 'created_by' => $shopOwner->id]);
        $beauty      = Category::create(['name' => 'Beauty & Health', 'description' => 'Personal care products', 'created_by' => $shopOwner->id]);

        // ── Products ───────────────────────────────────────────────────────
        $products = [
            ['name' => 'Samsung Galaxy A54', 'price' => 350000, 'cost_price' => 280000, 'stock_quantity' => 25, 'low_stock_threshold' => 5, 'category_id' => $electronics->id],
            ['name' => 'Wireless Earbuds Pro', 'price' => 45000, 'cost_price' => 30000, 'stock_quantity' => 50, 'low_stock_threshold' => 10, 'category_id' => $electronics->id],
            ['name' => 'USB-C Charging Cable', 'price' => 5000, 'cost_price' => 2500, 'stock_quantity' => 200, 'low_stock_threshold' => 30, 'category_id' => $electronics->id],
            ['name' => 'Men\'s Classic T-Shirt', 'price' => 8000, 'cost_price' => 4000, 'stock_quantity' => 100, 'low_stock_threshold' => 20, 'category_id' => $clothing->id],
            ['name' => 'Women\'s Denim Jeans', 'price' => 25000, 'cost_price' => 15000, 'stock_quantity' => 60, 'low_stock_threshold' => 10, 'category_id' => $clothing->id],
            ['name' => 'Running Shoes', 'price' => 35000, 'cost_price' => 22000, 'stock_quantity' => 40, 'low_stock_threshold' => 8, 'category_id' => $clothing->id],
            ['name' => 'Mineral Water 500ml', 'price' => 500, 'cost_price' => 200, 'stock_quantity' => 500, 'low_stock_threshold' => 50, 'category_id' => $food->id],
            ['name' => 'Energy Drink 250ml', 'price' => 1500, 'cost_price' => 800, 'stock_quantity' => 8, 'low_stock_threshold' => 20, 'category_id' => $food->id],
            ['name' => 'Face Moisturizer SPF30', 'price' => 12000, 'cost_price' => 7000, 'stock_quantity' => 3, 'low_stock_threshold' => 10, 'category_id' => $beauty->id],
            ['name' => 'Shampoo 400ml', 'price' => 6500, 'cost_price' => 3500, 'stock_quantity' => 80, 'low_stock_threshold' => 15, 'category_id' => $beauty->id],
        ];

        $productModels = [];
        foreach ($products as $p) {
            $productModels[] = Product::create(array_merge($p, [
                'sku'        => Product::generateSku(),
                'created_by' => $shopOwner->id,
            ]));
        }

        // ── Sales ──────────────────────────────────────────────────────────
        $sellers = [$seller1, $seller2];
        for ($i = 0; $i < 15; $i++) {
            $seller      = $sellers[$i % 2];
            $totalAmount = 0;
            $saleItems   = [];

            $count = rand(1, 3);
            for ($j = 0; $j < $count; $j++) {
                $product  = $productModels[array_rand($productModels)];
                $qty      = rand(1, 3);
                $subtotal = $product->price * $qty;
                $totalAmount += $subtotal;
                $saleItems[] = ['product' => $product, 'qty' => $qty, 'subtotal' => $subtotal];
            }

            $discount  = $i % 5 === 0 ? round($totalAmount * 0.05) : 0;
            $netAmount = $totalAmount - $discount;

            $sale = Sale::create([
                'sale_number'    => Sale::generateSaleNumber(),
                'total_amount'   => $totalAmount,
                'discount'       => $discount,
                'tax'            => 0,
                'net_amount'     => $netAmount,
                'payment_method' => ['cash', 'card', 'mobile_money'][rand(0, 2)],
                'status'         => 'completed',
                'customer_name'  => 'Customer ' . ($i + 1),
                'seller_id'      => $seller->id,
                'created_at'     => now()->subDays(rand(0, 30)),
            ]);

            foreach ($saleItems as $item) {
                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $item['product']->id,
                    'quantity'   => $item['qty'],
                    'unit_price' => $item['product']->price,
                    'subtotal'   => $item['subtotal'],
                ]);
            }
        }

        $this->command->info('✅ Seed complete!');
        $this->command->table(['Role', 'Email', 'Password'], [
            ['Super Admin', 'admin@store.com',   'Admin@1234'],
            ['Shop Owner',  'owner@store.com',   'Owner@1234'],
            ['Seller 1',    'seller1@store.com', 'Seller@1234'],
            ['Seller 2',    'seller2@store.com', 'Seller@1234'],
        ]);
    }
}
