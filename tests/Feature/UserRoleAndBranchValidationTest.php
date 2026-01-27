<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Branch;
use App\Services\UserService;
use App\DTOs\UserDTO;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class UserRoleAndBranchValidationTest extends TestCase
{
    use RefreshDatabase;

    protected UserService $userService;
    protected Branch $branch;

    public function setUp(): void
    {
        parent::setUp();

        $this->userService = app(UserService::class);

        // إنشاء فرع للاختبار
        $this->branch = Branch::factory()->active()->create([
            'name_ar' => 'فرع الاختبار',
            'name_en' => 'Test Branch',
        ]);

        // إنشاء الأدوار والصلاحيات
        $this->seed('RolesAndPermissionsSeeder');
    }

    // =====================================================
    // اختبارات الـ Seller - يجب أن يكون له branch_id
    // =====================================================

    /** @test */
    public function test_seller_must_have_branch_assigned()
    {
        // يجب أن ينجح عند إنشاء seller مع branch
        $user = $this->userService->create(
            userDTO: UserDTO::fromArray([
                'name' => 'بائع تجربة',
                'email' => 'seller-test@example.com',
                'phone' => '0912345678',
                'password' => 'SecurePassword123!',
                'status' => 'active',
                'branch_id' => $this->branch->id,
            ]),
            roles: ['seller']
        );

        $this->assertNotNull($user->branch_id);
        $this->assertEquals($this->branch->id, $user->branch_id);
        $this->assertTrue($user->hasRole('seller'));
    }

    /** @test */
    public function test_seller_creation_fails_without_branch()
    {
        // يجب أن يفشل عند محاولة إنشاء seller بدون branch
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Seller must have a branch assigned');

        $this->userService->create(
            userDTO: UserDTO::fromArray([
                'name' => 'بائع بدون فرع',
                'email' => 'seller-no-branch@example.com',
                'phone' => '0912345678',
                'password' => 'SecurePassword123!',
                'status' => 'active',
                'branch_id' => null, // ❌ خطأ
            ]),
            roles: ['seller']
        );
    }

    // =====================================================
    // اختبارات Admin - يجب ألا يكون له branch_id
    // =====================================================

    /** @test */
    public function test_admin_must_not_have_branch()
    {
        // يجب أن ينجح عند إنشاء admin بدون branch
        $user = $this->userService->create(
            userDTO: UserDTO::fromArray([
                'name' => 'موظف إدارة',
                'email' => 'admin-test@example.com',
                'phone' => '0912345678',
                'password' => 'SecurePassword123!',
                'status' => 'active',
                'branch_id' => null,
            ]),
            roles: ['admin']
        );

        $this->assertNull($user->branch_id);
        $this->assertTrue($user->hasRole('admin'));
    }

    /** @test */
    public function test_admin_creation_fails_with_branch()
    {
        // يجب أن يفشل عند محاولة إنشاء admin مع branch
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Admin cannot have a branch assigned');

        $this->userService->create(
            userDTO: UserDTO::fromArray([
                'name' => 'موظف مع فرع',
                'email' => 'admin-with-branch@example.com',
                'phone' => '0912345678',
                'password' => 'SecurePassword123!',
                'status' => 'active',
                'branch_id' => $this->branch->id, // ❌ خطأ
            ]),
            roles: ['admin']
        );
    }

    // =====================================================
    // اختبارات Super Admin - يجب ألا يكون له branch_id
    // =====================================================

    /** @test */
    public function test_super_admin_must_not_have_branch()
    {
        // يجب أن ينجح عند إنشاء super_admin بدون branch
        $user = $this->userService->create(
            userDTO: UserDTO::fromArray([
                'name' => 'مالك المنصة',
                'email' => 'superadmin-test@example.com',
                'phone' => '0912345678',
                'password' => 'SecurePassword123!',
                'status' => 'active',
                'branch_id' => null,
            ]),
            roles: ['super_admin']
        );

        $this->assertNull($user->branch_id);
        $this->assertTrue($user->isSuperAdmin());
    }

    /** @test */
    public function test_super_admin_creation_fails_with_branch()
    {
        // يجب أن يفشل عند محاولة إنشاء super_admin مع branch
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Super Admin cannot have a branch assigned');

        $this->userService->create(
            userDTO: UserDTO::fromArray([
                'name' => 'مالك مع فرع',
                'email' => 'superadmin-with-branch@example.com',
                'phone' => '0912345678',
                'password' => 'SecurePassword123!',
                'status' => 'active',
                'branch_id' => $this->branch->id, // ❌ خطأ
            ]),
            roles: ['super_admin']
        );
    }

    // =====================================================
    // اختبارات المستخدم العادي - يجب ألا يكون له branch_id
    // =====================================================

    /** @test */
    public function test_regular_user_must_not_have_branch()
    {
        // يجب أن ينجح عند إنشاء user بدون branch
        $user = $this->userService->create(
            userDTO: UserDTO::fromArray([
                'name' => 'مستخدم عادي',
                'email' => 'user-test@example.com',
                'phone' => '0912345678',
                'password' => 'SecurePassword123!',
                'status' => 'active',
                'branch_id' => null,
            ]),
            roles: ['user']
        );

        $this->assertNull($user->branch_id);
        $this->assertTrue($user->hasRole('user'));
    }

    /** @test */
    public function test_regular_user_creation_fails_with_branch()
    {
        // يجب أن يفشل عند محاولة إنشاء user مع branch
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('User cannot have a branch assigned');

        $this->userService->create(
            userDTO: UserDTO::fromArray([
                'name' => 'مستخدم مع فرع',
                'email' => 'user-with-branch@example.com',
                'phone' => '0912345678',
                'password' => 'SecurePassword123!',
                'status' => 'active',
                'branch_id' => $this->branch->id, // ❌ خطأ
            ]),
            roles: ['user']
        );
    }

    // =====================================================
    // اختبارات التحديث - تغيير الأدوار
    // =====================================================

    /** @test */
    public function test_changing_seller_to_admin_clears_branch()
    {
        // إنشاء seller مع branch
        $user = $this->userService->create(
            userDTO: UserDTO::fromArray([
                'name' => 'بائع سيصبح موظف',
                'email' => 'seller-to-admin@example.com',
                'phone' => '0912345678',
                'password' => 'SecurePassword123!',
                'status' => 'active',
                'branch_id' => $this->branch->id,
            ]),
            roles: ['seller']
        );

        $this->assertNotNull($user->branch_id);

        // تحديث الدور إلى admin - يجب أن تُمسح branch_id
        $updatedUser = $this->userService->update(
            userId: $user->id,
            dto: UserDTO::fromArray([
                'name' => 'موظف سابق بائع',
                'email' => $user->email,
                'phone' => $user->phone,
                'status' => 'active',
            ]),
            newRoles: ['admin']
        );

        $this->assertNull($updatedUser->branch_id);
        $this->assertTrue($updatedUser->hasRole('admin'));
        $this->assertFalse($updatedUser->hasRole('seller'));
    }

    /** @test */
    public function test_changing_user_to_seller_requires_branch()
    {
        // إنشاء user بدون branch
        $user = $this->userService->create(
            userDTO: UserDTO::fromArray([
                'name' => 'مستخدم سيصبح بائع',
                'email' => 'user-to-seller@example.com',
                'phone' => '0912345678',
                'password' => 'SecurePassword123!',
                'status' => 'active',
                'branch_id' => null,
            ]),
            roles: ['user']
        );

        $this->assertNull($user->branch_id);

        // محاولة تغيير الدور إلى seller بدون branch - يجب أن يفشل
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Seller must have a branch assigned');

        $this->userService->update(
            userId: $user->id,
            dto: UserDTO::fromArray([
                'name' => 'بائع سابق مستخدم',
                'email' => $user->email,
                'phone' => $user->phone,
                'status' => 'active',
                'branch_id' => null, // ❌ خطأ
            ]),
            newRoles: ['seller']
        );
    }

    /** @test */
    public function test_changing_user_to_seller_with_branch_succeeds()
    {
        // إنشاء user بدون branch
        $user = $this->userService->create(
            userDTO: UserDTO::fromArray([
                'name' => 'مستخدم سيصبح بائع',
                'email' => 'user-to-seller-success@example.com',
                'phone' => '0912345678',
                'password' => 'SecurePassword123!',
                'status' => 'active',
                'branch_id' => null,
            ]),
            roles: ['user']
        );

        // تغيير الدور إلى seller مع branch - يجب أن ينجح
        $updatedUser = $this->userService->update(
            userId: $user->id,
            dto: UserDTO::fromArray([
                'name' => 'بائع سابق مستخدم',
                'email' => $user->email,
                'phone' => $user->phone,
                'status' => 'active',
                'branch_id' => $this->branch->id, // ✅ صحيح
            ]),
            newRoles: ['seller']
        );

        $this->assertNotNull($updatedUser->branch_id);
        $this->assertEquals($this->branch->id, $updatedUser->branch_id);
        $this->assertTrue($updatedUser->hasRole('seller'));
    }

    // =====================================================
    // اختبارات الدوال المساعدة
    // =====================================================

    /** @test */
    public function test_is_seller_helper_method()
    {
        $seller = $this->userService->create(
            userDTO: UserDTO::fromArray([
                'name' => 'بائع',
                'email' => 'seller-helper@example.com',
                'phone' => '0912345678',
                'password' => 'SecurePassword123!',
                'status' => 'active',
                'branch_id' => $this->branch->id,
            ]),
            roles: ['seller']
        );

        $this->assertTrue($seller->isSeller());
        $this->assertFalse($seller->isAdmin());
        $this->assertFalse($seller->isRegularUser());
    }

    /** @test */
    public function test_is_admin_helper_method()
    {
        $admin = $this->userService->create(
            userDTO: UserDTO::fromArray([
                'name' => 'موظف إدارة',
                'email' => 'admin-helper@example.com',
                'phone' => '0912345678',
                'password' => 'SecurePassword123!',
                'status' => 'active',
                'branch_id' => null,
            ]),
            roles: ['admin']
        );

        $this->assertFalse($admin->isSeller());
        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isRegularUser());
    }

    /** @test */
    public function test_sellers_scope()
    {
        // إنشاء عدة مستخدمين
        $seller1 = $this->userService->create(
            userDTO: UserDTO::fromArray([
                'name' => 'بائع 1',
                'email' => 'seller1@example.com',
                'phone' => '0912345678',
                'password' => 'SecurePassword123!',
                'status' => 'active',
                'branch_id' => $this->branch->id,
            ]),
            roles: ['seller']
        );

        $seller2 = $this->userService->create(
            userDTO: UserDTO::fromArray([
                'name' => 'بائع 2',
                'email' => 'seller2@example.com',
                'phone' => '0912345678',
                'password' => 'SecurePassword123!',
                'status' => 'active',
                'branch_id' => $this->branch->id,
            ]),
            roles: ['seller']
        );

        $admin = $this->userService->create(
            userDTO: UserDTO::fromArray([
                'name' => 'موظف',
                'email' => 'admin@example.com',
                'phone' => '0912345678',
                'password' => 'SecurePassword123!',
                'status' => 'active',
                'branch_id' => null,
            ]),
            roles: ['admin']
        );

        $sellers = User::sellers()->get();

        $this->assertEquals(2, $sellers->count());
        $this->assertTrue($sellers->contains($seller1));
        $this->assertTrue($sellers->contains($seller2));
        $this->assertFalse($sellers->contains($admin));
    }

    // =====================================================
    // اختبارات الصلاحيات
    // =====================================================

    /** @test */
    public function test_admin_has_view_users_permission()
    {
        $admin = $this->userService->create(
            userDTO: UserDTO::fromArray([
                'name' => 'موظف',
                'email' => 'admin-perm@example.com',
                'phone' => '0912345678',
                'password' => 'SecurePassword123!',
                'status' => 'active',
                'branch_id' => null,
            ]),
            roles: ['admin']
        );

        $this->assertTrue($admin->can('view_users'));
        $this->assertTrue($admin->can('create_users'));
        $this->assertTrue($admin->can('update_users'));
    }

    /** @test */
    public function test_seller_cannot_view_all_users()
    {
        $seller = $this->userService->create(
            userDTO: UserDTO::fromArray([
                'name' => 'بائع',
                'email' => 'seller-perm@example.com',
                'phone' => '0912345678',
                'password' => 'SecurePassword123!',
                'status' => 'active',
                'branch_id' => $this->branch->id,
            ]),
            roles: ['seller']
        );

        $this->assertFalse($seller->can('view_users'));
        $this->assertTrue($seller->can('create_products'));
    }

    /** @test */
    public function test_user_has_limited_permissions()
    {
        $user = $this->userService->create(
            userDTO: UserDTO::fromArray([
                'name' => 'مستخدم عادي',
                'email' => 'user-perm@example.com',
                'phone' => '0912345678',
                'password' => 'SecurePassword123!',
                'status' => 'active',
                'branch_id' => null,
            ]),
            roles: ['user']
        );

        $this->assertTrue($user->can('view_products'));
        $this->assertTrue($user->can('create_requests'));
        $this->assertFalse($user->can('create_users'));
        $this->assertFalse($user->can('create_products'));
    }
}
