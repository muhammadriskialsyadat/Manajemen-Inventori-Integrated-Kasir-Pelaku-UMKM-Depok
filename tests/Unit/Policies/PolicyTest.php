<?php

namespace Tests\Unit\Policies;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Models\StockMovement;
use App\Models\User;
use App\Policies\CategoryPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\ProductPolicy;
use App\Policies\PurchaseOrderPolicy;
use App\Policies\SalesOrderPolicy;
use App\Policies\StockMovementPolicy;
use App\Policies\SupplierPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PolicyTest extends TestCase
{
    use RefreshDatabase;

    /** @var array<string, User> */
    private array $users = [];

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['Owner', 'Kasir', 'Gudang', 'Akuntan'] as $role) {
            Role::create(['name' => $role]);
            $user = User::factory()->create();
            $user->assignRole($role);
            $this->users[$role] = $user;
        }
    }

    private function assertAllowedFor(callable $ability, array $allowedRoles): void
    {
        foreach ($this->users as $role => $user) {
            $expected = in_array($role, $allowedRoles, true);
            $this->assertSame(
                $expected,
                $ability($user),
                "Role {$role} expected ".($expected ? 'allowed' : 'denied')
            );
        }
    }

    public function test_product_policy(): void
    {
        $policy = new ProductPolicy;
        $product = Product::factory()->make();

        $this->assertAllowedFor(fn (User $u) => $policy->viewAny($u), ['Owner', 'Gudang', 'Kasir', 'Akuntan']);
        $this->assertAllowedFor(fn (User $u) => $policy->create($u), ['Owner', 'Gudang']);
        $this->assertAllowedFor(fn (User $u) => $policy->update($u, $product), ['Owner', 'Gudang']);
        $this->assertAllowedFor(fn (User $u) => $policy->delete($u, $product), ['Owner', 'Gudang']);
    }

    public function test_category_policy(): void
    {
        $policy = new CategoryPolicy;
        $category = Category::factory()->make();

        $this->assertAllowedFor(fn (User $u) => $policy->viewAny($u), ['Owner', 'Gudang', 'Akuntan']);
        $this->assertAllowedFor(fn (User $u) => $policy->create($u), ['Owner']);
        $this->assertAllowedFor(fn (User $u) => $policy->update($u, $category), ['Owner']);
        $this->assertAllowedFor(fn (User $u) => $policy->delete($u, $category), ['Owner']);
    }

    public function test_supplier_policy(): void
    {
        $policy = new SupplierPolicy;
        $supplier = \App\Models\Supplier::factory()->make();

        $this->assertAllowedFor(fn (User $u) => $policy->viewAny($u), ['Owner', 'Gudang', 'Akuntan']);
        $this->assertAllowedFor(fn (User $u) => $policy->create($u), ['Owner', 'Gudang']);
        $this->assertAllowedFor(fn (User $u) => $policy->delete($u, $supplier), ['Owner', 'Gudang']);
    }

    public function test_customer_policy(): void
    {
        $policy = new CustomerPolicy;
        $customer = Customer::factory()->make();

        $this->assertAllowedFor(fn (User $u) => $policy->viewAny($u), ['Owner', 'Kasir', 'Akuntan']);
        $this->assertAllowedFor(fn (User $u) => $policy->create($u), ['Owner', 'Kasir']);
        $this->assertAllowedFor(fn (User $u) => $policy->delete($u, $customer), ['Owner', 'Kasir']);
    }

    public function test_purchase_order_policy(): void
    {
        $policy = new PurchaseOrderPolicy;
        $po = PurchaseOrder::factory()->make();

        $this->assertAllowedFor(fn (User $u) => $policy->viewAny($u), ['Owner', 'Gudang', 'Akuntan']);
        $this->assertAllowedFor(fn (User $u) => $policy->create($u), ['Owner', 'Gudang']);
        $this->assertAllowedFor(fn (User $u) => $policy->delete($u, $po), ['Owner', 'Gudang']);
    }

    public function test_sales_order_policy(): void
    {
        $policy = new SalesOrderPolicy;
        $so = SalesOrder::factory()->make();

        $this->assertAllowedFor(fn (User $u) => $policy->viewAny($u), ['Owner', 'Kasir', 'Akuntan']);
        $this->assertAllowedFor(fn (User $u) => $policy->create($u), ['Owner', 'Kasir']);
        $this->assertAllowedFor(fn (User $u) => $policy->delete($u, $so), ['Owner', 'Kasir']);
    }

    public function test_stock_movement_policy(): void
    {
        $policy = new StockMovementPolicy;
        $movement = StockMovement::factory()->make();

        $this->assertAllowedFor(fn (User $u) => $policy->viewAny($u), ['Owner', 'Gudang', 'Kasir', 'Akuntan']);
        $this->assertAllowedFor(fn (User $u) => $policy->create($u), ['Owner', 'Gudang']);
        $this->assertAllowedFor(fn (User $u) => $policy->update($u, $movement), ['Owner', 'Gudang']);
        $this->assertAllowedFor(fn (User $u) => $policy->delete($u, $movement), ['Owner']);
    }
}
