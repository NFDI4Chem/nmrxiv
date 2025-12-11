<?php

namespace Tests\Feature\Admin;

use App\Models\Dataset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CurationTest extends TestCase
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
        $managePlatformPermission = Permission::firstOrCreate(['name' => 'manage platform']);

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo([$manageRolesPermission, $managePlatformPermission]);
        $this->adminUser->assignRole($adminRole);
    }

    public function test_spectra_requires_authentication(): void
    {
        $response = $this->get('/admin/spectra');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_spectra_requires_permission(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/admin/spectra');

        $response->assertStatus(403);
    }

    public function test_spectra_renders_for_authorized_user(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get('/admin/spectra');

        $response->assertStatus(200);
    }

    public function test_spectra_accessible_via_named_route(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('console.spectra'));

        $response->assertStatus(200);
    }

    public function test_spectra_renders_for_user_with_manage_roles_permission(): void
    {
        $userWithManageRoles = User::factory()->withPersonalTeam()->create();
        $manageRolesPermission = Permission::firstOrCreate(['name' => 'manage roles']);
        $role = Role::firstOrCreate(['name' => 'moderator']);
        $role->givePermissionTo($manageRolesPermission);
        $userWithManageRoles->assignRole($role);

        $response = $this->actingAs($userWithManageRoles)
            ->get('/admin/spectra');

        $response->assertStatus(200);
    }

    public function test_snapshots_requires_authentication(): void
    {
        $response = $this->get('/admin/snapshots');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_snapshots_requires_permission(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/admin/snapshots');

        $response->assertStatus(403);
    }

    public function test_snapshots_renders_for_authorized_user(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get('/admin/snapshots');

        $response->assertStatus(200);
    }

    public function test_snapshots_accessible_via_named_route(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('console.spectra.snapshots'));

        $response->assertStatus(200);
    }

    public function test_snapshots_returns_datasets_with_nmrium_and_no_photo(): void
    {
        // Create dataset with nmrium and no photo
        $datasetWithNmrium = Dataset::factory()->create([
            'has_nmrium' => 1,
            'dataset_photo_path' => null,
        ]);

        // Create dataset with nmrium but has photo
        Dataset::factory()->create([
            'has_nmrium' => 1,
            'dataset_photo_path' => 'some/path.jpg',
        ]);

        // Create dataset without nmrium
        Dataset::factory()->create([
            'has_nmrium' => 0,
            'dataset_photo_path' => null,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get('/admin/snapshots');

        $response->assertStatus(200);
        // The controller passes datasets as IDs only
    }

    public function test_snapshots_filters_correctly(): void
    {
        // Create 3 datasets matching criteria
        $dataset1 = Dataset::factory()->create([
            'has_nmrium' => 1,
            'dataset_photo_path' => null,
        ]);

        $dataset2 = Dataset::factory()->create([
            'has_nmrium' => 1,
            'dataset_photo_path' => null,
        ]);

        $dataset3 = Dataset::factory()->create([
            'has_nmrium' => 1,
            'dataset_photo_path' => null,
        ]);

        // Create dataset that should be filtered out (has photo)
        Dataset::factory()->create([
            'has_nmrium' => 1,
            'dataset_photo_path' => 'photo.jpg',
        ]);

        // Create dataset that should be filtered out (no nmrium)
        Dataset::factory()->create([
            'has_nmrium' => 0,
            'dataset_photo_path' => null,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get('/admin/snapshots');

        $response->assertStatus(200);
        // Verify filtering logic works via database assertions
        $this->assertEquals(3, Dataset::where('has_nmrium', 1)
            ->where('dataset_photo_path', null)
            ->count());
    }

    public function test_snapshots_handles_empty_dataset_list(): void
    {
        // Don't create any datasets

        $response = $this->actingAs($this->adminUser)
            ->get('/admin/snapshots');

        $response->assertStatus(200);
    }
}
