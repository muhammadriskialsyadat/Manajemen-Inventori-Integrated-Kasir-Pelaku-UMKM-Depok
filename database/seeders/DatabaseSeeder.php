<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. ROLES & USERS (Spatie Permission)
        $this->call(RoleAndPermissionSeeder::class);

        // 2. APP SETTINGS — default notification toggles
        $this->call(AppSettingSeeder::class);

        // 2. CREATE CATEGORIES DULU (PENTING!)
        $categories = [
            ['name' => 'Cat Tembok', 'description' => 'Cat untuk interior dan eksterior tembok'],
            ['name' => 'Cat Kayu', 'description' => 'Cat khusus untuk furniture dan kayu'],
            ['name' => 'Cat Besi', 'description' => 'Cat anti karat untuk besi dan logam'],
            ['name' => 'Thinner & Pelarut', 'description' => 'Thinner dan bahan pelarut cat'],
            ['name' => 'Alat Cat', 'description' => 'Kuas, roller, dan alat bantu pengecatan'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }

        // 3. CREATE SUPPLIERS
        $suppliers = [
            [
                'name' => 'PT. Mowilex Indonesia',
                'contact_person' => 'Budi Santoso',
                'phone' => '021-12345678',
                'email' => 'budi@mowilex.co.id',
                'address' => 'Jakarta Selatan',
            ],
            [
                'name' => 'PT. Avian Brands',
                'contact_person' => 'Siti Nurhaliza',
                'phone' => '021-87654321',
                'email' => 'siti@avian.co.id',
                'address' => 'Tangerang',
            ],
            [
                'name' => 'CV. Jaya Sentosa',
                'contact_person' => 'Ahmad Yani',
                'phone' => '021-55555555',
                'email' => 'ahmad@jayasentosa.com',
                'address' => 'Bekasi',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::updateOrCreate(
                ['name' => $supplier['name']],
                $supplier
            );
        }

        // 4. CREATE CUSTOMERS
        $customers = [
            [
                'name' => 'Toko Bangunan Sejahtera',
                'phone' => '081234567890',
                'email' => 'sejahtera@gmail.com',
                'address' => 'Jl. Sudirman No. 123, Jakarta',
            ],
            [
                'name' => 'CV. Mandiri Jaya',
                'phone' => '081234567891',
                'email' => 'mandiri@gmail.com',
                'address' => 'Jl. Gatot Subroto No. 45, Bandung',
            ],
            [
                'name' => 'UD. Sumber Rejeki',
                'phone' => '081234567892',
                'email' => 'rejeki@gmail.com',
                'address' => 'Jl. Ahmad Yani No. 78, Surabaya',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::updateOrCreate(
                ['phone' => $customer['phone']],
                $customer
            );
        }

        // 5. CREATE PRODUCTS (SETELAH CATEGORIES ADA!)
        $products = [
            // Cat Tembok (category_id = 1)
            [
                'name' => 'Mowilex Emulsion Paint',
                'code' => 'MWX-001',
                'category_id' => 1,
                'brand' => 'Mowilex',
                'color' => 'Putih',
                'size' => '2.5L',
                'unit' => 'Kaleng',
                'minimum_stock' => 10,
                'current_stock' => 25,
                'purchase_price' => 85000,
                'selling_price' => 95000,
                'description' => 'Cat tembok berkualitas tinggi',
            ],
            [
                'name' => 'Avian Catylac Interior',
                'code' => 'AVN-001',
                'category_id' => 1,
                'brand' => 'Avian',
                'color' => 'Broken White',
                'size' => '5L',
                'unit' => 'Galon',
                'minimum_stock' => 15,
                'current_stock' => 30,
                'purchase_price' => 150000,
                'selling_price' => 170000,
                'description' => 'Cat interior premium',
            ],
            [
                'name' => 'Dulux Weathershield',
                'code' => 'DLX-001',
                'category_id' => 1,
                'brand' => 'Dulux',
                'color' => 'Ivory',
                'size' => '2.5L',
                'unit' => 'Kaleng',
                'minimum_stock' => 12,
                'current_stock' => 20,
                'purchase_price' => 120000,
                'selling_price' => 140000,
                'description' => 'Cat eksterior tahan cuaca',
            ],

            // Cat Kayu (category_id = 2)
            [
                'name' => 'Avian Wood Stain',
                'code' => 'AVN-WS-001',
                'category_id' => 2,
                'brand' => 'Avian',
                'color' => 'Teak',
                'size' => '1L',
                'unit' => 'Kaleng',
                'minimum_stock' => 8,
                'current_stock' => 15,
                'purchase_price' => 45000,
                'selling_price' => 55000,
                'description' => 'Cat kayu transparan warna kayu jati',
            ],
            [
                'name' => 'Propan PU Clear Gloss',
                'code' => 'PRP-001',
                'category_id' => 2,
                'brand' => 'Propan',
                'color' => 'Clear',
                'size' => '1L',
                'unit' => 'Kaleng',
                'minimum_stock' => 10,
                'current_stock' => 18,
                'purchase_price' => 65000,
                'selling_price' => 75000,
                'description' => 'Cat kayu polyurethane mengkilap',
            ],

            // Cat Besi (category_id = 3)
            [
                'name' => 'Avitex Anti Rust Primer',
                'code' => 'AVT-001',
                'category_id' => 3,
                'brand' => 'Avitex',
                'color' => 'Grey',
                'size' => '1L',
                'unit' => 'Kaleng',
                'minimum_stock' => 10,
                'current_stock' => 12,
                'purchase_price' => 50000,
                'selling_price' => 60000,
                'description' => 'Cat dasar anti karat untuk besi',
            ],

            // Thinner (category_id = 4)
            [
                'name' => 'Thinner A Special',
                'code' => 'THN-001',
                'category_id' => 4,
                'brand' => 'Generic',
                'color' => null,
                'size' => '1L',
                'unit' => 'Botol',
                'minimum_stock' => 20,
                'current_stock' => 50,
                'purchase_price' => 15000,
                'selling_price' => 18000,
                'description' => 'Thinner khusus untuk cat minyak',
            ],

            // Alat Cat (category_id = 5)
            [
                'name' => 'Kuas Tembok 3 inch',
                'code' => 'KUS-001',
                'category_id' => 5,
                'brand' => 'Generic',
                'color' => null,
                'size' => '3 inch',
                'unit' => 'Pcs',
                'minimum_stock' => 30,
                'current_stock' => 45,
                'purchase_price' => 8000,
                'selling_price' => 12000,
                'description' => 'Kuas tembok ukuran 3 inch',
            ],
            [
                'name' => 'Roller Paint 7 inch',
                'code' => 'ROL-001',
                'category_id' => 5,
                'brand' => 'Generic',
                'color' => null,
                'size' => '7 inch',
                'unit' => 'Pcs',
                'minimum_stock' => 25,
                'current_stock' => 40,
                'purchase_price' => 15000,
                'selling_price' => 20000,
                'description' => 'Roller cat untuk tembok luas',
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['code' => $product['code']], // Cek berdasarkan code
                $product // Data yang akan di-insert/update
            );
        }

        $this->command->info('✅ Master data seeding completed successfully!');
    }
}
