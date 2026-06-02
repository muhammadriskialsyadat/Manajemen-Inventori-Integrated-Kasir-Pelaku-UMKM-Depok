<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use Illuminate\Database\Seeder;

class AppSettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'notification_low_stock'       => '1',
            'notification_new_sale'        => '1',
            'notification_new_purchase'    => '1',
            'notification_purchase_confirm' => '1',
            'fonnte_owner_phone'           => env('FONNTE_OWNER_PHONE', ''),
        ];

        foreach ($defaults as $key => $value) {
            AppSetting::firstOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        $this->command->info('✅ AppSetting defaults seeded.');
    }
}
