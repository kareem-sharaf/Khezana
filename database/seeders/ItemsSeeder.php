<?php

namespace Database\Seeders;

use App\Enums\ApprovalStatus;
use App\Enums\ItemAvailability;
use App\Enums\OperationType;
use App\Models\Approval;
use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('email', '!=', 'superadmin@khezana.com')
            ->where('email', '!=', 'admin@khezana.com')
            ->get();
        
        $categories = Category::whereNotNull('parent_id')->get();
        
        if ($users->isEmpty() || $categories->isEmpty()) {
            $this->command->warn('No users or categories found. Please run UsersSeeder and CategoriesSeeder first.');
            return;
        }

        $titles = [
            'Classic Black Suit',
            'Designer Leather Jacket',
            'Formal White Shirt',
            'Casual Blue Jeans',
            'Winter Coat',
            'Summer T-Shirt',
            'Elegant Dress',
            'Sports Shoes',
            'Business Suit',
            'Traditional Abaya',
            'Evening Gown',
            'Children\'s Outfit',
            'Vintage Jacket',
            'Modern Blazer',
            'Casual Shorts',
        ];

        $descriptions = [
            'Perfect condition, barely worn. High quality material.',
            'Excellent quality, well maintained. Original packaging included.',
            'Like new condition. Great value for money.',
            'Good condition with minor wear. Still in great shape.',
            'Brand new, never worn. Tags still attached.',
            'Well cared for item. Ready to use.',
            'Excellent quality item. Perfect for special occasions.',
            'Comfortable and stylish. Great addition to any wardrobe.',
            'Classic design, timeless style. Very well maintained.',
            'Beautiful item in perfect condition.',
        ];

        $operationTypes = [OperationType::SELL, OperationType::RENT, OperationType::DONATE];
        
        // Create 30 items
        for ($i = 0; $i < 30; $i++) {
            $user = $users->random();
            $category = $categories->random();
            $operationType = $operationTypes[array_rand($operationTypes)];
            $title = $titles[array_rand($titles)] . ' ' . ($i + 1);
            
            $item = Item::create([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'operation_type' => $operationType,
                'title' => $title,
                'slug' => Str::slug($title) . '-' . ($i + 1),
                'description' => $descriptions[array_rand($descriptions)],
                'price' => $operationType !== OperationType::DONATE ? rand(50, 1000) + (rand(0, 99) / 100) : null,
                'deposit_amount' => $operationType === OperationType::RENT ? rand(50, 500) + (rand(0, 99) / 100) : null,
                'is_available' => true,
                'availability_status' => ItemAvailability::AVAILABLE,
            ]);

            // Create approval record (most items approved, some pending)
            $approvalStatus = rand(1, 10) <= 8 ? ApprovalStatus::APPROVED : ApprovalStatus::PENDING;
            
            Approval::create([
                'approvable_type' => Item::class,
                'approvable_id' => $item->id,
                'status' => $approvalStatus,
                'submitted_by' => $user->id,
                'reviewed_by' => $approvalStatus === ApprovalStatus::APPROVED ? User::where('email', 'admin@khezana.com')->first()?->id : null,
                'reviewed_at' => $approvalStatus === ApprovalStatus::APPROVED ? now()->subDays(rand(1, 30)) : null,
            ]);
        }

        $this->command->info('Created 30 items with approval records.');
    }
}
