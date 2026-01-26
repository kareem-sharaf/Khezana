<?php

namespace Database\Seeders;

use App\Enums\ApprovalStatus;
use App\Enums\ItemAvailability;
use App\Enums\OperationType;
use App\Models\Approval;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Item;
use App\Models\ItemImage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BulkItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $totalItems = 1000;
        $chunkSize = 100; // Process in chunks of 100 for better performance
        
        $this->command->info("Starting to create {$totalItems} items...");
        
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

        // Get all active branches
        $branches = Branch::active()->get();
        $branchIds = $branches->pluck('id')->all();
        
        // Governorates list
        $governorates = [
            'damascus', 'aleppo', 'homs', 'hama', 'latakia', 
            'tartus', 'daraa', 'sweida', 'hasakah', 'deir_ezzor', 'raqqa', 'idlib'
        ];
        
        // Operation types
        $operationTypes = [OperationType::SELL, OperationType::RENT, OperationType::DONATE];
        
        // Conditions
        $conditions = ['new', 'used'];
        
        // Sample titles for clothing items
        $titles = [
            'بدلة رجالية أنيقة', 'قميص رجالي كلاسيكي', 'حذاء رجالي جلد', 'جاكيت شتوي دافئ',
            'فستان نسائي أنيق', 'عباءة سوداء تقليدية', 'حذاء نسائي كعب عالي', 'بلوزة نسائية',
            'قميص أولاد', 'فستان بنات', 'جينز أولاد', 'حذاء أطفال رياضي',
            'معطف رجالي', 'بنطلون جينز', 'تيشيرت قطني', 'جوارب قطنية',
            'قبعة صيفية', 'حقيبة يد', 'حزام جلد', 'ساعة يد',
            'نظارات شمسية', 'وشاح صوفي', 'قفازات شتوية', 'أحذية رياضية',
        ];
        
        // Sample descriptions
        $descriptions = [
            'مناسبة للمناسبات الرسمية، بحالة ممتازة',
            'من القماش عالي الجودة، مناسب للعمل',
            'أنيق ومريح، بحالة جيدة جداً',
            'دافئ ومناسب للطقس البارد',
            'مناسب للمناسبات والاحتفالات',
            'تقليدي وأنيق، بحالة ممتازة',
            'مريح ومناسب للاستخدام اليومي',
            'من القطن الطبيعي، سهل الغسيل',
        ];
        
        // Image paths (using existing images)
        $imagePaths = [
            'items/wardrobe-background.jpg',
            'items/wardrobe-background-2.jpg',
        ];
        
        $progressBar = $this->command->getOutput()->createProgressBar($totalItems);
        $progressBar->start();
        
        $processed = 0;
        $startTime = microtime(true);
        
        // Process in chunks
        for ($chunk = 0; $chunk < ceil($totalItems / $chunkSize); $chunk++) {
            $itemsToCreate = min($chunkSize, $totalItems - $processed);
            $itemsData = [];
            $approvalsData = [];
            $imagesData = [];
            
            for ($i = 0; $i < $itemsToCreate; $i++) {
                $user = $users->random();
                $category = $categories->random();
                $operationType = $operationTypes[array_rand($operationTypes)];
                $governorate = $governorates[array_rand($governorates)];
                $condition = $conditions[array_rand($conditions)];
                $title = $titles[array_rand($titles)] . ' ' . ($processed + $i + 1);
                $description = $descriptions[array_rand($descriptions)];

                // Approval status distribution
                $statusRoll = rand(1, 100);
                if ($statusRoll <= 10) {
                    $approvalStatus = ApprovalStatus::VERIFICATION_REQUIRED;
                } elseif ($statusRoll <= 85) {
                    $approvalStatus = ApprovalStatus::APPROVED;
                } else {
                    $approvalStatus = ApprovalStatus::PENDING;
                }

                // Assign branch for non-verification items (admin sets branch later for verification)
                $branchId = null;
                if ($approvalStatus !== ApprovalStatus::VERIFICATION_REQUIRED && !empty($branchIds) && rand(0, 1) === 1) {
                    $branchId = $branchIds[array_rand($branchIds)];
                }
                
                // Generate price based on operation type
                $price = null;
                $depositAmount = null;
                
                if ($operationType === OperationType::SELL) {
                    $price = rand(10000, 500000); // 10,000 to 500,000 SYP
                } elseif ($operationType === OperationType::RENT) {
                    $price = rand(5000, 50000); // 5,000 to 50,000 SYP
                    $depositAmount = rand(10000, 100000); // 10,000 to 100,000 SYP
                }
                // DONATE has no price
                
                $slug = Str::slug($title) . '-' . ($processed + $i + 1) . '-' . Str::random(6);
                
                $itemsData[] = [
                    'user_id' => $user->id,
                    'branch_id' => $branchId,
                    'category_id' => $category->id,
                    'operation_type' => $operationType->value,
                    'title' => $title,
                    'slug' => $slug,
                    'description' => $description,
                    'governorate' => $governorate,
                    'condition' => $condition,
                    'price' => $price,
                    'deposit_amount' => $depositAmount,
                    'is_available' => true,
                    'availability_status' => ItemAvailability::AVAILABLE->value,
                    'approval_status' => $approvalStatus->value,
                    'created_at' => now()->subDays(rand(0, 365))->format('Y-m-d H:i:s'),
                    'updated_at' => now()->format('Y-m-d H:i:s'),
                ];
            }
            
            // Get the last item ID before insert
            $lastItemId = DB::table('items')->max('id') ?? 0;
            
            // Bulk insert items
            DB::table('items')->insert($itemsData);
            
            // Get the inserted items IDs (they will be sequential)
            $insertedItems = DB::table('items')
                ->where('id', '>', $lastItemId)
                ->orderBy('id', 'asc')
                ->get(['id', 'slug', 'user_id']);
            
            foreach ($insertedItems as $index => $item) {
                $itemData = $itemsData[$index];
                
                // Create approval
                $approvalPayload = [
                    'approvable_type' => Item::class,
                    'approvable_id' => $item->id,
                    'status' => $itemData['approval_status'] ?? ApprovalStatus::APPROVED->value,
                    'submitted_by' => $itemData['user_id'],
                    'resubmission_count' => 0,
                    'created_at' => now()->format('Y-m-d H:i:s'),
                    'updated_at' => now()->format('Y-m-d H:i:s'),
                ];

                if (($approvalPayload['status'] ?? null) === ApprovalStatus::APPROVED->value) {
                    $approvalPayload['reviewed_by'] = $itemData['user_id'];
                    $approvalPayload['reviewed_at'] = now()->format('Y-m-d H:i:s');
                    $approvalPayload['rejection_reason'] = null;
                }

                if (($approvalPayload['status'] ?? null) === ApprovalStatus::VERIFICATION_REQUIRED->value) {
                    $approvalPayload['reviewed_by'] = $itemData['user_id'];
                    $approvalPayload['reviewed_at'] = now()->format('Y-m-d H:i:s');
                    $approvalPayload['rejection_reason'] = __('approvals.messages.verification_required');
                }

                $approvalsData[] = $approvalPayload;
                
                // Create images (1-3 images per item)
                $numImages = rand(1, 3);
                for ($img = 0; $img < $numImages; $img++) {
                    $imagesData[] = [
                        'item_id' => $item->id,
                        'path' => $imagePaths[array_rand($imagePaths)],
                        'path_webp' => null,
                        'disk' => 'public',
                        'is_primary' => $img === 0, // First image is primary
                        'created_at' => now()->format('Y-m-d H:i:s'),
                        'updated_at' => now()->format('Y-m-d H:i:s'),
                    ];
                }
            }
            
            // Bulk insert approvals
            if (!empty($approvalsData)) {
                DB::table('approvals')->insert($approvalsData);
            }
            
            // Bulk insert images
            if (!empty($imagesData)) {
                DB::table('item_images')->insert($imagesData);
            }
            
            $processed += $itemsToCreate;
            $progressBar->advance($itemsToCreate);
            
            // Clear arrays for next chunk
            $itemsData = [];
            $approvalsData = [];
            $imagesData = [];
        }
        
        $progressBar->finish();
        $this->command->newLine();
        
        $duration = microtime(true) - $startTime;
        $this->command->info("Successfully created {$totalItems} items in " . round($duration, 2) . " seconds!");
        $this->command->info("Average: " . round($totalItems / $duration, 2) . " items/second");
    }
}
