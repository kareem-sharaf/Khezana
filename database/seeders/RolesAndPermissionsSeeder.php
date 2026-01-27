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
     * Creates 4 roles: super_admin, admin, seller, user
     * Each role has specific permissions based on responsibilities.
     *
     * Roles are independent and should not be checked with hasRole() in policies.
     * Always use can() to check permissions instead.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ========== Create Permissions ==========

        // User Management Permissions
        $userPermissions = [
            'view_users',
            'create_users',
            'update_users',
            'delete_users',
            'manage_roles', // For managing user roles
        ];

        // Product Management Permissions
        $productPermissions = [
            'view_products',
            'create_products',
            'update_own_products',
            'update_all_products',
            'delete_own_products',
            'delete_all_products',
            'publish_products',
            'approve_products',
            'reject_products',
        ];

        // Request Management Permissions
        $requestPermissions = [
            'view_requests',
            'create_requests',
            'update_own_requests',
            'manage_requests',
            'approve_requests',
            'reject_requests',
        ];

        // Order Management Permissions
        $orderPermissions = [
            'view_own_orders',
            'view_all_orders',
            'update_order_status',
            'cancel_orders',
        ];

        // Offer Management Permissions
        $offerPermissions = [
            'view_offers',
            'create_offers',
            'update_own_offers',
            'cancel_own_offers',
            'respond_to_offers',
        ];

        // System/Admin Permissions
        $systemPermissions = [
            'manage_branches',
            'manage_categories',
            'manage_attributes',
            'manage_permissions',
            'manage_settings',
            'view_reports',
            'export_reports',
        ];

        // Combine all permissions
        $allPermissions = array_merge(
            $userPermissions,
            $productPermissions,
            $requestPermissions,
            $orderPermissions,
            $offerPermissions,
            $systemPermissions
        );

        // Create all permissions
        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // ========== Create Roles ==========

        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $sellerRole = Role::firstOrCreate(['name' => 'seller', 'guard_name' => 'web']);
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        // ========== Assign Permissions to Roles ==========

        // SUPER_ADMIN: All permissions
        $superAdminRole->syncPermissions(Permission::all());

        // ADMIN: Manage users, products, requests, orders, and view reports
        $adminRole->syncPermissions([
            // User Management
            'view_users',
            'create_users',
            'update_users',
            'delete_users',
            'manage_roles',

            // Product Management
            'view_products',
            'approve_products',
            'reject_products',
            'update_all_products',
            'delete_all_products',

            // Request Management
            'view_requests',
            'manage_requests',
            'approve_requests',
            'reject_requests',

            // Order Management
            'view_all_orders',
            'update_order_status',
            'cancel_orders',

            // System
            'manage_branches',
            'manage_categories',
            'manage_attributes',
            'view_reports',
            'export_reports',
        ]);

        // SELLER: Manage own products and view orders
        $sellerRole->syncPermissions([
            // Product Management - own only
            'view_products',
            'create_products',
            'update_own_products',
            'delete_own_products',
            'publish_products',

            // Request Management
            'view_requests',
            'create_requests',

            // Order Management - own only
            'view_own_orders',

            // Offer Management
            'view_offers',
            'create_offers',
            'update_own_offers',
            'cancel_own_offers',
            'respond_to_offers',
        ]);

        // USER: View products, create requests, and manage own orders
        $userRole->syncPermissions([
            // Product Management - view only
            'view_products',

            // Request Management - own only
            'view_requests',
            'create_requests',
            'update_own_requests',

            // Order Management - own only
            'view_own_orders',

            // Offer Management
            'view_offers',
            'respond_to_offers',
        ]);

        $this->command->info('Roles and Permissions seeded successfully.');
        $this->command->table(
            ['Role', 'Permissions Count'],
            [
                ['super_admin', $superAdminRole->permissions()->count()],
                ['admin', $adminRole->permissions()->count()],
                ['seller', $sellerRole->permissions()->count()],
                ['user', $userRole->permissions()->count()],
            ]
        );
    }
}
