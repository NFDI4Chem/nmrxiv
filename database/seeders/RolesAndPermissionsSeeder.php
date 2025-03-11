<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'view studies']);
        Permission::create(['name' => 'view statistics']);
        Permission::create(['name' => 'manage platform']);
        Permission::create(['name' => 'manage studies']);
        Permission::create(['name' => 'manage roles']);
        Permission::create(['name' => 'register users']);

        // create roles and assign created permissions
        $role = Role::create(['name' => 'developer']);
        $role->givePermissionTo('manage platform');

        $role = Role::create(['name' => 'advisor']);
        $role->givePermissionTo(['view statistics']);

        $role = Role::create(['name' => 'community-curator']);
        $role->givePermissionTo('view studies', 'view statistics');

        $role = Role::create(['name' => 'curator']);
        $role->givePermissionTo('manage studies', 'view statistics');

        $role = Role::create(['name' => 'super-admin']);
        $role->givePermissionTo(Permission::all());

        $role = Role::create(['name' => 'eln']);
        $role->givePermissionTo('register users');
    }
}
