<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCatalogSeeder extends Seeder
{
    public function run(): void
    {
        // ── Step 1: Truncate (FK checks disabled so we only touch these 4 tables) ──
        $this->command->info('Truncating existing data...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('products')->truncate();
        DB::table('categories')->truncate();
        DB::table('suppliers')->truncate();
        DB::table('customers')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        $this->command->info('Done — categories, products, suppliers, customers cleared.');

        $now = now();

        // ── Step 2: Suppliers ──────────────────────────────────────────────────────
        $this->command->info('Seeding suppliers...');

        $ditonKingId = DB::table('suppliers')->insertGetId([
            'name'           => 'Diton King',
            'contact_person' => '-',
            'phone'          => '-',
            'email'          => null,
            'address'        => null,
            'created_at'     => $now,
            'updated_at'     => $now,
        ]);

        $titansId = DB::table('suppliers')->insertGetId([
            'name'           => 'Titans Paint',
            'contact_person' => '-',
            'phone'          => '-',
            'email'          => null,
            'address'        => null,
            'created_at'     => $now,
            'updated_at'     => $now,
        ]);

        $this->command->info('Suppliers seeded: 2');

        // ── Step 3: Categories ────────────────────────────────────────────────────
        $this->command->info('Seeding categories...');

        $catOriginal = DB::table('categories')->insertGetId([
            'name' => 'Diton King - The Original 400ml',
            'created_at' => $now, 'updated_at' => $now,
        ]);
        $cat300 = DB::table('categories')->insertGetId([
            'name' => 'Diton King - ThreeHundred 300ml',
            'created_at' => $now, 'updated_at' => $now,
        ]);
        $cat150 = DB::table('categories')->insertGetId([
            'name' => 'Diton King - Pusaka 150ml',
            'created_at' => $now, 'updated_at' => $now,
        ]);
        DB::table('categories')->insert([
            'name' => 'Diton King - Garuda 750ml',
            'created_at' => $now, 'updated_at' => $now,
        ]);
        DB::table('categories')->insert([
            'name' => 'Diton King - Nusantara 600ml',
            'created_at' => $now, 'updated_at' => $now,
        ]);
        DB::table('categories')->insert([
            'name' => 'Diton King - Varnish 400ml',
            'created_at' => $now, 'updated_at' => $now,
        ]);
        DB::table('categories')->insert([
            'name' => 'Diton King - Wall Blocking Paint 4kg',
            'created_at' => $now, 'updated_at' => $now,
        ]);
        $catTitans = DB::table('categories')->insertGetId([
            'name' => 'Titans Paint 400ml',
            'created_at' => $now, 'updated_at' => $now,
        ]);

        $this->command->info('Categories seeded: 8 (4 empty, 4 with products)');

        // ── Step 4: Products ──────────────────────────────────────────────────────
        $this->command->info('Seeding products...');

        // ── Diton King – The Original 400ml (76 products) ────────────────────────
        $original400 = [
            ['DK320', 'KHATULISTIWA'],
            ['DK321', 'DRACULA RED'],
            ['DK319', 'LAVA ORANGE'],
            ['DK374', 'NEMESIS'],
            ['DK322', 'REDWINE'],
            ['DK318', 'FINEST ORANGE'],
            ['DK317', 'MATAHARI'],
            ['DK334', 'DRAGON YELLOW'],
            ['DK359', 'TIGER YELLOW'],
            ['DK333', 'LEMON YELLOW'],
            ['DK360', 'MONK YELLOW'],
            ['DK372', 'STARSEED'],
            ['DK316', 'MERAUKE'],
            ['DK315', 'BAJAWA'],
            ['DK375', 'BATA'],
            ['DK373', 'MAHARAJA'],
            ['DK370', 'BOROBUDUR'],
            ['DK314', 'BHUMI'],
            ['DK313', 'MUSI'],
            ['DK312', 'ARJUNA'],
            ['DK311', 'SKIN TONE'],
            ['DK369', 'MOCHA'],
            ['DK368', 'BAMBOO'],
            ['DK350', 'RAJA AMPAT'],
            ['DK357', 'JAMBI'],
            ['DK332', 'ZAMRUD'],
            ['DK361', 'KOMODO'],
            ['DK356', 'MOSS GREEN'],
            ['DK354', 'BALIEM'],
            ['DK355', 'BORNEO'],
            ['DK331', 'SABANG'],
            ['DK330', 'GREEN TEA'],
            ['DK329', 'BALI LIME'],
            ['DK328', 'TROPICAL YELLOW'],
            ['DK362', 'SMOKE GREY'],
            ['DK363', 'RAS GREY'],
            ['DK365', 'TORAJA GREY'],
            ['DK364', 'MAROS GREY'],
            ['DK309', 'BLACK KING'],
            ['DK310', 'WHITE KING'],
            ['DK302', 'TRANSPARANT BLACK'],
            ['DK301', 'TRANSPARANT WHITE'],
            ['DK304', 'RICH GOLD'],
            ['DK303', 'SILVER'],
            ['DK300', 'CAPS CLEANER'],
            ['DK344', 'BHATARA BLUE'],
            ['DK342', 'MONSTER BLUE'],
            ['DK343', 'JAVA BLUE'],
            ['DK366', 'ARAFURU'],
            ['DK367', 'TELAGA'],
            ['DK371', 'BELITUNG'],
            ['DK348', 'TOSCA'],
            ['DK347', 'BUNAKEN'],
            ['DK340', 'REMBULAN'],
            ['DK341', 'ULUWATU'],
            ['DK349', 'ALOR BLUE'],
            ['DK323', 'BABY PINK'],
            ['DK345', 'BIRU MALAM'],
            ['DK346', 'PALUNG'],
            ['DK339', 'NEGRO VIOLET'],
            ['DK326', 'CENDRAWASIH'],
            ['DK338', 'ULTRA VIOLET'],
            ['DK337', 'PURPLE ANGLE'],
            ['DK336', 'LAVENDER'],
            ['DK325', 'NEON PINK'],
            ['DK353', 'GERHANA PINK'],
            ['DK352', 'MAGENTA'],
            ['DK324', 'PIA PINK'],
            ['DK358', 'PADI'],
            ['DK4005', 'FLUOR RED'],
            ['DK4003', 'FLUOR ORANGE'],
            ['DK4007', 'FLUOR BLUE'],
            ['DK4004', 'FLUOR YELLOW'],
            ['DK4006', 'FLUOR VIOLET'],
            ['DK4002', 'FLUOR GREEN'],
            ['DK4001', 'FLUOR PINK'],
        ];

        DB::table('products')->insert(
            $this->makeRows($original400, $catOriginal, 'Diton King', '400ml', $now)
        );
        $this->command->info('  ✓ The Original 400ml: ' . count($original400) . ' products');

        // ── Diton King – ThreeHundred 300ml (39 products) ────────────────────────
        $threehundred = [
            ['DK03', 'MERAPI'],
            ['DK05', 'GALAXY'],
            ['DK26', 'CORAL PINK'],
            ['DK35', 'GULALI'],
            ['DK36', 'ROMANSA'],
            ['DK37', 'SALEM'],
            ['DK23', 'TANGERINE'],
            ['DK12', 'BANANA YELLOW'],
            ['DK11', 'CANARY YELLOW'],
            ['DK13', 'SATIN YELLOW'],
            ['DK28', 'BLUE SAPHIRE'],
            ['DK09', 'INDIGO BLUE'],
            ['DK10', 'BALI BLUE'],
            ['DK29', 'HIMALAYAN'],
            ['DK31', 'NILA'],
            ['DK32', 'KALDERA'],
            ['DK33', 'UBUD'],
            ['DK16', 'RUMPUT'],
            ['DK27', 'SUMATERA GREEN'],
            ['DK17', 'PANDAN'],
            ['DK30', 'JOKER BLUE'],
            ['DK15', 'TOBA'],
            ['DK14', 'KARIMUN'],
            ['DK25', 'MAHADEWA'],
            ['DK08', 'ATLANTIS'],
            ['DK07', 'LEMBAYUNG'],
            ['DK06', 'LADY PINK'],
            ['DK04', 'MANGGIS'],
            ['DK34', 'MANDALA'],
            ['DK20', 'BROMO'],
            ['DK21', 'BATANG HARI'],
            ['DK24', 'CENDANA'],
            ['DK22', 'JATI'],
            ['DK19', 'ARCA GREY'],
            ['DK18', 'ROCK GREY'],
            ['DK01', 'DITON WHITE'],
            ['DK02', 'DITON BLACK'],
            ['DK38', 'EMAS'],
            ['DK39', 'PERAK'],
        ];

        DB::table('products')->insert(
            $this->makeRows($threehundred, $cat300, 'Diton King', '300ml', $now)
        );
        $this->command->info('  ✓ ThreeHundred 300ml: ' . count($threehundred) . ' products');

        // ── Diton King – Pusaka 150ml (20 products) ──────────────────────────────
        // Note: DK118B is a temporary code for GILI (source had DK118 duplicated)
        $pusaka = [
            ['DK110',  'MERAH PUSAKA'],
            ['DK108',  'RAJAH'],
            ['DK111',  'SENJA'],
            ['DK109',  'MERAH JAMBU'],
            ['DK106',  'TAMBORA'],
            ['DK107',  'SULTAN'],
            ['DK117',  'BANDA'],
            ['DK118',  'SAMUDERA'],
            ['DK119',  'HALMAHERA'],
            ['DK118B', 'GILI'],
            ['DK116',  'BATUR'],
            ['DK115',  'KELIMUTU'],
            ['DK114',  'NATUNA'],
            ['DK104',  'ASMAT'],
            ['DK112',  'KUNING RAYA'],
            ['DK113',  'PANEN'],
            ['DK105',  'KRAKATAU'],
            ['DK102',  'PUTIH PUSAKA'],
            ['DK101',  'PERAK'],
            ['DK103',  'HITAM PUSAKA'],
        ];

        DB::table('products')->insert(
            $this->makeRows($pusaka, $cat150, 'Diton King', '150ml', $now)
        );
        $this->command->info('  ✓ Pusaka 150ml: ' . count($pusaka) . ' products  [DK118B=GILI is a temp code]');

        // ── Titans Paint 400ml (120 products) ────────────────────────────────────
        $titans = [
            ['TS-001', 'BONE YELLOW'],
            ['TS-002', 'CREAM YELLOW'],
            ['TS-003', 'PALE YELLOW'],
            ['TS-004', 'TUSCAN YELLOW'],
            ['TS-005', 'GILT YELLOW'],
            ['TS-006', 'POWDER YELLOW'],
            ['TS-008', 'CHEERS'],
            ['TS-009', 'TITANS YELLOW'],
            ['TS-010', 'FLAK YELLOW'],
            ['TS-011', 'SHRIMP'],
            ['TS-012', 'NEVERTOOLAVISH'],
            ['TS-013', 'PAPAYA'],
            ['TS-014', 'LAVA RED'],
            ['TS-016', 'SALMON'],
            ['TS-017', 'APRICOT'],
            ['TS-018', 'WALSPARTY'],
            ['TS-019', 'TERRACOTA'],
            ['TS-020', 'LORD MIEL'],
            ['TS-021', 'LIGHT RED'],
            ['TS-022', 'REAL DASKER'],
            ['TS-023', 'MATADOR'],
            ['TS-024', 'SCARLET WITCH'],
            ['TS-025', 'VAMPIRE RED'],
            ['TS-027', 'REDBERRY'],
            ['TS-028', 'SLAYER'],
            ['TS-029', 'TECHOO BLOODSHOT'],
            ['TS-030', 'DARK ZANY'],
            ['TS-031', 'PETAL PINK'],
            ['TS-032', 'ROSE PINK'],
            ['TS-033', 'BUBBLEGUM'],
            ['TS-034', 'WISHKISS'],
            ['TS-035', 'HARDBERRY'],
            ['TS-036', 'PINK PANTHER'],
            ['TS-037', 'PINK IS PUNK'],
            ['TS-038', 'MAGICIAN PINK'],
            ['TS-039', 'ROSEFVN PINK'],
            ['TS-040', 'SHADOW PINK'],
            ['TS-042', 'PERSIA'],
            ['TS-043', 'WILDBERRY'],
            ['TS-044', 'THUNDERBOLT'],
            ['TS-045', 'RUBYCORD'],
            ['TS-046', 'MYSTERIO'],
            ['TS-047', 'CRONUS'],
            ['TS-048', 'DEJAVU'],
            ['TS-049', 'GLOOMBERRY'],
            ['TS-050', 'NECRO'],
            ['TS-051', 'MANTRA'],
            ['TS-052', 'PETAL VIOLET'],
            ['TS-053', 'ASTER'],
            ['TS-054', 'ASTRAL'],
            ['TS-055', 'VIOLET GEMS'],
            ['TS-056', 'VIOLETTA'],
            ['TS-057', 'COSMOS BLUE'],
            ['TS-058', 'ASTROPUNX'],
            ['TS-059', 'NEBSTERISM'],
            ['TS-060', 'STOKE NIGHTMARE'],
            ['TS-061', 'LEGACY BLUE'],
            ['TS-062', 'ATOMIZER'],
            ['TS-063', 'DEOVBLUE'],
            ['TS-064', 'POSEIDON BLUE'],
            ['TS-065', 'LABOON'],
            ['TS-066', 'PAPI DENIM'],
            ['TS-067', 'ICARUS'],
            ['TS-068', 'GLACIER'],
            ['TS-069', 'ARASY'],
            ['TS-070', 'LEVIATHAN'],
            ['TS-071', 'STORMY'],
            ['TS-072', 'HOUDINI'],
            ['TS-073', 'ICE WALLS'],
            ['TS-074', 'MARIANA'],
            ['TS-075', 'ICE MENTHOL'],
            ['TS-076', 'LUCID RHEIN'],
            ['TS-077', 'TUTSY'],
            ['TS-078', 'TEAL OCEAN'],
            ['TS-080', 'NIHILIST'],
            ['TS-081', 'ICEBERG'],
            ['TS-083', 'SEAFOAM'],
            ['TS-084', 'CACTUS'],
            ['TS-086', 'NITRO'],
            ['TS-087', 'REPTILE'],
            ['TS-088', 'DOOMSDAY'],
            ['TS-089', 'VILLAINS'],
            ['TS-090', 'VENOM GREEN'],
            ['TS-091', 'JUNGLE GREEN'],
            ['TS-092', 'REGGAE GREEN'],
            ['TS-093', 'SEAWEED'],
            ['TS-094', 'SACRAMENTO'],
            ['TS-095', 'VOODOO'],
            ['TS-096', 'LIME GREEN'],
            ['TS-097', 'AVOCADO'],
            ['TS-098', 'WASABI'],
            ['TS-099', 'HONEY'],
            ['TS-100', 'VIPER'],
            ['TS-101', 'ANACONDA'],
            ['TS-103', 'HORNET'],
            ['TS-104', 'BATTLEFIELD'],
            ['TS-105', 'MUSTARD'],
            ['TS-106', 'MEDALION'],
            ['TS-107', 'SOLDIER'],
            ['TS-108', 'BISQUE'],
            ['TS-109', 'PEANUT'],
            ['TS-132', 'PINOKIO'],
            ['TS-110', 'TAN'],
            ['TS-111', 'STRANGERS'],
            ['TS-112', 'MARS'],
            ['TS-113', 'BRIGHT COCOA'],
            ['TS-114', 'COCOA'],
            ['TS-115', 'BURNT UMBER'],
            ['TS-116', 'AREN SUGAR'],
            ['TS-117', 'DARK CHOCO'],
            ['TS-118', 'DARBOTZ BLACK'],
            ['TS-119', 'TOP GREY'],
            ['TS-120', 'ASHPALT'],
            ['TS-121', 'GLOOM GREY'],
            ['TS-122', 'STONE GREY'],
            ['TS-123', 'MISTY GREY'],
            ['TS-124', 'KONG GREY'],
            ['TS-125', 'MONSTER WHITE'],
            ['TS-130', 'EZ BLACK'],
            ['TS-131', 'TIPSY WHITE'],
        ];

        DB::table('products')->insert(
            $this->makeRows($titans, $catTitans, 'Titans Paint', '400ml', $now)
        );
        $this->command->info('  ✓ Titans Paint 400ml: ' . count($titans) . ' products');

        $total = count($original400) + count($threehundred) + count($pusaka) + count($titans);
        $this->command->newLine();
        $this->command->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->command->info("Total products seeded : {$total}");
        $this->command->info("Suppliers seeded      : 2");
        $this->command->info("Categories seeded     : 8 (4 active, 4 empty)");
        $this->command->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
    }

    private function makeRows(array $items, int $categoryId, string $brand, string $size, mixed $now): array
    {
        return array_map(fn($item) => [
            'code'           => $item[0],
            'name'           => $item[1],
            'category_id'    => $categoryId,
            'brand'          => $brand,
            'size'           => $size,
            'unit'           => 'kaleng',
            'current_stock'  => 0,
            'minimum_stock'  => 5,
            'purchase_price' => 0,
            'selling_price'  => 0,
            'description'    => null,
            'created_at'     => $now,
            'updated_at'     => $now,
        ], $items);
    }
}