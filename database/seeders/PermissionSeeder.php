<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Create the initial roles and permissions.
     *
     * @return void
     */
    public function permissionCrud($paramRole, $paramSubject)
    {
        $paramRole->givePermissionTo("$paramSubject.show");
        $paramRole->givePermissionTo("$paramSubject.create");
        $paramRole->givePermissionTo("$paramSubject.update");
        $paramRole->givePermissionTo("$paramSubject.delete");
        $paramRole->givePermissionTo("$paramSubject.self");
        $paramRole->givePermissionTo("$paramSubject.approve");
    }

    public function createPermission($paramsSubject)
    {
        Permission::create(['name' => "$paramsSubject.show"]);
        Permission::create(['name' => "$paramsSubject.create"]);
        Permission::create(['name' => "$paramsSubject.update"]);
        Permission::create(['name' => "$paramsSubject.delete"]);
        Permission::create(['name' => "$paramsSubject.self"]);
        Permission::create(['name' => "$paramsSubject.approve"]);
    }

    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        $this->createPermission('users');
        $this->createPermission('roles');
        $this->createPermission('permissions');
        $this->createPermission('photos');

        // create roles and assign existing permissions
        $role1 = Role::create(['name' => 'superadmin']);
        $this->permissionCrud($role1, 'users');
        $this->permissionCrud($role1, 'roles');
        $this->permissionCrud($role1, 'permissions');
        $this->permissionCrud($role1, 'photos');
        // gets all permissions via Gate::before rule; see AuthServiceProvider

        // create demo users
        $user = User::factory()->create([
            'name' => 'Superadmin Full Name',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('123123'),
        ]);
        $user->assignRole($role1);
        $settings = Setting::factory()->create([
            'key_column' => 'default_role',
            'value_column' => 'superadmin'
        ]);
    }
}
