<?php

namespace Tests\Unit\Models;

use App\Models\AppSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_returns_default_when_key_missing(): void
    {
        $this->assertNull(AppSetting::get('missing_key'));
        $this->assertSame('fallback', AppSetting::get('missing_key', 'fallback'));
    }

    public function test_get_returns_stored_value(): void
    {
        AppSetting::create(['key' => 'fonnte_owner_phone', 'value' => '0812345']);

        $this->assertSame('0812345', AppSetting::get('fonnte_owner_phone'));
    }

    public function test_set_creates_then_updates_value(): void
    {
        AppSetting::set('notification_low_stock', '1');
        $this->assertSame('1', AppSetting::get('notification_low_stock'));
        $this->assertDatabaseCount('app_settings', 1);

        AppSetting::set('notification_low_stock', '0');
        $this->assertSame('0', AppSetting::get('notification_low_stock'));
        $this->assertDatabaseCount('app_settings', 1);
    }
}
