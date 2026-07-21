<?php

namespace Tests\Unit\Observers;

use App\Models\AppSetting;
use App\Models\Customer;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OrderObserverTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        User::factory()->create(['id' => 1]);
        Http::fake(['api.fonnte.com/*' => Http::response(['status' => true], 200)]);
        AppSetting::set('fonnte_owner_phone', '08123456789');
    }

    public function test_purchase_order_completion_notifies_owner(): void
    {
        $po = PurchaseOrder::factory()->create(['status' => 'pending']);

        $po->update(['status' => 'completed']);

        Http::assertSent(function (Request $request) {
            return $request['target'] === '08123456789'
                && str_contains($request['message'], 'Barang Diterima');
        });
    }

    public function test_purchase_order_completion_skips_when_notification_disabled(): void
    {
        AppSetting::set('notification_new_purchase', '0');
        $po = PurchaseOrder::factory()->create(['status' => 'pending']);

        $po->update(['status' => 'completed']);

        Http::assertNothingSent();
    }

    public function test_sales_order_completion_notifies_owner_and_customer(): void
    {
        $customer = Customer::factory()->create(['phone' => '08998877665']);
        $so = SalesOrder::factory()->create(['status' => 'pending', 'customer_id' => $customer->id]);

        $so->update(['status' => 'completed']);

        Http::assertSent(fn (Request $r) => $r['target'] === '08123456789'
            && str_contains($r['message'], 'Penjualan Baru'));
        Http::assertSent(fn (Request $r) => $r['target'] === '08998877665'
            && str_contains($r['message'], 'Terima kasih'));
    }

    public function test_sales_order_completion_does_not_message_customer_without_phone(): void
    {
        $customer = Customer::factory()->create(['phone' => null]);
        $so = SalesOrder::factory()->create(['status' => 'pending', 'customer_id' => $customer->id]);

        $so->update(['status' => 'completed']);

        // Owner still notified, but no message to a customer target.
        Http::assertSent(fn (Request $r) => $r['target'] === '08123456789');
        Http::assertNotSent(fn (Request $r) => str_contains($r['message'], 'Terima kasih'));
    }

    public function test_no_notification_when_status_unchanged(): void
    {
        $po = PurchaseOrder::factory()->create(['status' => 'pending']);

        $po->update(['notes' => 'updated notes']);

        Http::assertNothingSent();
    }
}
