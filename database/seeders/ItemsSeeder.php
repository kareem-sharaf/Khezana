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
use Illuminate\Support\Str;

class ItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create a user for items
        $user = User::firstOrCreate(
            ['email' => 'user@khezana.com'],
            [
                'name' => 'Ù…Ø³ØªØ®Ø¯Ù… Ø¹Ø§Ø¯ÙŠ',
                'password' => bcrypt('password'),
                'status' => 'active',
            ]
        );

        // Get categories
        $categories = Category::all();
        
        if ($categories->isEmpty()) {
            $this->command->warn('No categories found. Please run CategoriesSeeder first.');
            return;
        }

        // Get branches
        $branches = Branch::active()->get();
        $branchIds = $branches->pluck('id')->toArray();

        // Sample clothing items data with real Damascus locations
        $itemsData = [
            // Men's Clothing - In Branches
            [
                'title' => 'Ø¨Ø¯Ù„Ø© Ø±Ø¬Ø§Ù„ÙŠØ© Ø£Ù†ÙŠÙ‚Ø© Ø¨Ø§Ù„Ù„ÙˆÙ† Ø§Ù„Ø£Ø²Ø±Ù‚',
                'description' => 'Ø¨Ø¯Ù„Ø© Ø±Ø¬Ø§Ù„ÙŠØ© Ø£Ù†ÙŠÙ‚Ø© Ø¨Ø§Ù„Ù„ÙˆÙ† Ø§Ù„Ø£Ø²Ø±Ù‚ Ø§Ù„Ø¯Ø§ÙƒÙ†ØŒ Ù…Ù†Ø§Ø³Ø¨Ø© Ù„Ù„Ù…Ù†Ø§Ø³Ø¨Ø§Øª Ø§Ù„Ø±Ø³Ù…ÙŠØ©. Ø§Ù„Ù…Ù‚Ø§Ø³ 50ØŒ Ø¨Ø­Ø§Ù„Ø© Ù…Ù…ØªØ§Ø²Ø©.',
                'operation_type' => OperationType::SELL,
                'price' => 150000,
                'condition' => 'used',
                'governorate' => 'damascus',
                'category_slug' => 'men-suits',
                'branch_code' => 'MZE', // ÙØ±Ø¹ Ø§Ù„Ù…Ø²Ø©
            ],
            [
                'title' => 'Ù‚Ù…ÙŠØµ Ø±Ø¬Ø§Ù„ÙŠ Ø£Ø¨ÙŠØ¶ ÙƒÙ„Ø§Ø³ÙŠÙƒÙŠ',
                'description' => 'Ù‚Ù…ÙŠØµ Ø±Ø¬Ø§Ù„ÙŠ Ø£Ø¨ÙŠØ¶ ÙƒÙ„Ø§Ø³ÙŠÙƒÙŠ Ù…Ù† Ø§Ù„Ù‚Ø·Ù† Ø¹Ø§Ù„ÙŠ Ø§Ù„Ø¬ÙˆØ¯Ø©. Ø§Ù„Ù…Ù‚Ø§Ø³ LØŒ Ù…Ù†Ø§Ø³Ø¨ Ù„Ù„Ø¹Ù…Ù„ ÙˆØ§Ù„Ù…Ù†Ø§Ø³Ø¨Ø§Øª.',
                'operation_type' => OperationType::SELL,
                'price' => 25000,
                'condition' => 'new',
                'governorate' => 'damascus',
                'category_slug' => 'men-shirts',
                'branch_code' => 'SHL', // ÙØ±Ø¹ Ø§Ù„Ø´Ø¹Ù„Ø§Ù†
            ],
            [
                'title' => 'Ø­Ø°Ø§Ø¡ Ø±Ø¬Ø§Ù„ÙŠ Ø¬Ù„Ø¯ Ø£Ø³ÙˆØ¯',
                'description' => 'Ø­Ø°Ø§Ø¡ Ø±Ø¬Ø§Ù„ÙŠ Ø¬Ù„Ø¯ Ø£Ø³ÙˆØ¯ Ø£Ù†ÙŠÙ‚ØŒ Ù…Ù†Ø§Ø³Ø¨ Ù„Ù„Ø¹Ù…Ù„ ÙˆØ§Ù„Ù…Ù†Ø§Ø³Ø¨Ø§Øª Ø§Ù„Ø±Ø³Ù…ÙŠØ©. Ø§Ù„Ù…Ù‚Ø§Ø³ 42ØŒ Ø¨Ø­Ø§Ù„Ø© Ø¬ÙŠØ¯Ø© Ø¬Ø¯Ø§Ù‹.',
                'operation_type' => OperationType::SELL,
                'price' => 80000,
                'condition' => 'used',
                'governorate' => 'damascus',
                'category_slug' => 'men-shoes',
                'branch_code' => 'MLK', // ÙØ±Ø¹ Ø§Ù„Ù…Ø§Ù„ÙƒÙŠ
            ],
            [
                'title' => 'Ø¬Ø§ÙƒÙŠØª Ø±Ø¬Ø§Ù„ÙŠ Ø´ØªÙˆÙŠ',
                'description' => 'Ø¬Ø§ÙƒÙŠØª Ø±Ø¬Ø§Ù„ÙŠ Ø´ØªÙˆÙŠ Ø¯Ø§ÙØ¦ØŒ Ù…Ù† Ø§Ù„ØµÙˆÙ Ø§Ù„Ø·Ø¨ÙŠØ¹ÙŠ. Ø§Ù„Ù…Ù‚Ø§Ø³ XLØŒ Ù…Ù†Ø§Ø³Ø¨ Ù„Ù„Ø·Ù‚Ø³ Ø§Ù„Ø¨Ø§Ø±Ø¯.',
                'operation_type' => OperationType::RENT,
                'price' => 15000,
                'deposit_amount' => 30000,
                'condition' => 'used',
                'governorate' => 'damascus',
                'category_slug' => 'men',
                'branch_code' => 'BTM', // ÙØ±Ø¹ Ø¨Ø§Ø¨ ØªÙˆÙ…Ø§
            ],
            // Women's Clothing - Some in branches, some with sellers
            [
                'title' => 'ÙØ³ØªØ§Ù† Ù†Ø³Ø§Ø¦ÙŠ Ø£Ù†ÙŠÙ‚ Ø¨Ø§Ù„Ù„ÙˆÙ† Ø§Ù„Ø£Ø­Ù…Ø±',
                'description' => 'ÙØ³ØªØ§Ù† Ù†Ø³Ø§Ø¦ÙŠ Ø£Ù†ÙŠÙ‚ Ø¨Ø§Ù„Ù„ÙˆÙ† Ø§Ù„Ø£Ø­Ù…Ø±ØŒ Ù…Ù†Ø§Ø³Ø¨ Ù„Ù„Ù…Ù†Ø§Ø³Ø¨Ø§Øª ÙˆØ§Ù„Ø§Ø­ØªÙØ§Ù„Ø§Øª. Ø§Ù„Ù…Ù‚Ø§Ø³ MØŒ Ø¨Ø­Ø§Ù„Ø© Ù…Ù…ØªØ§Ø²Ø©.',
                'operation_type' => OperationType::SELL,
                'price' => 120000,
                'condition' => 'used',
                'governorate' => 'damascus',
                'category_slug' => 'women-dresses',
                'branch_code' => 'MZE', // ÙØ±Ø¹ Ø§Ù„Ù…Ø²Ø©
            ],
            [
                'title' => 'Ø¹Ø¨Ø§Ø¡Ø© Ø³ÙˆØ¯Ø§Ø¡ ØªÙ‚Ù„ÙŠØ¯ÙŠØ©',
                'description' => 'Ø¹Ø¨Ø§Ø¡Ø© Ø³ÙˆØ¯Ø§Ø¡ ØªÙ‚Ù„ÙŠØ¯ÙŠØ© Ø£Ù†ÙŠÙ‚Ø©ØŒ Ù…Ù† Ø§Ù„Ù‚Ù…Ø§Ø´ Ø¹Ø§Ù„ÙŠ Ø§Ù„Ø¬ÙˆØ¯Ø©. Ø§Ù„Ù…Ù‚Ø§Ø³ ÙƒØ¨ÙŠØ±ØŒ Ø¨Ø­Ø§Ù„Ø© Ø¬ÙŠØ¯Ø© Ø¬Ø¯Ø§Ù‹.',
                'operation_type' => OperationType::SELL,
                'price' => 100000,
                'condition' => 'used',
                'governorate' => 'damascus',
                'category_slug' => 'women-abayas',
                'branch_code' => null, // Ù„Ø¯Ù‰ Ø§Ù„Ø¨Ø§Ø¦Ø¹
                'approval_status' => ApprovalStatus::VERIFICATION_REQUIRED,
                'verification_message' => __('approvals.messages.verification_required'),
            ],
            [
                'title' => 'Ø­Ø°Ø§Ø¡ Ù†Ø³Ø§Ø¦ÙŠ ÙƒØ¹Ø¨ Ø¹Ø§Ù„ÙŠ',
                'description' => 'Ø­Ø°Ø§Ø¡ Ù†Ø³Ø§Ø¦ÙŠ ÙƒØ¹Ø¨ Ø¹Ø§Ù„ÙŠ Ø£Ù†ÙŠÙ‚ Ø¨Ø§Ù„Ù„ÙˆÙ† Ø§Ù„Ø£Ø³ÙˆØ¯. Ø§Ù„Ù…Ù‚Ø§Ø³ 38ØŒ Ù…Ù†Ø§Ø³Ø¨ Ù„Ù„Ù…Ù†Ø§Ø³Ø¨Ø§Øª.',
                'operation_type' => OperationType::SELL,
                'price' => 60000,
                'condition' => 'new',
                'governorate' => 'damascus',
                'category_slug' => 'women-shoes',
                'branch_code' => 'SHL', // ÙØ±Ø¹ Ø§Ù„Ø´Ø¹Ù„Ø§Ù†
            ],
            [
                'title' => 'Ø¨Ù„ÙˆØ²Ø© Ù†Ø³Ø§Ø¦ÙŠØ© Ø¨ÙŠØ¶Ø§Ø¡',
                'description' => 'Ø¨Ù„ÙˆØ²Ø© Ù†Ø³Ø§Ø¦ÙŠØ© Ø¨ÙŠØ¶Ø§Ø¡ Ø£Ù†ÙŠÙ‚Ø© Ù…Ù† Ø§Ù„Ù‚Ø·Ù†. Ø§Ù„Ù…Ù‚Ø§Ø³ SØŒ Ù…Ù†Ø§Ø³Ø¨Ø© Ù„Ù„Ø¹Ù…Ù„ ÙˆØ§Ù„ÙŠÙˆÙ…ÙŠ.',
                'operation_type' => OperationType::RENT,
                'price' => 10000,
                'deposit_amount' => 20000,
                'condition' => 'new',
                'governorate' => 'damascus',
                'category_slug' => 'women',
                'branch_code' => null, // Ù„Ø¯Ù‰ Ø§Ù„Ø¨Ø§Ø¦Ø¹
                'approval_status' => ApprovalStatus::VERIFICATION_REQUIRED,
                'verification_message' => __('approvals.messages.verification_required'),
            ],
            // Kids Clothing - Mix
            [
                'title' => 'Ù‚Ù…ÙŠØµ Ø£ÙˆÙ„Ø§Ø¯ Ø£Ø²Ø±Ù‚',
                'description' => 'Ù‚Ù…ÙŠØµ Ø£ÙˆÙ„Ø§Ø¯ Ø£Ø²Ø±Ù‚ Ø£Ù†ÙŠÙ‚ Ù…Ù† Ø§Ù„Ù‚Ø·Ù†. Ø§Ù„Ù…Ù‚Ø§Ø³ 10 Ø³Ù†ÙˆØ§ØªØŒ Ù…Ù†Ø§Ø³Ø¨ Ù„Ù„Ù…Ø¯Ø±Ø³Ø© ÙˆØ§Ù„ÙŠÙˆÙ…ÙŠ.',
                'operation_type' => OperationType::SELL,
                'price' => 15000,
                'condition' => 'used',
                'governorate' => 'damascus',
                'category_slug' => 'kids-boys',
                'branch_code' => 'JRM', // ÙØ±Ø¹ Ø¬Ø±Ù…Ø§Ù†Ø§
            ],
            [
                'title' => 'ÙØ³ØªØ§Ù† Ø¨Ù†Ø§Øª ÙˆØ±Ø¯ÙŠ',
                'description' => 'ÙØ³ØªØ§Ù† Ø¨Ù†Ø§Øª ÙˆØ±Ø¯ÙŠ Ø¬Ù…ÙŠÙ„ØŒ Ù…Ù†Ø§Ø³Ø¨ Ù„Ù„Ù…Ù†Ø§Ø³Ø¨Ø§Øª. Ø§Ù„Ù…Ù‚Ø§Ø³ 8 Ø³Ù†ÙˆØ§ØªØŒ Ø¨Ø­Ø§Ù„Ø© Ù…Ù…ØªØ§Ø²Ø©.',
                'operation_type' => OperationType::SELL,
                'price' => 20000,
                'condition' => 'used',
                'governorate' => 'damascus',
                'category_slug' => 'kids-girls',
                'branch_code' => 'BTM', // ÙØ±Ø¹ Ø¨Ø§Ø¨ ØªÙˆÙ…Ø§
            ],
            [
                'title' => 'Ø¬ÙŠÙ†Ø² Ø£ÙˆÙ„Ø§Ø¯ Ø£Ø²Ø±Ù‚',
                'description' => 'Ø¨Ù†Ø·Ù„ÙˆÙ† Ø¬ÙŠÙ†Ø² Ø£ÙˆÙ„Ø§Ø¯ Ø£Ø²Ø±Ù‚ ÙƒÙ„Ø§Ø³ÙŠÙƒÙŠ. Ø§Ù„Ù…Ù‚Ø§Ø³ 12 Ø³Ù†Ø©ØŒ Ù…Ù‚Ø§ÙˆÙ… Ù„Ù„Ø¨Ù„Ù‰.',
                'operation_type' => OperationType::DONATE,
                'price' => null,
                'condition' => 'used',
                'governorate' => 'damascus',
                'category_slug' => 'kids-boys',
                'branch_code' => null, // Ù„Ø¯Ù‰ Ø§Ù„Ø¨Ø§Ø¦Ø¹ - ØªØ¨Ø±Ø¹
                'approval_status' => ApprovalStatus::VERIFICATION_REQUIRED,
                'verification_message' => __('approvals.messages.verification_required'),
            ],
            [
                'title' => 'Ø­Ø°Ø§Ø¡ Ø£Ø·ÙØ§Ù„ Ø±ÙŠØ§Ø¶ÙŠ',
                'description' => 'Ø­Ø°Ø§Ø¡ Ø£Ø·ÙØ§Ù„ Ø±ÙŠØ§Ø¶ÙŠ Ù…Ø±ÙŠØ­ØŒ Ù…Ù†Ø§Ø³Ø¨ Ù„Ù„Ø¹Ø¨ ÙˆØ§Ù„Ø±ÙŠØ§Ø¶Ø©. Ø§Ù„Ù…Ù‚Ø§Ø³ 30ØŒ Ø¨Ø­Ø§Ù„Ø© Ø¬ÙŠØ¯Ø©.',
                'operation_type' => OperationType::SELL,
                'price' => 30000,
                'condition' => 'used',
                'governorate' => 'damascus',
                'category_slug' => 'kids',
                'branch_code' => 'MLK', // ÙØ±Ø¹ Ø§Ù„Ù…Ø§Ù„ÙƒÙŠ
            ],
            // Additional items
            [
                'title' => 'Ù…Ø¹Ø·Ù Ø±Ø¬Ø§Ù„ÙŠ ØµÙˆÙ Ø±Ù…Ø§Ø¯ÙŠ',
                'description' => 'Ù…Ø¹Ø·Ù Ø±Ø¬Ø§Ù„ÙŠ Ù…Ù† Ø§Ù„ØµÙˆÙ Ø§Ù„Ø·Ø¨ÙŠØ¹ÙŠ Ø¨Ø§Ù„Ù„ÙˆÙ† Ø§Ù„Ø±Ù…Ø§Ø¯ÙŠ. Ø§Ù„Ù…Ù‚Ø§Ø³ LØŒ Ù…Ø«Ø§Ù„ÙŠ Ù„Ù„Ø´ØªØ§Ø¡.',
                'operation_type' => OperationType::SELL,
                'price' => 200000,
                'condition' => 'new',
                'governorate' => 'damascus',
                'category_slug' => 'men',
                'branch_code' => 'MZE',
            ],
            [
                'title' => 'ØªÙ†ÙˆØ±Ø© Ù†Ø³Ø§Ø¦ÙŠØ© Ø³ÙˆØ¯Ø§Ø¡',
                'description' => 'ØªÙ†ÙˆØ±Ø© Ù†Ø³Ø§Ø¦ÙŠØ© Ø³ÙˆØ¯Ø§Ø¡ Ø£Ù†ÙŠÙ‚Ø©ØŒ Ù…Ù†Ø§Ø³Ø¨Ø© Ù„Ù„Ø¹Ù…Ù„. Ø§Ù„Ù…Ù‚Ø§Ø³ M.',
                'operation_type' => OperationType::SELL,
                'price' => 35000,
                'condition' => 'used',
                'governorate' => 'damascus',
                'category_slug' => 'women',
                'branch_code' => 'SHL',
            ],
            [
                'title' => 'Ø¨ÙŠØ¬Ø§Ù…Ø§ Ø£Ø·ÙØ§Ù„ Ù‚Ø·Ù†ÙŠØ©',
                'description' => 'Ø¨ÙŠØ¬Ø§Ù…Ø§ Ø£Ø·ÙØ§Ù„ Ù‚Ø·Ù†ÙŠØ© Ù†Ø§Ø¹Ù…Ø© ÙˆÙ…Ø±ÙŠØ­Ø©. Ø§Ù„Ù…Ù‚Ø§Ø³ 6 Ø³Ù†ÙˆØ§Øª.',
                'operation_type' => OperationType::DONATE,
                'price' => null,
                'condition' => 'used',
                'governorate' => 'damascus',
                'category_slug' => 'kids',
                'branch_code' => null,
                'approval_status' => ApprovalStatus::VERIFICATION_REQUIRED,
                'verification_message' => __('approvals.messages.verification_required'),
            ],
        ];

        // Use the same image path for all items
        $imagePath = 'items/wardrobe-background.jpg';
        $imagePath2 = 'items/wardrobe-background-2.jpg';

        // Create items
        foreach ($itemsData as $index => $itemData) {
            // Get category by slug
            $category = Category::where('slug', $itemData['category_slug'])->first();
            
            if (!$category) {
                $this->command->warn("Category not found: {$itemData['category_slug']}. Skipping item: {$itemData['title']}");
                continue;
            }

            // Get branch by code
            $branchId = null;
            if (!empty($itemData['branch_code'])) {
                $branch = Branch::where('code', $itemData['branch_code'])->first();
                $branchId = $branch?->id;
            }

            $item = Item::create([
                'user_id' => $user->id,
                'branch_id' => $branchId,
                'category_id' => $category->id,
                'operation_type' => $itemData['operation_type'],
                'title' => $itemData['title'],
                'slug' => Str::slug($itemData['title']) . '-' . Str::random(6),
                'description' => $itemData['description'],
                'governorate' => $itemData['governorate'],
                'condition' => $itemData['condition'],
                'price' => $itemData['price'] ?? null,
                'deposit_amount' => $itemData['deposit_amount'] ?? null,
                'is_available' => true,
                'availability_status' => ItemAvailability::AVAILABLE,
            ]);

            // Create at least 2 images for each item
            ItemImage::create([
                'item_id' => $item->id,
                'path' => $imagePath,
                'path_webp' => null,
                'disk' => 'public',
                'is_primary' => true,
            ]);

            ItemImage::create([
                'item_id' => $item->id,
                'path' => $imagePath2,
                'path_webp' => null,
                'disk' => 'public',
                'is_primary' => false,
            ]);

            // Add a third image for some items (randomly)
            if (rand(0, 1)) {
                ItemImage::create([
                    'item_id' => $item->id,
                    'path' => $imagePath,
                    'path_webp' => null,
                    'disk' => 'public',
                    'is_primary' => false,
                ]);
            }
            // Create approval record for the item
            $approvalStatus = $itemData['approval_status'] ?? ApprovalStatus::APPROVED;
            $approvalPayload = [
                'approvable_type' => Item::class,
                'approvable_id' => $item->id,
                'status' => $approvalStatus,
                'submitted_by' => $user->id,
                'resubmission_count' => 0,
            ];

            if ($approvalStatus === ApprovalStatus::APPROVED) {
                $approvalPayload['reviewed_by'] = $user->id;
                $approvalPayload['reviewed_at'] = now();
                $approvalPayload['rejection_reason'] = null;
            }

            if ($approvalStatus === ApprovalStatus::VERIFICATION_REQUIRED) {
                $approvalPayload['reviewed_by'] = $user->id;
                $approvalPayload['reviewed_at'] = now();
                $approvalPayload['rejection_reason'] = $itemData['verification_message']
                    ?? __('approvals.messages.verification_required');
            }

            Approval::create($approvalPayload);

            $branchName = $branchId ? Branch::find($branchId)->name : 'Ù„Ø¯Ù‰ Ø§Ù„Ø¨Ø§Ø¦Ø¹';
            $this->command->info("Created item: {$item->title} - {$branchName}");
        }

        $this->command->info('Items seeded successfully!');
    }
}

