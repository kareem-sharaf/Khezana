<?php

namespace Database\Seeders;

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
        // Seed roles and permissions first
        $this->call([
            RolesAndPermissionsSeeder::class,
            CategoriesSeeder::class,
            AttributesSeeder::class,
            UsersSeeder::class,
            ItemsSeeder::class,
        ]);

        // Create a super admin user (if not exists)
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@khezana.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
                'status' => 'active',
            ]
        );
        if (!$superAdmin->hasRole('super_admin')) {
            $superAdmin->assignRole('super_admin');
        }

        // Create an admin user (if not exists)
        $admin = User::firstOrCreate(
            ['email' => 'admin@khezana.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'status' => 'active',
            ]
        );
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // Create a regular user (if not exists)
        $user = User::firstOrCreate(
            ['email' => 'user@khezana.com'],
            [
                'name' => 'Regular User',
                'password' => bcrypt('password'),
                'status' => 'active',
            ]
        );
        if (!$user->hasRole('user')) {
            $user->assignRole('user');
        }
    }
}
