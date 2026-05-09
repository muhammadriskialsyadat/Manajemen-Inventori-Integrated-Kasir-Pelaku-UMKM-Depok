<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Bersihkan cache permission Spatie
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat 4 role sesuai CLAUDE.md
        $owner   = Role::firstOrCreate(['name' => 'Owner',   'guard_name' => 'web']);
        $kasir   = Role::firstOrCreate(['name' => 'Kasir',   'guard_name' => 'web']);
        $gudang  = Role::firstOrCreate(['name' => 'Gudang',  'guard_name' => 'web']);
        $akuntan = Role::firstOrCreate(['name' => 'Akuntan', 'guard_name' => 'web']);

        // Buat user Owner default (akun pertama untuk akses sistem)
        $ownerUser = User::firstOrCreate(
            ['email' => 'owner@heavenspotindo.my.id'],
            [
                'name'      => 'Owner',
                'password'  => Hash::make('password'),
                'is_active' => true,
            ]
        );
        $ownerUser->syncRoles([$owner]);

        // Buat user Kasir contoh
        $kasirUser = User::firstOrCreate(
            ['email' => 'kasir@heavenspotindo.my.id'],
            [
                'name'      => 'Kasir Demo',
                'password'  => Hash::make('password'),
                'is_active' => true,
            ]
        );
        $kasirUser->syncRoles([$kasir]);

        // Buat user Gudang contoh
        $gudangUser = User::firstOrCreate(
            ['email' => 'gudang@heavenspotindo.my.id'],
            [
                'name'      => 'Gudang Demo',
                'password'  => Hash::make('password'),
                'is_active' => true,
            ]
        );
        $gudangUser->syncRoles([$gudang]);

        // Buat user Akuntan contoh
        $akuntanUser = User::firstOrCreate(
            ['email' => 'akuntan@heavenspotindo.my.id'],
            [
                'name'      => 'Akuntan Demo',
                'password'  => Hash::make('password'),
                'is_active' => true,
            ]
        );
        $akuntanUser->syncRoles([$akuntan]);

        $this->command->info('✅ Roles created: Owner, Kasir, Gudang, Akuntan');
        $this->command->info('👤 owner@heavenspotindo.my.id   → Owner   (password: password)');
        $this->command->info('👤 kasir@heavenspotindo.my.id   → Kasir   (password: password)');
        $this->command->info('👤 gudang@heavenspotindo.my.id  → Gudang  (password: password)');
        $this->command->info('👤 akuntan@heavenspotindo.my.id → Akuntan (password: password)');
    }
}