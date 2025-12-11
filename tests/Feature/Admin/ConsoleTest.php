<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ConsoleTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create regular user
        $this->user = User::factory()->withPersonalTeam()->create();

        // Create admin user with permissions
        $this->adminUser = User::factory()->withPersonalTeam()->create();

        // Create permissions and role
        $manageRolesPermission = Permission::firstOrCreate(['name' => 'manage roles']);
        $viewStatisticsPermission = Permission::firstOrCreate(['name' => 'view statistics']);
        $managePlatformPermission = Permission::firstOrCreate(['name' => 'manage platform']);

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo([$manageRolesPermission, $viewStatisticsPermission, $managePlatformPermission]);
        $this->adminUser->assignRole($adminRole);
    }

    public function test_console_requires_authentication(): void
    {
        $response = $this->get('/admin/console');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_console_requires_permission(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/admin/console');

        $response->assertStatus(403);
    }

    public function test_console_renders_for_user_with_manage_roles_permission(): void
    {
        $userWithManageRoles = User::factory()->withPersonalTeam()->create();
        $manageRolesPermission = Permission::firstOrCreate(['name' => 'manage roles']);
        $role = Role::firstOrCreate(['name' => 'moderator']);
        $role->givePermissionTo($manageRolesPermission);
        $userWithManageRoles->assignRole($role);

        $response = $this->actingAs($userWithManageRoles)
            ->get('/admin/console');

        $response->assertStatus(200);
    }

    public function test_console_renders_for_user_with_view_statistics_permission(): void
    {
        $userWithViewStats = User::factory()->withPersonalTeam()->create();
        $viewStatsPermission = Permission::firstOrCreate(['name' => 'view statistics']);
        $role = Role::firstOrCreate(['name' => 'analyst']);
        $role->givePermissionTo($viewStatsPermission);
        $userWithViewStats->assignRole($role);

        $response = $this->actingAs($userWithViewStats)
            ->get('/admin/console');

        $response->assertStatus(200);
    }

    public function test_console_renders_for_user_with_manage_platform_permission(): void
    {
        $userWithManagePlatform = User::factory()->withPersonalTeam()->create();
        $managePlatformPermission = Permission::firstOrCreate(['name' => 'manage platform']);
        $role = Role::firstOrCreate(['name' => 'platform_manager']);
        $role->givePermissionTo($managePlatformPermission);
        $userWithManagePlatform->assignRole($role);

        $response = $this->actingAs($userWithManagePlatform)
            ->get('/admin/console');

        $response->assertStatus(200);
    }

    public function test_console_renders_for_admin_user(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get('/admin/console');

        $response->assertStatus(200);
        // Admin console pages may not return standard Inertia responses in tests
    }

    public function test_console_accessible_via_named_route(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('console'));

        $response->assertStatus(200);
    }

    public function test_console_denies_access_to_user_without_any_required_permissions(): void
    {
        $userWithoutPermissions = User::factory()->withPersonalTeam()->create();

        $response = $this->actingAs($userWithoutPermissions)
            ->get('/admin/console');

        $response->assertStatus(403);
    }

    public function test_console_denies_access_to_user_with_different_permission(): void
    {
        $userWithOtherPermission = User::factory()->withPersonalTeam()->create();
        $otherPermission = Permission::firstOrCreate(['name' => 'some other permission']);
        $role = Role::firstOrCreate(['name' => 'other_role']);
        $role->givePermissionTo($otherPermission);
        $userWithOtherPermission->assignRole($role);

        $response = $this->actingAs($userWithOtherPermission)
            ->get('/admin/console');

        $response->assertStatus(403);
    }

    public function test_console_uses_correct_route_prefix(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get('/admin/console');

        $response->assertStatus(200);

        // Verify the route without prefix doesn't work
        $response = $this->actingAs($this->adminUser)
            ->get('/console');

        $response->assertStatus(404);
    }
}
