<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Dashboard
            'dashboard.view',

            // Users
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.restore',

            // Roles
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',

            // Permissions
            'permissions.view',
            'permissions.create',
            'permissions.edit',
            'permissions.delete',

            // Settings
            'settings.view',
            'settings.edit',

            // Audit Logs
            'audit.view',
            'audit.clear',

            // Activity Logs
            'activity.view',
            'activity.clear',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Super Admin — all permissions
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        // Manager — users.*, roles.view, dashboard.view
        $manager = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
        $manager->syncPermissions([
            'dashboard.view',
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.restore',
            'roles.view',
        ]);

        // Editor — users.view, dashboard.view
        $editor = Role::firstOrCreate(['name' => 'Editor', 'guard_name' => 'web']);
        $editor->syncPermissions([
            'dashboard.view',
            'users.view',
        ]);

        // User — dashboard.view
        $user = Role::firstOrCreate(['name' => 'User', 'guard_name' => 'web']);
        $user->syncPermissions([
            'dashboard.view',
        ]);
    }
}
