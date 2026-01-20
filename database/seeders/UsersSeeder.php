<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 regular users
        for ($i = 1; $i <= 10; $i++) {
            User::firstOrCreate(
                ['email' => "user{$i}@khezana.com"],
                [
                    'name' => "User {$i}",
                    'password' => Hash::make('password'),
                    'phone' => "09" . str_pad((string)$i, 8, '0', STR_PAD_LEFT),
                    'status' => 'active',
                ]
            );
        }

        $this->command->info('Created 10 regular users.');
    }
}
