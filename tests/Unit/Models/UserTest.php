<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['Owner', 'Kasir', 'Gudang', 'Akuntan'] as $role) {
            Role::create(['name' => $role]);
        }
    }

    private function userWithRole(string $role, bool $active = true): User
    {
        $user = User::factory()->create(['is_active' => $active]);
        $user->assignRole($role);

        return $user;
    }

    public function test_can_adjust_stock_only_for_owner_and_gudang(): void
    {
        $this->assertTrue($this->userWithRole('Owner')->canAdjustStock());
        $this->assertTrue($this->userWithRole('Gudang')->canAdjustStock());
        $this->assertFalse($this->userWithRole('Kasir')->canAdjustStock());
        $this->assertFalse($this->userWithRole('Akuntan')->canAdjustStock());
    }

    public function test_inactive_owner_cannot_adjust_stock(): void
    {
        $this->assertFalse($this->userWithRole('Owner', active: false)->canAdjustStock());
    }

    public function test_can_access_panel_requires_active_and_known_role(): void
    {
        $panel = \Filament\Facades\Filament::getPanel('admin');

        $this->assertTrue($this->userWithRole('Kasir')->canAccessPanel($panel));
        $this->assertFalse($this->userWithRole('Kasir', active: false)->canAccessPanel($panel));

        $roleless = User::factory()->create(['is_active' => true]);
        $this->assertFalse($roleless->canAccessPanel($panel));
    }

    public function test_role_badge_color_maps_role_names(): void
    {
        $this->assertSame('success', $this->userWithRole('Owner')->role_badge_color);
        $this->assertSame('warning', $this->userWithRole('Kasir')->role_badge_color);
        $this->assertSame('info', $this->userWithRole('Gudang')->role_badge_color);
        $this->assertSame('gray', $this->userWithRole('Akuntan')->role_badge_color);
    }

    public function test_role_badge_color_defaults_to_gray_without_role(): void
    {
        $user = User::factory()->create();

        $this->assertSame('gray', $user->role_badge_color);
    }
}
