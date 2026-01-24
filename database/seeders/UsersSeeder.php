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
        // Create 100 regular users (for bulk seeding)
        $usersToCreate = 100;
        
        $this->command->info("Creating {$usersToCreate} regular users...");
        $progressBar = $this->command->getOutput()->createProgressBar($usersToCreate);
        $progressBar->start();
        
        for ($i = 1; $i <= $usersToCreate; $i++) {
            User::firstOrCreate(
                ['email' => "user{$i}@khezana.com"],
                [
                    'name' => "User {$i}",
                    'password' => Hash::make('password'),
                    'phone' => "09" . str_pad((string)$i, 8, '0', STR_PAD_LEFT),
                    'status' => 'active',
                ]
            );
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->command->newLine();
        $this->command->info("Created {$usersToCreate} regular users.");
    }
}
