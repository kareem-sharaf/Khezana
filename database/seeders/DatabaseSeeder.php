<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed in proper order
        $this->call([
            RolesAndPermissionsSeeder::class,
            CategoriesSeeder::class,
            AttributesSeeder::class,
            BranchSeeder::class,      // Branches before users
            UsersSeeder::class,
            ItemsSeeder::class,
            RequestsSeeder::class,    // Add requests seeder
        ]);

        // Get branches for admin assignment
        $branches = Branch::active()->get();
        $branchIds = $branches->pluck('id')->toArray();

        // Create a super admin user (if not exists) - NO branch
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@khezana.com'],
            [
                'name' => 'المدير العام',
                'password' => bcrypt('password'),
                'status' => 'active',
                'branch_id' => null, // Super admin not tied to branch
            ]
        );
        if (!$superAdmin->hasRole('super_admin')) {
            $superAdmin->assignRole('super_admin');
        }

        // Create admin users for each branch
        $adminBranches = [
            ['email' => 'admin.mazzeh@khezana.com', 'name' => 'أحمد - مدير فرع المزة', 'branch_code' => 'MZE'],
            ['email' => 'admin.shaalan@khezana.com', 'name' => 'محمد - مدير فرع الشعلان', 'branch_code' => 'SHL'],
            ['email' => 'admin.malki@khezana.com', 'name' => 'خالد - مدير فرع المالكي', 'branch_code' => 'MLK'],
            ['email' => 'admin.babtoma@khezana.com', 'name' => 'عمر - مدير فرع باب توما', 'branch_code' => 'BTM'],
            ['email' => 'admin.jaramana@khezana.com', 'name' => 'ياسر - مدير فرع جرمانا', 'branch_code' => 'JRM'],
        ];

        foreach ($adminBranches as $adminData) {
            $branch = Branch::where('code', $adminData['branch_code'])->first();
            
            $admin = User::firstOrCreate(
                ['email' => $adminData['email']],
                [
                    'name' => $adminData['name'],
                    'password' => bcrypt('password'),
                    'status' => 'active',
                    'branch_id' => $branch?->id,
                ]
            );
            
            if (!$admin->hasRole('admin')) {
                $admin->assignRole('admin');
            }
            
            // Update branch_id if user already exists
            if ($branch && $admin->branch_id !== $branch->id) {
                $admin->update(['branch_id' => $branch->id]);
            }
        }

        // Create a general admin (not tied to specific branch)
        $admin = User::firstOrCreate(
            ['email' => 'admin@khezana.com'],
            [
                'name' => 'مدير عام',
                'password' => bcrypt('password'),
                'status' => 'active',
                'branch_id' => null,
            ]
        );
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // Create a regular user (if not exists)
        $user = User::firstOrCreate(
            ['email' => 'user@khezana.com'],
            [
                'name' => 'مستخدم عادي',
                'password' => bcrypt('password'),
                'status' => 'active',
            ]
        );
        if (!$user->hasRole('user')) {
            $user->assignRole('user');
        }

        $this->command->info('');
        $this->command->info('=== Login Credentials ===');
        $this->command->info('Super Admin: superadmin@khezana.com / password');
        $this->command->info('Admin (General): admin@khezana.com / password');
        $this->command->info('Admin (المزة): admin.mazzeh@khezana.com / password');
        $this->command->info('Admin (الشعلان): admin.shaalan@khezana.com / password');
        $this->command->info('Admin (المالكي): admin.malki@khezana.com / password');
        $this->command->info('Admin (باب توما): admin.babtoma@khezana.com / password');
        $this->command->info('Admin (جرمانا): admin.jaramana@khezana.com / password');
        $this->command->info('User: user@khezana.com / password');
        $this->command->info('========================');
    }
}
