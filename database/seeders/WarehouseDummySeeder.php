<?php

namespace Database\Seeders;

use App\Enums\PaymentMethod;
use App\Enums\PurchaseStatus;
use App\Enums\SaleStatus;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WarehouseDummySeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create([
            'name' => 'Super Admin Gudang',
            'email' => 'superadmin@gudangin.test',
            'username' => 'superadmin',
            'password' => bcrypt('password'),
        ]);

        $suppliers = Supplier::all();
        if ($suppliers->isEmpty()) {
            $suppliers = Supplier::factory()->count(10)->create();
        }

        $customers = Customer::all();
        if ($customers->isEmpty()) {
            $customers = Customer::factory()->count(10)->create();
        }

        $products = Product::where('is_active', true)->get();
        if ($products->isEmpty()) {
            return;
        }

        // 1. Set 4 products as Low Stock for Alert Section
        $lowStockProducts = $products->take(4);
        foreach ($lowStockProducts as $idx => $p) {
            $p->min_stock = 15;
            $p->quantity = 2 + $idx; // 2, 3, 4, 5
            $p->save();
        }

        // 2. Clear existing dummy purchases & sales safely
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        PurchaseItem::truncate();
        Purchase::truncate();
        SaleItem::truncate();
        Sale::truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 3. Generate Inbound (Purchases) and Outbound (Sales) for the past 60 days
        $now = Carbon::now();
        $topProducts = $products->skip(4)->take(10)->values();

        // Generate data for each of the past 45 days
        for ($daysAgo = 45; $daysAgo >= 0; $daysAgo--) {
            $date = $now->copy()->subDays($daysAgo)->setTime(rand(8, 17), rand(0, 59));

            // Inbound Purchases (1-3 transactions per day)
            $numPurchases = ($daysAgo == 0) ? 2 : rand(1, 3);
            for ($p = 0; $p < $numPurchases; $p++) {
                $supplier = $suppliers->random();
                $purchase = Purchase::create([
                    'invoice_number' => 'INB-' . $date->format('Ymd') . '-' . Str::padLeft($daysAgo * 10 + $p + 1, 4, '0'),
                    'supplier_id' => $supplier->id,
                    'purchase_date' => $date->format('Y-m-d'),
                    'due_date' => $date->copy()->addDays(14)->format('Y-m-d'),
                    'total' => 0,
                    'status' => PurchaseStatus::RECEIVED,
                    'notes' => 'Penerimaan rutin stok dari supplier ' . $supplier->name,
                    'created_by' => $user->id,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);

                $totalPurchase = 0;
                $itemCount = rand(2, 4);
                $selectedProducts = $topProducts->random(min($itemCount, $topProducts->count()));

                foreach ($selectedProducts as $prod) {
                    $qty = rand(15, 60);
                    $unitPrice = $prod->purchase_price > 0 ? $prod->purchase_price : 25000;
                    $subtotal = $qty * $unitPrice;
                    $totalPurchase += $subtotal;

                    PurchaseItem::create([
                        'purchase_id' => $purchase->id,
                        'product_id' => $prod->id,
                        'quantity' => $qty,
                        'unit_price' => $unitPrice,
                        'subtotal' => $subtotal,
                        'selling_price' => $prod->selling_price > 0 ? $prod->selling_price : ($unitPrice * 1.3),
                    ]);
                }

                $purchase->update(['total' => $totalPurchase]);
            }

            // Outbound Sales (2-5 transactions per day)
            $numSales = ($daysAgo == 0) ? 3 : rand(2, 5);
            for ($s = 0; $s < $numSales; $s++) {
                $customer = $customers->random();
                $sale = Sale::create([
                    'invoice_number' => 'OUT-' . $date->format('Ymd') . '-' . Str::padLeft($daysAgo * 10 + $s + 1, 4, '0'),
                    'customer_id' => $customer->id,
                    'created_by' => $user->id,
                    'sale_date' => $date,
                    'status' => SaleStatus::COMPLETED,
                    'subtotal' => 0,
                    'global_discount' => 0,
                    'total_discount' => 0,
                    'total' => 0,
                    'cash_received' => 0,
                    'change' => 0,
                    'payment_method' => PaymentMethod::CASH,
                    'notes' => 'Pengeluaran barang gudang untuk ' . $customer->name,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);

                $totalSale = 0;
                $itemCount = rand(1, 3);
                $selectedProducts = $topProducts->random(min($itemCount, $topProducts->count()));

                foreach ($selectedProducts as $prod) {
                    $qty = rand(5, 30);
                    $unitPrice = $prod->selling_price > 0 ? $prod->selling_price : 35000;
                    $costPrice = $prod->purchase_price > 0 ? $prod->purchase_price : 25000;
                    $subtotal = $qty * $unitPrice;
                    $totalSale += $subtotal;

                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $prod->id,
                        'quantity' => $qty,
                        'cost_price' => $costPrice,
                        'unit_price' => $unitPrice,
                        'discount' => 0,
                        'final_price' => $unitPrice,
                        'subtotal' => $subtotal,
                    ]);
                }

                $sale->update([
                    'subtotal' => $totalSale,
                    'total' => $totalSale,
                    'cash_received' => $totalSale,
                ]);
            }
        }
    }
}
