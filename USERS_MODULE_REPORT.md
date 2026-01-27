# Comprehensive Users Module Report

**Report Date:** January 27, 2026  
**Project:** Khezana - Goods Exchange System  
**Version:** 2.0.0 - Roles & Permissions Refactoring

🎉 **Major Update:** System migrated from `user_type` enum to Spatie Permission

---

## 📋 Table of Contents

1. [Overview](#overview)
2. [Architecture](#architecture)
3. [Database](#database)
4. [Models](#models)
5. [DTOs](#dtos)
6. [Services](#services)
7. [Repositories](#repositories)
8. [Controllers](#controllers)
9. [Filament Resources](#filament-resources)
10. [Policies](#policies)
11. [Events](#events)
12. [Migrations](#migrations)
13. [Routes](#routes)
14. [Statistics](#statistics)

---

## 🎯 Overview

**Module:** User Management System

**Description:**  
Complete system for managing user data in Khezana. Provides all functions for creating, updating, and deleting users with full support for roles, permissions, and branch management.

**Key Features:**
- ✅ Create, update, delete users
- ✅ Role System (Spatie Permission): super_admin, admin, seller, user
- ✅ 26 fine-grained permissions across 6 categories
- ✅ **Single role per user** (prevents conflicts)
- ✅ branch_id automatically tied to role (seller only)
- ✅ Optimized Policies (using can())
- ✅ Advanced Filament interface
- ✅ Complete authentication system

**Technologies Used:**
- Laravel 11
- Filament 3 (admin panel)
- Spatie Permission (role/permission management)
- Eloquent ORM
- Pattern: Service + Repository

---

## 🏗️ Architecture

### Hierarchy Structure

```
Users Module
├── Models
│   └── User (Main Model)
├── DTOs
│   └── UserDTO
├── Services
│   └── UserService
├── Repositories
│   └── UserRepository
├── Controllers
│   ├── Auth
│   │   ├── RegisteredUserController
│   │   ├── AuthenticatedSessionController
│   │   ├── PasswordController
│   │   └── More auth controllers...
├── Filament Resources
│   └── UserResource
│       ├── Pages/ListUsers
│       ├── Pages/CreateUser
│       ├── Pages/ViewUser
│       └── Pages/EditUser
├── Policies
│   └── UserPolicy
├── Events
│   ├── UserCreated
│   ├── UserUpdated
│   └── UserDeleted
└── Database
    ├── Migrations (8+ files)
    ├── Factories
    │   └── UserFactory
    └── Seeders
        └── UsersSeeder
```

---

## 🗄️ Database

### Users Table

**Primary Fields:**
| Field | Type | Description |
|--------|--------|--------|
| `id` | INT (PK) | Unique identifier |
| `name` | VARCHAR(255) | User name |
| `email` | VARCHAR(255) | Email (unique) |
| `password` | VARCHAR(255) | Encrypted password |
| `phone` | VARCHAR(20) | Phone number (unique) |
| `status` | ENUM | active/inactive/suspended |
| `branch_id` | BIGINT (FK) | For seller role only |
| `created_at` | TIMESTAMP | Creation date |
| `updated_at` | TIMESTAMP | Update date |

**Relationships:**
- **belongsTo**: Branch (seller only)
- **hasMany**: AdminActionLog, Item, Request, Offer
- **roles**: Spatie Permission

---

## 🔐 Role & Permission System (Spatie Permission)

**4 Defined Roles:** super_admin, admin, seller, user  
**26 Fine-grained Permissions** | **Single role per user (validated)**

---

### The Four Roles

#### 1. 👑 Super Admin (Platform Owner)

**Description:** Complete system permissions

**Details:**
- **Permission Count:** 26 (all permissions)
- **branch_id:** ❌ Must be null (prohibited)
- **Cannot:** Be deleted or have role changed

**Permissions:**
```
- User Management (5 permissions)
- Product Management (9 permissions)
- Request Management (6 permissions)
- Offer Management (4 permissions)
- Approval Management (2 permissions)
```

---

#### 2. 🏢 Admin (Platform Administrator)

**Description:** Administrative access to all operations

**Details:**
- **Permission Count:** 24 (all except super admin specific)
- **branch_id:** ❌ Must be null
- **Can:** Manage users, approvals, content

**Permissions:**
```
- User Management (5)
- Product Management (9)
- Request Management (6)
- Offer Management (4)
```

---

#### 3. 🏪 Seller (Store Owner)

**Description:** Manage store and inventory

**Details:**
- **Permission Count:** 10 (seller specific)
- **branch_id:** ✅ Required (stores their branch ID)
- **Can:** Manage own products, offers, requests

**Permissions:**
```
- view_own_products
- create_products
- update_own_products
- delete_own_products
- publish_products
- view_requests
- create_requests
- update_own_requests
- view_offers
- create_offers
```

---

#### 4. 👤 User (Regular User)

**Description:** Basic customer access

**Details:**
- **Permission Count:** 6
- **branch_id:** ❌ Must be null
- **Can:** Browse, request, make offers

**Permissions:**
```
- view_products
- view_requests
- create_requests
- view_offers
- create_offers
```

---

### Permission Categories (26 Total)

#### User Management (5)
```
- view_users           (View user list)
- create_users         (Create new user)
- edit_users           (Edit user data)
- delete_users         (Delete user)
- manage_user_roles    (Assign/change roles)
```

#### Product Management (9)
```
- view_products        (View all products)
- create_products      (Create product)
- update_own_products  (Update own product)
- update_all_products  (Update any product)
- delete_own_products  (Delete own product)
- delete_all_products  (Delete any product)
- publish_products     (Publish/unpublish)
- approve_products     (Approve products)
- reject_products      (Reject products)
```

#### Request Management (6)
```
- view_requests        (View requests)
- create_requests      (Create request)
- update_own_requests  (Update own request)
- manage_requests      (Manage all requests)
- approve_requests     (Approve requests)
- reject_requests      (Reject requests)
```

#### Offer Management (4)
```
- view_offers          (View offers)
- create_offers        (Create offer)
- update_offers        (Update offer)
- manage_offers        (Manage all offers)
```

#### Approval Management (2)
```
- approve_content      (Approve submissions)
- reject_content       (Reject submissions)
```

#### System (2 implicit)
```
- access_panel         (Access admin panel)
- view_logs            (View action logs)
```

---

## 📦 Models

### User Model

**Path:** `app/Models/User.php`

**Description:** Main user model

**Key Relationships:**
```php
// One-to-many relationships
public function adminLogs(): HasMany
public function items(): HasMany
public function requests(): HasMany
public function offers(): HasMany

// Foreign key relationship
public function branch(): BelongsTo
```

**Helper Methods:**
```php
public function isSeller(): bool
public function isAdmin(): bool
public function isRegularUser(): bool

// Scopes
public function scopeActive($query)
public function scopeAdmins($query)
public function scopeSellers($query)
```

**Casts:**
```php
'password' => 'hashed'
```

**Fillable Fields:**
```php
'name', 'email', 'phone', 'password', 'status', 'branch_id'
```

---

## 📦 DTOs

### UserDTO

**Path:** `app/DTOs/UserDTO.php`

**Purpose:** Transfer user data between layers with validation

**Data:**
```php
class UserDTO {
    public string $name;          // User name (required)
    public string $email;         // Email (required)
    public ?string $phone;        // Phone number
    public ?string $password;     // Password
    public string $status;        // Status (default: active)
    public ?array $roles;         // Single role array (required)
}
```

**Methods:**
```php
public static function fromArray(array $data): self
public function toArray(): array
```

**Usage Example:**
```php
$userDTO = UserDTO::fromArray([
    'name' => 'John Smith',
    'email' => 'john@example.com',
    'phone' => '1234567890',
    'password' => 'secret123',
    'status' => 'active',
    'roles' => ['user']  // Single role only
]);
```

---

## ⚙️ Services

### UserService

**Path:** `app/Services/UserService.php`

**Purpose:** Business logic for user operations

**Constructor:**
```php
public function __construct(
    private UserRepository $repository,
    private PermissionService $permissionService
)
```

**Key Methods:**

#### create(array $data, array $roles)
```php
// Creates new user with single role validation
// Throws InvalidArgumentException if roles count !== 1
public function create(array $data, array $roles): User
```

#### update(User $user, array $data, array $roles)
```php
// Updates user with role validation
public function update(User $user, array $data, array $roles): User
```

#### assignRoles(User $user, array $roles)
```php
// Assigns roles with validation
// Enforces single role per user
private function assignRoles(User $user, array $roles): void
```

#### validateRoles(array $roles)
```php
// Validates roles
// Throws InvalidArgumentException if:
// - count($roles) !== 1
// - role not in [super_admin, admin, seller, user]
private function validateRoles(array $roles): void
```

#### validateBranchForRoles(User $user, array $roles)
```php
// Ensures branch_id logic:
// - seller: must have branch_id
// - others: must have null branch_id
private function validateBranchForRoles(User $user, array $roles): void
```

---

## 🗃️ Repositories

### UserRepository

**Path:** `app/Repositories/UserRepository.php`

**Purpose:** Data access layer for users

**Key Methods:**
```php
public function create(array $data): User
public function update(User $user, array $data): User
public function findById(int $id): ?User
public function all(array $filters = []): Collection
public function delete(User $user): bool
public function findByEmail(string $email): ?User
public function findByPhone(string $phone): ?User
```

---

## 🎮 Controllers

### Auth Controllers

**Location:** `app/Http/Controllers/Auth/`

**Controllers:**
- RegisteredUserController (register)
- AuthenticatedSessionController (login/logout)
- PasswordController (update password)
- PasswordResetLinkController (request reset)
- NewPasswordController (reset password)
- ConfirmablePasswordController (confirm password)
- VerifyEmailController (verify email)
- EmailVerificationNotificationController (resend)

---

## 🎨 Filament Resources

### UserResource

**Path:** `app/Filament/Resources/UserResource.php`

**Features:**
- List users with filtering
- Create/Edit forms
- Role assignment (single role only)
- Branch selection (seller only)
- Status management
- Email/Phone verification

**Form Sections:**
```php
Basic Information
├── Name (required)
├── Email (required, unique)
├── Phone (unique)
├── Password (required on create)
└── Status (active/inactive/suspended)

Roles & Permissions
├── Single role selector
└── Branch (conditional - seller only)
```

**Table Columns:**
```php
ID, Name, Email, Phone, Roles, Status, Created At
```

---

## 📋 Policies

### UserPolicy

**Path:** `app/Policies/UserPolicy.php`

**Methods:**
```php
public function viewAny(User $user): bool
public function view(User $user, User $model): bool
public function create(User $user): bool
public function update(User $user, User $model): bool
public function delete(User $user, User $model): bool
public function restore(User $user, User $model): bool
```

---

## 📡 Events

### UserCreated
```php
// Fired when user is created
public function __construct(public User $user)
```

### UserUpdated
```php
// Fired when user is updated
public function __construct(public User $user, public array $changes)
```

### UserDeleted
```php
// Fired when user is deleted
public function __construct(public User $user)
```

---

## 🚀 Migrations

### migration: create_users_table
- Creates users table with all fields
- Indexes on email, phone
- Foreign key: branch_id → branches.id

### migration: add_phone_status_to_users_table
- Adds phone, status fields

### migration: update_users_table (2026_01_26_100600)
- Drops user_type column (replaced by roles)

---

## 🌐 Routes

### Web Routes
```php
// Auth routes (built-in)
Route::middleware('guest')->group(function () {
    Route::get('register', RegisterController::class)->name('register');
    Route::post('register', RegisterController::class)->name('register.store');
    // Login, password reset, email verification...
});

Route::middleware('auth')->group(function () {
    Route::post('logout', LogoutController::class)->name('logout');
    // Profile, password update...
});
```

### Filament Routes
```
/admin/users        (List)
/admin/users/create (Create)
/admin/users/{id}   (View/Edit)
```

---

## 📊 Statistics

### Code Metrics
- **1 Model** (User)
- **1 DTO** (UserDTO)
- **1 Service** (UserService)
- **1 Repository** (UserRepository)
- **1 Policy** (UserPolicy)
- **3 Events** (UserCreated, UserUpdated, UserDeleted)
- **1 Filament Resource** (UserResource)
- **8+ Migrations**
- **8 Auth Controllers**
- **26 Permissions** in 6 categories
- **4 Roles**

### Database
- **1 Table:** users
- **9 Columns** (core fields only)
- **4 Relationships:** branch, adminLogs, items, requests, offers
- **Spatie Permission Tables:** roles, permissions, model_has_roles, model_has_permissions, role_has_permissions

---

## 🔄 Validation Flow

### Create User Process
```
1. UserDTO validation
2. validateRoles() → ensure exactly 1 role
3. Role check in valid list [super_admin, admin, seller, user]
4. validateBranchForRoles() → seller needs branch_id, others need null
5. Create user in database
6. Assign role via Spatie
7. Fire UserCreated event
```

### Update User Process
```
1. Validate data
2. validateRoles() → ensure exactly 1 role
3. validateBranchForRoles() → enforce branch logic
4. Update user
5. Update roles
6. Fire UserUpdated event
```

---

## ✅ Single Role Enforcement

The system enforces single role per user in three layers:

1. **Service Layer:** `validateRoles()` throws exception if count !== 1
2. **Form Validation:** Filament form restricts to single selection
3. **Database:** Only one role per user in role_user pivot table

**Error Example:**
```
InvalidArgumentException: User must have exactly one role.
```

---

## 🔒 Security Considerations

- Passwords are hashed (using Laravel's hash)
- Email/phone fields are unique
- branch_id is validated per role
- Soft deletes possible for users
- Admin panel access restricted to admin/super_admin
- All user modifications logged in admin_action_logs
- Email verification supported
- Phone verification supported

---

## 🚀 Future Enhancements

- [ ] Two-factor authentication
- [ ] Social login integration
- [ ] Audit trail for all user changes
- [ ] User activity logging
- [ ] API token management
- [ ] SSO integration

---

**Document Version:** 2.0.0  
**Last Updated:** January 27, 2026
