# Users, Roles, and Permissions Module

## Overview

This module implements a complete user management system with roles and permissions using Spatie Laravel Permission package and Filament admin panel.

## Architecture

### Design Patterns
- **Repository Pattern**: Database interactions (`app/Repositories/`)
- **Service Layer**: Business logic (`app/Services/`)
- **DTOs**: Data Transfer Objects (`app/DTOs/`)
- **Form Requests**: Validation (`app/Http/Requests/`)
- **Policies**: Authorization (`app/Policies/`)
- **Events & Listeners**: Event-driven architecture (`app/Events/`, `app/Listeners/`)

### Database Structure

#### Users Table
- `id`, `name`, `email`, `phone`, `password`, `status`, `phone_verified_at`, `created_at`, `updated_at`

#### User Profiles Table
- `id`, `user_id`, `city`, `address`, `avatar`, `created_at`, `updated_at`

#### Admin Actions Logs Table
- `id`, `admin_id`, `action_type`, `target_type`, `target_id`, `notes`, `old_values`, `new_values`, `created_at`, `updated_at`

#### Spatie Permission Tables
- `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`

## Roles

1. **super_admin**: Full system access
2. **admin**: Administrative access with limited permissions
3. **user**: Regular user (default role)
4. **delivery_agent**: Special permissions for delivery operations

## Permissions

### User Management
- `manage_users`, `view_users`, `create_users`, `update_users`, `delete_users`

### Product Management
- `approve_products`, `manage_products`, `view_products`

### Request Management
- `approve_requests`, `manage_requests`, `view_requests`

### Reports
- `view_reports`, `export_reports`

### System
- `manage_roles`, `manage_permissions`, `manage_settings`

## Installation & Setup

### 1. Run Migrations

```bash
php artisan migrate
```

### 2. Seed Roles and Permissions

```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
```

Or seed all (including sample users):

```bash
php artisan db:seed
```

This will create:
- All roles and permissions
- A super admin user (superadmin@khezana.com / password)
- An admin user (admin@khezana.com / password)
- A regular user (user@khezana.com / password)

### 3. Access Filament Admin Panel

Navigate to: `http://your-domain/admin`

Login with super admin credentials:
- Email: `superadmin@khezana.com`
- Password: `password`

## Usage

### User Service

```php
use App\DTOs\UserDTO;
use App\Services\UserService;

$userService = app(UserService::class);

// Create user
$userDTO = UserDTO::fromArray([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'phone' => '+1234567890',
    'password' => 'password123',
    'status' => 'active',
]);

$user = $userService->create($userDTO, null, ['user']);

// Update user
$updatedDTO = UserDTO::fromArray([
    'name' => 'Jane Doe',
    'email' => 'jane@example.com',
]);

$user = $userService->update($userId, $updatedDTO);

// Assign roles
$user = $userService->assignRoles($userId, ['admin', 'delivery_agent']);

// Delete user
$userService->delete($userId);
```

### Policies

```php
// Check permissions
if (auth()->user()->can('create', User::class)) {
    // Create user
}

if (auth()->user()->can('update', $user)) {
    // Update user
}

// Check roles
if (auth()->user()->hasRole('admin')) {
    // Admin only action
}
```

### Admin Action Logging

All admin actions are automatically logged when using UserService or Filament resources.

```php
use App\Services\AdminActionLogService;

$logService = app(AdminActionLogService::class);

// Manual logging
$logService->log(
    adminId: auth()->id(),
    actionType: 'approved',
    targetType: User::class,
    targetId: $userId,
    notes: 'User approved for access'
);
```

## Security Features

1. **Super Admin Protection**: Super admin users cannot be deleted or have their roles modified by non-super admins
2. **Self-Protection**: Users cannot delete themselves
3. **Policy-Based Authorization**: All actions are checked against policies
4. **Action Logging**: All administrative actions are logged
5. **Password Hashing**: Passwords are automatically hashed

## Filament Resources

### UserResource

Located at: `app/Filament/Resources/UserResource.php`

Features:
- Full CRUD operations
- Role management
- Profile management
- Status filtering
- Role filtering
- Search functionality
- Automatic action logging

Access: `/admin/users`

## Events & Listeners

### Events
- `UserCreated`: Fired when a user is created
- `UserUpdated`: Fired when a user is updated
- `UserDeleted`: Fired when a user is deleted

### Listeners
- `LogAdminAction`: Logs all user-related admin actions

## Best Practices

1. **Never hardcode roles**: Always use constants or configuration
2. **Use Policies**: Check permissions using policies, not direct role checks
3. **Use Services**: Always use service layer for business logic
4. **Use DTOs**: Pass data using DTOs for better type safety
5. **Log Actions**: All administrative actions should be logged

## File Structure

```
app/
├── DTOs/
│   ├── UserDTO.php
│   ├── UserProfileDTO.php
│   └── AdminActionLogDTO.php
├── Events/
│   ├── UserCreated.php
│   ├── UserUpdated.php
│   └── UserDeleted.php
├── Filament/Resources/
│   └── UserResource/
│       ├── UserResource.php
│       └── Pages/
│           ├── ListUsers.php
│           ├── CreateUser.php
│           ├── EditUser.php
│           └── ViewUser.php
├── Listeners/
│   └── LogAdminAction.php
├── Models/
│   ├── User.php
│   ├── UserProfile.php
│   └── AdminActionLog.php
├── Policies/
│   ├── UserPolicy.php
│   └── AdminActionLogPolicy.php
├── Repositories/
│   ├── BaseRepository.php
│   ├── UserRepository.php
│   ├── UserProfileRepository.php
│   └── AdminActionLogRepository.php
├── Services/
│   ├── UserService.php
│   ├── RolePermissionService.php
│   └── AdminActionLogService.php
└── Http/Requests/
    ├── StoreUserRequest.php
    ├── UpdateUserRequest.php
    └── UpdateUserProfileRequest.php
```

## Testing

To test the module:

1. Run migrations and seeders
2. Login to Filament admin panel
3. Navigate to Users resource
4. Test CRUD operations
5. Verify role assignments
6. Check admin action logs

## Notes

- All timestamps are automatically managed by Eloquent
- Soft deletes are not implemented (can be added if needed)
- Email verification is handled by Laravel Breeze
- Phone verification structure is prepared but not implemented

## Future Enhancements

- Soft deletes for users
- Email verification integration
- Phone verification system
- Two-factor authentication
- User activity tracking
- API token management
- Audit trail with detailed change tracking
