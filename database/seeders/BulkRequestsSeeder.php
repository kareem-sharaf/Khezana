<?php

namespace Database\Seeders;

use App\Enums\ApprovalStatus;
use App\Enums\RequestStatus;
use App\Models\Approval;
use App\Models\Category;
use App\Models\Request;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BulkRequestsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $totalRequests = 1000;
        $chunkSize = 100; // Process in chunks of 100 for better performance
        
        $this->command->info("Starting to create {$totalRequests} requests...");
        
        // Get all users
        $users = User::all();
        if ($users->isEmpty()) {
            $this->command->error('No users found. Please run UsersSeeder first.');
            return;
        }
        
        // Get all active categories
        $categories = Category::where('is_active', true)->get();
        if ($categories->isEmpty()) {
            $this->command->error('No active categories found. Please run CategoriesSeeder first.');
            return;
        }
        
        // Request statuses (mostly open, some fulfilled, few closed)
        $statuses = [
            RequestStatus::OPEN,
            RequestStatus::OPEN,
            RequestStatus::OPEN,
            RequestStatus::OPEN,
            RequestStatus::OPEN,
            RequestStatus::FULFILLED,
            RequestStatus::CLOSED,
        ];
        
        // Sample titles for requests
        $titles = [
            'أبحث عن بدلة رجالية أنيقة',
            'أحتاج قميص رجالي كلاسيكي',
            'أبحث عن حذاء رجالي جلد',
            'أحتاج جاكيت شتوي دافئ',
            'أبحث عن فستان نسائي أنيق',
            'أحتاج عباءة سوداء تقليدية',
            'أبحث عن حذاء نسائي كعب عالي',
            'أحتاج بلوزة نسائية',
            'أبحث عن قميص أولاد',
            'أحتاج فستان بنات',
            'أبحث عن جينز أولاد',
            'أحتاج حذاء أطفال رياضي',
            'أبحث عن معطف رجالي',
            'أحتاج بنطلون جينز',
            'أبحث عن تيشيرت قطني',
            'أحتاج جوارب قطنية',
            'أبحث عن قبعة صيفية',
            'أحتاج حقيبة يد',
            'أبحث عن حزام جلد',
            'أحتاج ساعة يد',
            'أبحث عن نظارات شمسية',
            'أحتاج وشاح صوفي',
            'أبحث عن قفازات شتوية',
            'أحتاج أحذية رياضية',
            'أبحث عن كارديجان نسائي',
            'أحتاج سروال رياضي',
            'أبحث عن قميص بولو',
            'أحتاج شنطة سفر',
        ];
        
        // Sample descriptions
        $descriptions = [
            'أبحث عن هذا المنتج بحالة جيدة ومناسبة للمناسبات الرسمية',
            'أحتاج هذا المنتج بحالة ممتازة أو جديدة',
            'أبحث عن هذا المنتج بجودة عالية ومناسبة للاستخدام اليومي',
            'أحتاج هذا المنتج بحالة جيدة جداً ومناسبة للعمل',
            'أبحث عن هذا المنتج بحالة ممتازة ومناسبة للمناسبات',
            'أحتاج هذا المنتج بجودة عالية ومناسبة للاستخدام اليومي',
            'أبحث عن هذا المنتج بحالة جيدة ومناسبة للطقس البارد',
            'أحتاج هذا المنتج بحالة ممتازة ومناسبة للاحتفالات',
            'أبحث عن هذا المنتج بجودة عالية ومناسبة للاستخدام اليومي',
            'أحتاج هذا المنتج بحالة جيدة جداً ومناسبة للمناسبات',
        ];
        
        $progressBar = $this->command->getOutput()->createProgressBar($totalRequests);
        $progressBar->start();
        
        $processed = 0;
        $startTime = microtime(true);
        
        // Process in chunks
        for ($chunk = 0; $chunk < ceil($totalRequests / $chunkSize); $chunk++) {
            $requestsToCreate = min($chunkSize, $totalRequests - $processed);
            $requestsData = [];
            $approvalsData = [];
            
            for ($i = 0; $i < $requestsToCreate; $i++) {
                $user = $users->random();
                $category = $categories->random();
                $status = $statuses[array_rand($statuses)];
                $title = $titles[array_rand($titles)] . ' ' . ($processed + $i + 1);
                $description = $descriptions[array_rand($descriptions)];
                
                $slug = Str::slug($title) . '-' . ($processed + $i + 1) . '-' . Str::random(6);
                
                $requestsData[] = [
                    'user_id' => $user->id,
                    'category_id' => $category->id,
                    'title' => $title,
                    'slug' => $slug,
                    'description' => $description,
                    'status' => $status->value,
                    'created_at' => now()->subDays(rand(0, 365))->format('Y-m-d H:i:s'),
                    'updated_at' => now()->format('Y-m-d H:i:s'),
                ];
            }
            
            // Get the last request ID before insert
            $lastRequestId = DB::table('requests')->max('id') ?? 0;
            
            // Bulk insert requests
            DB::table('requests')->insert($requestsData);
            
            // Get the inserted requests IDs (they will be sequential)
            $insertedRequests = DB::table('requests')
                ->where('id', '>', $lastRequestId)
                ->orderBy('id', 'asc')
                ->get(['id', 'user_id']);
            
            foreach ($insertedRequests as $index => $request) {
                $requestData = $requestsData[$index];
                
                // Create approval
                $approvalsData[] = [
                    'approvable_type' => Request::class,
                    'approvable_id' => $request->id,
                    'status' => ApprovalStatus::APPROVED->value,
                    'submitted_by' => $requestData['user_id'],
                    'reviewed_by' => $requestData['user_id'],
                    'reviewed_at' => now()->format('Y-m-d H:i:s'),
                    'resubmission_count' => 0,
                    'created_at' => now()->format('Y-m-d H:i:s'),
                    'updated_at' => now()->format('Y-m-d H:i:s'),
                ];
            }
            
            // Bulk insert approvals
            if (!empty($approvalsData)) {
                DB::table('approvals')->insert($approvalsData);
            }
            
            $processed += $requestsToCreate;
            $progressBar->advance($requestsToCreate);
            
            // Clear arrays for next chunk
            $requestsData = [];
            $approvalsData = [];
        }
        
        $progressBar->finish();
        $this->command->newLine();
        
        $duration = microtime(true) - $startTime;
        $this->command->info("Successfully created {$totalRequests} requests in " . round($duration, 2) . " seconds!");
        $this->command->info("Average: " . round($totalRequests / $duration, 2) . " requests/second");
    }
}
