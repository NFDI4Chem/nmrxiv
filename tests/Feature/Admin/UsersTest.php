<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UsersTest extends TestCase
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

    public function test_index_requires_authentication(): void
    {
        $response = $this->get('/admin/users');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_index_requires_permission(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/admin/users');

        $response->assertStatus(403);
    }

    public function test_index_renders_for_authorized_user(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get('/admin/users');

        $response->assertStatus(200);
    }

    public function test_index_accessible_via_named_route(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('console.users'));

        $response->assertStatus(200);
    }

    public function test_create_requires_authentication(): void
    {
        $response = $this->get('/admin/users/create');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_create_requires_permission(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/admin/users/create');

        $response->assertStatus(403);
    }

    public function test_create_renders_for_authorized_user(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get('/admin/users/create');

        $response->assertStatus(200);
    }

    public function test_create_accessible_via_named_route(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('console.users.create'));

        $response->assertStatus(200);
    }

    public function test_store_requires_authentication(): void
    {
        $response = $this->post('/admin/users', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_store_requires_permission(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/admin/users', [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $response->assertStatus(403);
    }

    public function test_store_creates_new_user(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post('/admin/users', [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john@example.com',
                'username' => 'johndoe',
                'orcid_id' => '0000-0001-2345-6789',
                'affiliation' => 'Test University',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'terms' => true,
            ]);

        $response->assertRedirect(route('console.users'));
        $response->assertSessionHas('success', 'User created successfully');

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);
    }

    public function test_edit_requires_authentication(): void
    {
        $testUser = User::factory()->create();

        $response = $this->get("/admin/users/edit/{$testUser->id}");

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_edit_requires_permission(): void
    {
        $testUser = User::factory()->create();

        $response = $this->actingAs($this->user)
            ->get("/admin/users/edit/{$testUser->id}");

        $response->assertStatus(403);
    }

    public function test_edit_renders_for_authorized_user(): void
    {
        $testUser = User::factory()->create();

        $response = $this->actingAs($this->adminUser)
            ->get("/admin/users/edit/{$testUser->id}");

        $response->assertStatus(200);
    }

    public function test_edit_accessible_via_named_route(): void
    {
        $testUser = User::factory()->create();

        $response = $this->actingAs($this->adminUser)
            ->get(route('console.users.edit', $testUser));

        $response->assertStatus(200);
    }

    public function test_update_requires_authentication(): void
    {
        $testUser = User::factory()->create();

        $response = $this->put("/admin/users/edit/{$testUser->id}", [
            'first_name' => 'Updated',
            'last_name' => 'Name',
            'email' => $testUser->email,
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_update_requires_permission(): void
    {
        $testUser = User::factory()->create();

        $response = $this->actingAs($this->user)
            ->put("/admin/users/edit/{$testUser->id}", [
                'first_name' => 'Updated',
                'last_name' => 'Name',
                'email' => $testUser->email,
            ]);

        $response->assertStatus(403);
    }

    public function test_update_updates_user_information(): void
    {
        $testUser = User::factory()->create([
            'first_name' => 'Original',
            'last_name' => 'Name',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->put("/admin/users/edit/{$testUser->id}", [
                'first_name' => 'Updated',
                'last_name' => 'NewName',
                'email' => $testUser->email,
                'username' => $testUser->username,
                'orcid_id' => $testUser->orcid_id ?? '',
                'affiliation' => $testUser->affiliation ?? '',
            ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $testUser->id,
            'first_name' => 'Updated',
            'last_name' => 'NewName',
        ]);
    }

    public function test_update_password_requires_authentication(): void
    {
        $testUser = User::factory()->create();

        $response = $this->put("/admin/users/edit/{$testUser->id}/password", [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_update_password_requires_permission(): void
    {
        $testUser = User::factory()->create();

        $response = $this->actingAs($this->user)
            ->put("/admin/users/edit/{$testUser->id}/password", [
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ]);

        $response->assertStatus(403);
    }

    public function test_update_password_updates_user_password(): void
    {
        $testUser = User::factory()->create([
            'password' => Hash::make('oldpassword'),
        ]);

        $response = $this->actingAs($this->adminUser)
            ->put("/admin/users/edit/{$testUser->id}/password", [
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ]);

        $response->assertSessionHas('success');

        $testUser->refresh();
        $this->assertTrue(Hash::check('newpassword123', $testUser->password));
    }

    public function test_update_password_validates_password_confirmation(): void
    {
        $testUser = User::factory()->create();

        $response = $this->actingAs($this->adminUser)
            ->put("/admin/users/edit/{$testUser->id}/password", [
                'password' => 'newpassword123',
                'password_confirmation' => 'differentpassword',
            ]);

        $response->assertSessionHasErrors();
    }

    public function test_update_role_requires_authentication(): void
    {
        $testUser = User::factory()->create();
        $role = Role::firstOrCreate(['name' => 'editor']);

        $response = $this->put("/admin/users/edit/{$testUser->id}/role", [
            'role' => $role->name,
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_update_role_requires_manage_roles_permission(): void
    {
        $testUser = User::factory()->create();
        $role = Role::firstOrCreate(['name' => 'editor']);

        $response = $this->actingAs($this->user)
            ->put("/admin/users/edit/{$testUser->id}/role", [
                'role' => $role->name,
            ]);

        $response->assertStatus(403);
    }

    public function test_update_role_updates_user_role(): void
    {
        $testUser = User::factory()->create();
        $role = Role::firstOrCreate(['name' => 'editor']);

        $response = $this->actingAs($this->adminUser)
            ->put("/admin/users/edit/{$testUser->id}/role", [
                'role' => $role->name,
            ]);

        $response->assertSessionHas('success');

        $this->assertTrue($testUser->fresh()->hasRole('editor'));
    }

    public function test_update_role_prevents_updating_own_role(): void
    {
        $role = Role::firstOrCreate(['name' => 'editor']);

        $response = $this->actingAs($this->adminUser)
            ->put("/admin/users/edit/{$this->adminUser->id}/role", [
                'role' => $role->name,
            ]);

        $response->assertSessionHasErrors(['error_message']);
    }

    public function test_destroy_photo_requires_authentication(): void
    {
        $testUser = User::factory()->create();

        $response = $this->delete("/admin/users/edit/{$testUser->id}/photo");

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_destroy_photo_requires_permission(): void
    {
        $testUser = User::factory()->create();

        $response = $this->actingAs($this->user)
            ->delete("/admin/users/edit/{$testUser->id}/photo");

        $response->assertStatus(403);
    }

    public function test_impersonate_requires_authentication(): void
    {
        $testUser = User::factory()->create();

        $response = $this->get("/admin/users/impersonate/{$testUser->id}");

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_impersonate_requires_permission(): void
    {
        $testUser = User::factory()->create();

        $response = $this->actingAs($this->user)
            ->get("/admin/users/impersonate/{$testUser->id}");

        $response->assertStatus(403);
    }

    public function test_impersonate_redirects_to_impersonate_route(): void
    {
        $testUser = User::factory()->create();

        $response = $this->actingAs($this->adminUser)
            ->get("/admin/users/impersonate/{$testUser->id}");

        $response->assertRedirect(route('impersonate', $testUser->id));
    }

    public function test_check_password_returns_true_when_user_has_password(): void
    {
        $userWithPassword = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($userWithPassword)
            ->get('/auth/checkPassword');

        $response->assertStatus(200);
        $response->assertJson(['hasPassword' => true]);
    }

    public function test_check_password_returns_false_when_user_has_no_password(): void
    {
        $userWithoutPassword = User::factory()->create([
            'password' => null,
        ]);

        $response = $this->actingAs($userWithoutPassword)
            ->get('/auth/checkPassword');

        $response->assertStatus(200);
        $response->assertJson(['hasPassword' => false]);
    }
}
