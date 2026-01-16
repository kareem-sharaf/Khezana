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
        ]);

        // Create a super admin user
        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@khezana.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        $superAdmin->assignRole('super_admin');

        // Create an admin user
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@khezana.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        $admin->assignRole('admin');

        // Create a regular user
        $user = User::factory()->create([
            'name' => 'Regular User',
            'email' => 'user@khezana.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        $user->assignRole('user');
    }
}
