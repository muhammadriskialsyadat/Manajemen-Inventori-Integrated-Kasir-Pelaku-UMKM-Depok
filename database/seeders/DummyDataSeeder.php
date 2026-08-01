<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────────────────────────────
        // KATEGORI 1: Makanan & Minuman Khas Kota Depok
        // ─────────────────────────────────────────────────────────────
        $katMakanan = Category::updateOrCreate(
            ['name' => 'Makanan & Minuman Khas Kota Depok'],
            [
                'name'        => 'Makanan & Minuman Khas Kota Depok',
                'description' => 'Produk makanan dan minuman khas UMKM Kota Depok, meliputi frozen food, makanan kering, dan makanan basah siap saji.',
            ]
        );

        // ── A. Frozen Food ───────────────────────────────────────────
        $frozenFood = [
            [
                'name'           => 'Nugget Ayam Crispy',
                'code'           => 'MKN-FRZ-001',
                'brand'          => 'Fiesta',
                'size'           => '500g',
                'unit'           => 'Pack',
                'minimum_stock'  => 10,
                'current_stock'  => 35,
                'purchase_price' => 28000,
                'selling_price'  => 35000,
                'description'    => 'Nugget ayam premium, cocok untuk goreng',
            ],
            [
                'name'           => 'Sosis Ayam Premium',
                'code'           => 'MKN-FRZ-002',
                'brand'          => 'Bernardi',
                'size'           => '500g',
                'unit'           => 'Pack',
                'minimum_stock'  => 10,
                'current_stock'  => 28,
                'purchase_price' => 22000,
                'selling_price'  => 28000,
                'description'    => 'Sosis ayam untuk camilan atau masak',
            ],
            [
                'name'           => 'Dimsum Ayam (10 pcs)',
                'code'           => 'MKN-FRZ-003',
                'brand'          => 'Kobe',
                'size'           => '250g',
                'unit'           => 'Pack',
                'minimum_stock'  => 8,
                'current_stock'  => 20,
                'purchase_price' => 18000,
                'selling_price'  => 24000,
                'description'    => 'Dimsum ayam siap kukus',
            ],
            [
                'name'           => 'Kentang Goreng Shoestring',
                'code'           => 'MKN-FRZ-004',
                'brand'          => 'Champ',
                'size'           => '1kg',
                'unit'           => 'Pack',
                'minimum_stock'  => 12,
                'current_stock'  => 40,
                'purchase_price' => 32000,
                'selling_price'  => 40000,
                'description'    => 'Kentang goreng beku siap goreng',
            ],
            [
                'name'           => 'Bakso Ikan Frozen',
                'code'           => 'MKN-FRZ-005',
                'brand'          => 'Kanzler',
                'size'           => '500g',
                'unit'           => 'Pack',
                'minimum_stock'  => 8,
                'current_stock'  => 15,
                'purchase_price' => 20000,
                'selling_price'  => 26000,
                'description'    => 'Bakso ikan untuk sup atau goreng',
            ],
        ];

        // ── B. Makanan Kering ────────────────────────────────────────
        $makananKering = [
            [
                'name'           => 'Keripik Singkong Pedas',
                'code'           => 'MKN-KRG-001',
                'brand'          => 'Depok Snack',
                'size'           => '200g',
                'unit'           => 'Pack',
                'minimum_stock'  => 15,
                'current_stock'  => 50,
                'purchase_price' => 8000,
                'selling_price'  => 12000,
                'description'    => 'Keripik singkong olahan lokal Depok',
            ],
            [
                'name'           => 'Abon Sapi Homemade',
                'code'           => 'MKN-KRG-002',
                'brand'          => 'UMKM Depok',
                'size'           => '100g',
                'unit'           => 'Pack',
                'minimum_stock'  => 10,
                'current_stock'  => 30,
                'purchase_price' => 25000,
                'selling_price'  => 32000,
                'description'    => 'Abon sapi buatan UMKM lokal',
            ],
            [
                'name'           => 'Kripik Tempe Original',
                'code'           => 'MKN-KRG-003',
                'brand'          => 'Maeda',
                'size'           => '150g',
                'unit'           => 'Pack',
                'minimum_stock'  => 12,
                'current_stock'  => 45,
                'purchase_price' => 7000,
                'selling_price'  => 10000,
                'description'    => 'Kripik tempe renyah',
            ],
            [
                'name'           => 'Dodol Garut Khas Depok',
                'code'           => 'MKN-KRG-004',
                'brand'          => 'Garut',
                'size'           => '250g',
                'unit'           => 'Pack',
                'minimum_stock'  => 10,
                'current_stock'  => 25,
                'purchase_price' => 15000,
                'selling_price'  => 20000,
                'description'    => 'Dodol legit khas daerah',
            ],
            [
                'name'           => 'Kerupuk Udang Cap Depok',
                'code'           => 'MKN-KRG-005',
                'brand'          => 'Cap Depok',
                'size'           => '500g',
                'unit'           => 'Pack',
                'minimum_stock'  => 15,
                'current_stock'  => 60,
                'purchase_price' => 18000,
                'selling_price'  => 24000,
                'description'    => 'Kerupuk udang untuk pelengkap makan',
            ],
        ];

        // ── C. Makanan Basah Siap Saji ───────────────────────────────
        $makananBasah = [
            [
                'name'           => 'Nasi Box Ayam Goreng',
                'code'           => 'MKN-BSH-001',
                'brand'          => 'Dapur Depok',
                'size'           => '1 porsi',
                'unit'           => 'Box',
                'minimum_stock'  => 5,
                'current_stock'  => 12,
                'purchase_price' => 15000,
                'selling_price'  => 20000,
                'description'    => 'Nasi box siap saji dengan ayam goreng',
            ],
            [
                'name'           => 'Lontong Sayur Betawi',
                'code'           => 'MKN-BSH-002',
                'brand'          => 'Dapur Depok',
                'size'           => '1 porsi',
                'unit'           => 'Pack',
                'minimum_stock'  => 5,
                'current_stock'  => 8,
                'purchase_price' => 12000,
                'selling_price'  => 16000,
                'description'    => 'Lontong sayur khas Betawi area Depok',
            ],
            [
                'name'           => 'Tahu Gejrot Depok',
                'code'           => 'MKN-BSH-003',
                'brand'          => 'UMKM Depok',
                'size'           => '1 porsi',
                'unit'           => 'Pack',
                'minimum_stock'  => 5,
                'current_stock'  => 10,
                'purchase_price' => 8000,
                'selling_price'  => 12000,
                'description'    => 'Tahu gejrot dengan kuah manis pedas',
            ],
            [
                'name'           => 'Es Cendol Dawet Depok',
                'code'           => 'MKN-BSH-004',
                'brand'          => 'Es Depok',
                'size'           => '1 gelas',
                'unit'           => 'Cup',
                'minimum_stock'  => 8,
                'current_stock'  => 20,
                'purchase_price' => 6000,
                'selling_price'  => 10000,
                'description'    => 'Minuman es cendol dawet segar',
            ],
            [
                'name'           => 'Klepon Isi Gula Merah',
                'code'           => 'MKN-BSH-005',
                'brand'          => 'UMKM Depok',
                'size'           => '10 pcs',
                'unit'           => 'Pack',
                'minimum_stock'  => 8,
                'current_stock'  => 18,
                'purchase_price' => 10000,
                'selling_price'  => 14000,
                'description'    => 'Klepon khas jajanan basah tradisional',
            ],
        ];

        foreach (array_merge($frozenFood, $makananKering, $makananBasah) as $product) {
            Product::updateOrCreate(
                ['code' => $product['code']],
                array_merge($product, ['category_id' => $katMakanan->id])
            );
        }

        // ─────────────────────────────────────────────────────────────
        // KATEGORI 2: Fashion (Busana dan Aksesoris)
        // ─────────────────────────────────────────────────────────────
        $katFashion = Category::updateOrCreate(
            ['name' => 'Fashion (Busana dan Aksesoris)'],
            [
                'name'        => 'Fashion (Busana dan Aksesoris)',
                'description' => 'Produk busana dan aksesoris fashion lokal, meliputi busana muslim & hijab, batik khas Depok, dan pakaian kasual.',
            ]
        );

        // ── A. Busana Muslim & Hijab ──────────────────────────────────
        $busanaMuslim = [
            [
                'name'           => 'Hijab Segi Empat Voal Premium',
                'code'           => 'FSH-MSL-001',
                'brand'          => 'Zoya',
                'size'           => null,
                'unit'           => 'Pcs',
                'minimum_stock'  => 10,
                'current_stock'  => 40,
                'purchase_price' => 45000,
                'selling_price'  => 65000,
                'description'    => 'Hijab voal premium warna netral',
            ],
            [
                'name'           => 'Gamis Syari Polos',
                'code'           => 'FSH-MSL-002',
                'brand'          => 'Elzatta',
                'size'           => 'L',
                'unit'           => 'Pcs',
                'minimum_stock'  => 5,
                'current_stock'  => 15,
                'purchase_price' => 180000,
                'selling_price'  => 250000,
                'description'    => 'Gamis syari polos bahan katun',
            ],
            [
                'name'           => 'Mukena Travel Mini',
                'code'           => 'FSH-MSL-003',
                'brand'          => 'Rauna',
                'size'           => null,
                'unit'           => 'Set',
                'minimum_stock'  => 5,
                'current_stock'  => 12,
                'purchase_price' => 120000,
                'selling_price'  => 165000,
                'description'    => 'Mukena travel praktis dengan tas kecil',
            ],
            [
                'name'           => 'Ciput Rajut Premium',
                'code'           => 'FSH-MSL-004',
                'brand'          => 'Hijabers',
                'size'           => null,
                'unit'           => 'Pcs',
                'minimum_stock'  => 15,
                'current_stock'  => 50,
                'purchase_price' => 15000,
                'selling_price'  => 22000,
                'description'    => 'Ciput rajut nyaman dipakai sehari-hari',
            ],
            [
                'name'           => 'Rok Plisket Muslim',
                'code'           => 'FSH-MSL-005',
                'brand'          => 'Kami',
                'size'           => 'M',
                'unit'           => 'Pcs',
                'minimum_stock'  => 8,
                'current_stock'  => 20,
                'purchase_price' => 75000,
                'selling_price'  => 105000,
                'description'    => 'Rok plisket panjang untuk muslimah',
            ],
        ];

        // ── B. Batik Khas Depok ──────────────────────────────────────
        $batik = [
            [
                'name'           => 'Kemeja Batik Megamendung',
                'code'           => 'FSH-BTK-001',
                'brand'          => 'Batik Depok',
                'size'           => 'L',
                'unit'           => 'Pcs',
                'minimum_stock'  => 5,
                'current_stock'  => 10,
                'purchase_price' => 150000,
                'selling_price'  => 210000,
                'description'    => 'Kemeja batik motif megamendung',
            ],
            [
                'name'           => 'Blouse Batik Modern',
                'code'           => 'FSH-BTK-002',
                'brand'          => 'Batik Depok',
                'size'           => 'M',
                'unit'           => 'Pcs',
                'minimum_stock'  => 5,
                'current_stock'  => 12,
                'purchase_price' => 120000,
                'selling_price'  => 175000,
                'description'    => 'Blouse batik modern untuk wanita',
            ],
            [
                'name'           => 'Kain Batik Tulis Depok (2m)',
                'code'           => 'FSH-BTK-003',
                'brand'          => 'Batik Depok',
                'size'           => '2m',
                'unit'           => 'Pcs',
                'minimum_stock'  => 3,
                'current_stock'  => 8,
                'purchase_price' => 350000,
                'selling_price'  => 480000,
                'description'    => 'Kain batik tulis asli motif lokal',
            ],
            [
                'name'           => 'Sarung Batik Pria',
                'code'           => 'FSH-BTK-004',
                'brand'          => 'Batik Depok',
                'size'           => null,
                'unit'           => 'Pcs',
                'minimum_stock'  => 5,
                'current_stock'  => 15,
                'purchase_price' => 85000,
                'selling_price'  => 120000,
                'description'    => 'Sarung batik motif tradisional',
            ],
        ];

        // ── C. Pakaian Kasual ────────────────────────────────────────
        $kasual = [
            [
                'name'           => 'Kaos Polos Cotton Combed',
                'code'           => 'FSH-KSL-001',
                'brand'          => 'Greenlight',
                'size'           => 'L',
                'unit'           => 'Pcs',
                'minimum_stock'  => 10,
                'current_stock'  => 35,
                'purchase_price' => 45000,
                'selling_price'  => 65000,
                'description'    => 'Kaos polos bahan cotton combed 30s',
            ],
            [
                'name'           => 'Celana Chino Pria',
                'code'           => 'FSH-KSL-002',
                'brand'          => 'Cardinal',
                'size'           => '32',
                'unit'           => 'Pcs',
                'minimum_stock'  => 8,
                'current_stock'  => 18,
                'purchase_price' => 130000,
                'selling_price'  => 185000,
                'description'    => 'Celana chino kasual untuk pria',
            ],
            [
                'name'           => 'Hoodie Sweater Basic',
                'code'           => 'FSH-KSL-003',
                'brand'          => 'Erigo',
                'size'           => 'L',
                'unit'           => 'Pcs',
                'minimum_stock'  => 5,
                'current_stock'  => 14,
                'purchase_price' => 110000,
                'selling_price'  => 155000,
                'description'    => 'Hoodie sweater basic unisex',
            ],
            [
                'name'           => 'Tote Bag Kanvas Depok',
                'code'           => 'FSH-KSL-004',
                'brand'          => 'UMKM Depok',
                'size'           => null,
                'unit'           => 'Pcs',
                'minimum_stock'  => 10,
                'current_stock'  => 25,
                'purchase_price' => 35000,
                'selling_price'  => 50000,
                'description'    => 'Tote bag kanvas motif Kota Depok',
            ],
        ];

        foreach (array_merge($busanaMuslim, $batik, $kasual) as $product) {
            Product::updateOrCreate(
                ['code' => $product['code']],
                array_merge($product, ['category_id' => $katFashion->id])
            );
        }

        $this->command->info('✅ Dummy data seeding completed!');
        $this->command->info('   → Kategori: 2 (Makanan & Minuman, Fashion)');
        $this->command->info('   → Produk: 28 (15 makanan, 13 fashion)');
    }
}
