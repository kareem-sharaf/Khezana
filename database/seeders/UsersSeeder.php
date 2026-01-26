<?php

namespace Database\Seeders;

use App\Models\Branch;
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
        // Get branches for assigning to users
        $branches = Branch::active()->get();
        $branchIds = $branches->pluck('id')->toArray();
        
        // Create 100 regular users (for bulk seeding)
        $usersToCreate = 100;
        
        $this->command->info("Creating {$usersToCreate} regular users...");
        $progressBar = $this->command->getOutput()->createProgressBar($usersToCreate);
        $progressBar->start();
        
        for ($i = 1; $i <= $usersToCreate; $i++) {
            // 30% of users will be assigned to a branch (as if they're sellers in a branch)
            $branchId = null;
            if (!empty($branchIds) && rand(1, 100) <= 30) {
                $branchId = $branchIds[array_rand($branchIds)];
            }
            
            User::firstOrCreate(
                ['email' => "user{$i}@khezana.com"],
                [
                    'name' => $this->getArabicName($i),
                    'password' => Hash::make('password'),
                    'phone' => "09" . str_pad((string)$i, 8, '0', STR_PAD_LEFT),
                    'status' => 'active',
                    'branch_id' => $branchId,
                ]
            );
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->command->newLine();
        $this->command->info("Created {$usersToCreate} regular users.");
    }
    
    /**
     * Get a random Arabic name
     */
    private function getArabicName(int $index): string
    {
        $firstNames = [
            'محمد', 'أحمد', 'علي', 'حسن', 'خالد', 'عمر', 'ياسر', 'سامر', 'فادي', 'رامي',
            'ماجد', 'طارق', 'هاني', 'وليد', 'بسام', 'نادر', 'عادل', 'سمير', 'كريم', 'زياد',
            'فاطمة', 'مريم', 'نور', 'سارة', 'لينا', 'دانا', 'رنا', 'هبة', 'سلمى', 'ياسمين',
            'ريم', 'لمى', 'غادة', 'سوسن', 'منى', 'هند', 'رشا', 'ديما', 'مايا', 'جنى',
        ];
        
        $lastNames = [
            'العلي', 'الأحمد', 'الخالد', 'المحمد', 'الحسن', 'السعيد', 'الشامي', 'الدمشقي',
            'الحلبي', 'الحموي', 'اللاذقاني', 'الطرطوسي', 'الحمصي', 'الإدلبي', 'الرقاوي',
            'البيطار', 'الجندي', 'النجار', 'الحداد', 'الصباغ', 'العطار', 'القصاب',
        ];
        
        $firstName = $firstNames[($index - 1) % count($firstNames)];
        $lastName = $lastNames[($index - 1) % count($lastNames)];
        
        return "{$firstName} {$lastName}";
    }
}
