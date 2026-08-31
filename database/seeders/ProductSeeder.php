<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Seed contoh produk toko.
     */
    public function run(): void
    {
        $products = [
            ['sku' => 'TRP-001', 'name' => 'Sepatu Sneakers Pria', 'price' => 349000, 'reference' => 'REF-TRP-001'],
            ['sku' => 'TRP-002', 'name' => 'Jam Tangan Digital', 'price' => 189000, 'reference' => 'REF-TRP-002'],
            ['sku' => 'TRP-003', 'name' => 'Tas Ransel Kanvas', 'price' => 275000, 'reference' => 'REF-TRP-003'],
            ['sku' => 'TRP-004', 'name' => 'Earbuds Wireless', 'price' => 229000, 'reference' => 'REF-TRP-004'],
            ['sku' => 'TRP-005', 'name' => 'Kemeja Flanel', 'price' => 159000, 'reference' => 'REF-TRP-005'],
            ['sku' => 'TRP-006', 'name' => 'Kacamata Vintage', 'price' => 129000, 'reference' => 'REF-TRP-006'],
            ['sku' => 'TRP-007', 'name' => 'Botol Minum Steel', 'price' => 89000, 'reference' => 'REF-TRP-007'],
            ['sku' => 'TRP-008', 'name' => 'Speaker Bluetooth', 'price' => 315000, 'reference' => 'REF-TRP-008'],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(['sku' => $product['sku']], $product);
        }
    }
}
