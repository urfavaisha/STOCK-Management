<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Store;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Category;
use App\Models\Supplier;

class WarehouseValueSeeder extends Seeder
{
    public function run(): void
    {
        // Create a category and supplier first
        $category = Category::firstOrCreate(['name' => 'Premium Products']);
        $supplier = Supplier::firstOrCreate(
            ['first_name' => 'Premium', 'last_name' => 'Supplier'],
            ['email' => 'premium@example.com', 'phone' => '1234567890']
        );

        // Create Lind-Gislason warehouse if it doesn't exist
        $lindGislason = Store::firstOrCreate(
            ['name' => 'Lind-Gislason'],
            ['address' => '123 Main St']
        );

        // Create warehouses with higher values
        $highValueWarehouses = [
            ['name' => 'Premium Storage', 'address' => '456 High St'],
            ['name' => 'Elite Warehouse', 'address' => '789 Elite Ave'],
            ['name' => 'Mega Storage', 'address' => '321 Mega Blvd'],
        ];

        foreach ($highValueWarehouses as $warehouse) {
            $store = Store::firstOrCreate(
                ['name' => $warehouse['name']],
                ['address' => $warehouse['address']]
            );

            // Add some high-value products to each warehouse
            for ($i = 1; $i <= 5; $i++) {
                $product = Product::create([
                    'name' => "Premium Product {$i} for {$warehouse['name']}",
                    'description' => "High-value product for {$warehouse['name']}",
                    'price' => rand(1000, 5000), // High prices between 1000 and 5000
                    'category_id' => $category->id,
                    'supplier_id' => $supplier->id,
                ]);

                // Add stock with high quantity
                Stock::create([
                    'product_id' => $product->id,
                    'store_id' => $store->id,
                    'quantity_stock' => rand(10, 50), // High quantities
                ]);
            }
        }

        // Add some products to Lind-Gislason with lower values
        for ($i = 1; $i <= 3; $i++) {
            $product = Product::create([
                'name' => "Basic Product {$i}",
                'description' => "Basic product for Lind-Gislason",
                'price' => rand(100, 500), // Lower prices
                'category_id' => $category->id,
                'supplier_id' => $supplier->id,
            ]);

            Stock::create([
                'product_id' => $product->id,
                'store_id' => $lindGislason->id,
                'quantity_stock' => rand(5, 15), // Lower quantities
            ]);
        }
    }
} 