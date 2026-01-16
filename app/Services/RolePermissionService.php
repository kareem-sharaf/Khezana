<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionService extends BaseService
{
    /**
     * Create a new role.
     */
    public function createRole(string $name, ?string $guardName = 'web'): Role
    {
        return Role::create(['name' => $name, 'guard_name' => $guardName]);
    }

    /**
     * Create a new permission.
     */
    public function createPermission(string $name, ?string $guardName = 'web'): Permission
    {
        return Permission::create(['name' => $name, 'guard_name' => $guardName]);
    }

    /**
     * Assign permissions to role.
     */
    public function assignPermissionsToRole(string $roleName, array $permissionNames): Role
    {
        $role = Role::findByName($roleName);

        if (!$role) {
            throw new \Exception("Role not found: {$roleName}");
        }

        $permissions = Permission::whereIn('name', $permissionNames)->get();
        $role->syncPermissions($permissions);

        return $role->load('permissions');
    }

    /**
     * Get all roles.
     */
    public function getAllRoles()
    {
        return Role::with('permissions')->get();
    }

    /**
     * Get all permissions.
     */
    public function getAllPermissions()
    {
        return Permission::all();
    }

    /**
     * Get user roles and permissions.
     */
    public function getUserRolesAndPermissions(User $user): array
    {
        return [
            'roles' => $user->roles,
            'permissions' => $user->getAllPermissions(),
        ];
    }
}
