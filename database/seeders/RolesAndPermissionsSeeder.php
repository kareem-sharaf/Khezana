<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            // User Management
            'manage_users',
            'view_users',
            'create_users',
            'update_users',
            'delete_users',

            // Product Management
            'approve_products',
            'manage_products',
            'view_products',

            // Request Management
            'approve_requests',
            'manage_requests',
            'view_requests',

            // Reports
            'view_reports',
            'export_reports',

            // System
            'manage_roles',
            'manage_permissions',
            'manage_settings',

            // Categories & Attributes
            'manage_categories',
            'manage_attributes',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create Roles
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $deliveryAgentRole = Role::firstOrCreate(['name' => 'delivery_agent', 'guard_name' => 'web']);

        // Assign all permissions to super_admin
        $superAdminRole->givePermissionTo(Permission::all());

        // Assign permissions to admin
        $adminRole->givePermissionTo([
            'manage_users',
            'view_users',
            'create_users',
            'update_users',
            'delete_users',
            'approve_products',
            'manage_products',
            'view_products',
            'approve_requests',
            'manage_requests',
            'view_requests',
            'view_reports',
            'export_reports',
            'manage_categories',
            'manage_attributes',
        ]);

        // Assign permissions to delivery_agent
        $deliveryAgentRole->givePermissionTo([
            'view_requests',
        ]);

        // User role has no special permissions (default permissions only)
    }
}
