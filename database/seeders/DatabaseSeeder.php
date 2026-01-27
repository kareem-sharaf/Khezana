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

        // Get branches for seller assignment
        $branches = Branch::active()->get();
        $branchIds = $branches->pluck('id')->toArray();

        // ========== Create Super Admin (no branch) ==========
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@khezana.com'],
            [
                'name' => 'المدير العام',
                'password' => bcrypt('password'),
                'status' => 'active',
                'branch_id' => null, // ✅ Super admin must NOT have branch
            ]
        );
        if (!$superAdmin->hasRole('super_admin')) {
            $superAdmin->assignRole('super_admin');
        }

        // ========== Create General Admin (no branch) ==========
        $adminGeneral = User::firstOrCreate(
            ['email' => 'admin@khezana.com'],
            [
                'name' => 'مدير عام',
                'password' => bcrypt('password'),
                'status' => 'active',
                'branch_id' => null, // ✅ Admin must NOT have branch
            ]
        );
        if (!$adminGeneral->hasRole('admin')) {
            $adminGeneral->assignRole('admin');
        }

        // ========== Create Sellers for each branch ==========
        $sellerBranches = [
            ['email' => 'seller.mazzeh@khezana.com', 'name' => 'أحمد - بائع فرع المزة', 'branch_code' => 'MZE'],
            ['email' => 'seller.shaalan@khezana.com', 'name' => 'محمد - بائع فرع الشعلان', 'branch_code' => 'SHL'],
            ['email' => 'seller.malki@khezana.com', 'name' => 'خالد - بائع فرع المالكي', 'branch_code' => 'MLK'],
            ['email' => 'seller.babtoma@khezana.com', 'name' => 'عمر - بائع فرع باب توما', 'branch_code' => 'BTM'],
            ['email' => 'seller.jaramana@khezana.com', 'name' => 'ياسر - بائع فرع جرمانا', 'branch_code' => 'JRM'],
        ];

        foreach ($sellerBranches as $sellerData) {
            $branch = Branch::where('code', $sellerData['branch_code'])->first();

            if (!$branch) {
                $this->command->warn("Branch {$sellerData['branch_code']} not found, skipping seller creation");
                continue;
            }

            $seller = User::firstOrCreate(
                ['email' => $sellerData['email']],
                [
                    'name' => $sellerData['name'],
                    'password' => bcrypt('password'),
                    'status' => 'active',
                    'branch_id' => $branch->id, // ✅ Seller MUST have branch
                ]
            );

            if (!$seller->hasRole('seller')) {
                $seller->assignRole('seller');
            }

            // Ensure branch_id is set correctly
            if ($seller->branch_id !== $branch->id) {
                $seller->update(['branch_id' => $branch->id]);
            }
        }

        // ========== Create Regular User (no branch) ==========
        $regularUser = User::firstOrCreate(
            ['email' => 'user@khezana.com'],
            [
                'name' => 'مستخدم عادي',
                'password' => bcrypt('password'),
                'status' => 'active',
                'branch_id' => null, // ✅ Regular user must NOT have branch
            ]
        );
        if (!$regularUser->hasRole('user')) {
            $regularUser->assignRole('user');
        }

        // ========== Display Login Credentials ==========
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════');
        $this->command->info('          Login Credentials');
        $this->command->info('═══════════════════════════════════════════');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Super Admin', 'superadmin@khezana.com', 'password'],
                ['Admin (General)', 'admin@khezana.com', 'password'],
                ['Seller (المزة)', 'seller.mazzeh@khezana.com', 'password'],
                ['Seller (الشعلان)', 'seller.shaalan@khezana.com', 'password'],
                ['Seller (المالكي)', 'seller.malki@khezana.com', 'password'],
                ['Seller (باب توما)', 'seller.babtoma@khezana.com', 'password'],
                ['Seller (جرمانا)', 'seller.jaramana@khezana.com', 'password'],
                ['Regular User', 'user@khezana.com', 'password'],
            ]
        );
        $this->command->info('═══════════════════════════════════════════');
    }
}
