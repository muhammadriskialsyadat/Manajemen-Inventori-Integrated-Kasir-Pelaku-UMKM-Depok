<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;
use App\Models\Product;

class DummySupplierSeeder extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────────────────────────────
        // SUPPLIER KATEGORI MAKANAN & MINUMAN
        // ─────────────────────────────────────────────────────────────

        // Supplier 1: Frozen Food (Fiesta, Bernardi, Kobe, Champ, Kanzler)
        $supFrozen = Supplier::updateOrCreate(
            ['name' => 'CV. Sumber Frozen Nusantara'],
            [
                'name'           => 'CV. Sumber Frozen Nusantara',
                'contact_person' => 'Bapak Hendra Wijaya',
                'phone'          => '021-7654321',
                'email'          => 'order@sumberfrozen.co.id',
                'address'        => 'Jl. Raya Bogor No. 45, Depok, Jawa Barat',
            ]
        );

        // Supplier 2: Makanan Kering UMKM Lokal (Depok Snack, UMKM Depok, Maeda, Garut, Cap Depok)
        $supKering = Supplier::updateOrCreate(
            ['name' => 'UD. Snack & Oleh-Oleh Depok'],
            [
                'name'           => 'UD. Snack & Oleh-Oleh Depok',
                'contact_person' => 'Ibu Sari Rahayu',
                'phone'          => '0812-9876-5432',
                'email'          => 'sari.snackdepok@gmail.com',
                'address'        => 'Jl. Margonda Raya No. 12, Depok, Jawa Barat',
            ]
        );

        // Supplier 3: Makanan Basah Siap Saji (Dapur Depok, Es Depok, UMKM Depok)
        $supBasah = Supplier::updateOrCreate(
            ['name' => 'Dapur UMKM Kota Depok'],
            [
                'name'           => 'Dapur UMKM Kota Depok',
                'contact_person' => 'Ibu Dewi Susanti',
                'phone'          => '0857-1234-5678',
                'email'          => 'dapur.umkmdepok@gmail.com',
                'address'        => 'Jl. Cinere Raya No. 8, Depok, Jawa Barat',
            ]
        );

        // ─────────────────────────────────────────────────────────────
        // SUPPLIER KATEGORI FASHION
        // ─────────────────────────────────────────────────────────────

        // Supplier 4: Busana Muslim & Hijab (Zoya, Elzatta, Rauna, Hijabers, Kami)
        $supMuslim = Supplier::updateOrCreate(
            ['name' => 'PT. Grosir Busana Muslim Depok'],
            [
                'name'           => 'PT. Grosir Busana Muslim Depok',
                'contact_person' => 'Bapak Ahmad Fauzi',
                'phone'          => '021-8765432',
                'email'          => 'grosir@busanamuslim-depok.com',
                'address'        => 'Jl. Sawangan Raya No. 33, Depok, Jawa Barat',
            ]
        );

        // Supplier 5: Batik Khas Depok (Batik Depok — semua 4 produk)
        $supBatik = Supplier::updateOrCreate(
            ['name' => 'Sanggar Batik Depok'],
            [
                'name'           => 'Sanggar Batik Depok',
                'contact_person' => 'Bapak Wahyu Batik',
                'phone'          => '0813-5555-7777',
                'email'          => 'sanggar.batikdepok@gmail.com',
                'address'        => 'Jl. Kober No. 5, Depok, Jawa Barat',
            ]
        );

        // Supplier 6: Pakaian Kasual (Greenlight, Cardinal, Erigo, UMKM Depok)
        $supKasual = Supplier::updateOrCreate(
            ['name' => 'CV. Distro & Fashion Depok'],
            [
                'name'           => 'CV. Distro & Fashion Depok',
                'contact_person' => 'Bapak Rizky Pratama',
                'phone'          => '0878-3333-9999',
                'email'          => 'distro.fashiondepok@gmail.com',
                'address'        => 'Jl. Beji Timur No. 21, Depok, Jawa Barat',
            ]
        );

        $this->command->info('✅ Supplier dummy berhasil dibuat: 6 supplier');

        // ─────────────────────────────────────────────────────────────
        // SINKRONISASI supplier_id KE PRODUK
        // Pakai updateOrCreate berdasarkan code produk yang sudah ada
        // ─────────────────────────────────────────────────────────────

        // ── Frozen Food → CV. Sumber Frozen Nusantara ────────────────
        $frozenCodes = [
            'MKN-FRZ-001', // Nugget Ayam Crispy (Fiesta)
            'MKN-FRZ-002', // Sosis Ayam Premium (Bernardi)
            'MKN-FRZ-003', // Dimsum Ayam (Kobe)
            'MKN-FRZ-004', // Kentang Goreng Shoestring (Champ)
            'MKN-FRZ-005', // Bakso Ikan Frozen (Kanzler)
        ];

        Product::whereIn('code', $frozenCodes)
            ->update(['supplier_id' => $supFrozen->id]);

        $this->command->info("   → {$supFrozen->name}: " . count($frozenCodes) . ' produk frozen food');

        // ── Makanan Kering → UD. Snack & Oleh-Oleh Depok ────────────
        $keringCodes = [
            'MKN-KRG-001', // Keripik Singkong Pedas (Depok Snack)
            'MKN-KRG-002', // Abon Sapi Homemade (UMKM Depok)
            'MKN-KRG-003', // Kripik Tempe Original (Maeda)
            'MKN-KRG-004', // Dodol Garut Khas Depok (Garut)
            'MKN-KRG-005', // Kerupuk Udang Cap Depok (Cap Depok)
        ];

        Product::whereIn('code', $keringCodes)
            ->update(['supplier_id' => $supKering->id]);

        $this->command->info("   → {$supKering->name}: " . count($keringCodes) . ' produk makanan kering');

        // ── Makanan Basah → Dapur UMKM Kota Depok ───────────────────
        $basahCodes = [
            'MKN-BSH-001', // Nasi Box Ayam Goreng (Dapur Depok)
            'MKN-BSH-002', // Lontong Sayur Betawi (Dapur Depok)
            'MKN-BSH-003', // Tahu Gejrot Depok (UMKM Depok)
            'MKN-BSH-004', // Es Cendol Dawet Depok (Es Depok)
            'MKN-BSH-005', // Klepon Isi Gula Merah (UMKM Depok)
        ];

        Product::whereIn('code', $basahCodes)
            ->update(['supplier_id' => $supBasah->id]);

        $this->command->info("   → {$supBasah->name}: " . count($basahCodes) . ' produk makanan basah');

        // ── Busana Muslim & Hijab → PT. Grosir Busana Muslim Depok ──
        $muslimCodes = [
            'FSH-MSL-001', // Hijab Segi Empat Voal Premium (Zoya)
            'FSH-MSL-002', // Gamis Syari Polos (Elzatta)
            'FSH-MSL-003', // Mukena Travel Mini (Rauna)
            'FSH-MSL-004', // Ciput Rajut Premium (Hijabers)
            'FSH-MSL-005', // Rok Plisket Muslim (Kami)
        ];

        Product::whereIn('code', $muslimCodes)
            ->update(['supplier_id' => $supMuslim->id]);

        $this->command->info("   → {$supMuslim->name}: " . count($muslimCodes) . ' produk busana muslim');

        // ── Batik Khas Depok → Sanggar Batik Depok ───────────────────
        $batikCodes = [
            'FSH-BTK-001', // Kemeja Batik Megamendung
            'FSH-BTK-002', // Blouse Batik Modern
            'FSH-BTK-003', // Kain Batik Tulis Depok
            'FSH-BTK-004', // Sarung Batik Pria
        ];

        Product::whereIn('code', $batikCodes)
            ->update(['supplier_id' => $supBatik->id]);

        $this->command->info("   → {$supBatik->name}: " . count($batikCodes) . ' produk batik');

        // ── Pakaian Kasual → CV. Distro & Fashion Depok ──────────────
        $kasualCodes = [
            'FSH-KSL-001', // Kaos Polos Cotton Combed (Greenlight)
            'FSH-KSL-002', // Celana Chino Pria (Cardinal)
            'FSH-KSL-003', // Hoodie Sweater Basic (Erigo)
            'FSH-KSL-004', // Tote Bag Kanvas Depok (UMKM Depok)
        ];

        Product::whereIn('code', $kasualCodes)
            ->update(['supplier_id' => $supKasual->id]);

        $this->command->info("   → {$supKasual->name}: " . count($kasualCodes) . ' produk kasual');

        // ─────────────────────────────────────────────────────────────
        // RINGKASAN
        // ─────────────────────────────────────────────────────────────
        $totalSynced = Product::whereNotNull('supplier_id')
            ->whereIn('code', array_merge(
                $frozenCodes, $keringCodes, $basahCodes,
                $muslimCodes, $batikCodes, $kasualCodes
            ))
            ->count();

        $this->command->info('');
        $this->command->info("✅ Sinkronisasi selesai: {$totalSynced}/28 produk sudah punya supplier default");
        $this->command->line('');
        $this->command->table(
            ['Supplier', 'Kategori Produk', 'Jumlah Produk'],
            [
                [$supFrozen->name,  'Frozen Food',          count($frozenCodes)],
                [$supKering->name,  'Makanan Kering',       count($keringCodes)],
                [$supBasah->name,   'Makanan Basah',        count($basahCodes)],
                [$supMuslim->name,  'Busana Muslim & Hijab',count($muslimCodes)],
                [$supBatik->name,   'Batik Khas Depok',     count($batikCodes)],
                [$supKasual->name,  'Pakaian Kasual',       count($kasualCodes)],
            ]
        );
    }
}
