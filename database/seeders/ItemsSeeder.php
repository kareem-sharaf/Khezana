<?php

namespace Database\Seeders;

use App\Enums\ApprovalStatus;
use App\Enums\ItemAvailability;
use App\Enums\OperationType;
use App\Models\Approval;
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
                'name' => 'Regular User',
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

        // Sample clothing items data
        $itemsData = [
            // Men's Clothing
            [
                'title' => 'بدلة رجالية أنيقة باللون الأزرق',
                'description' => 'بدلة رجالية أنيقة باللون الأزرق الداكن، مناسبة للمناسبات الرسمية. المقاس 50، بحالة ممتازة.',
                'operation_type' => OperationType::SELL,
                'price' => 150000,
                'condition' => 'used',
                'governorate' => 'damascus',
                'category_slug' => 'men-suits',
            ],
            [
                'title' => 'قميص رجالي أبيض كلاسيكي',
                'description' => 'قميص رجالي أبيض كلاسيكي من القطن عالي الجودة. المقاس L، مناسب للعمل والمناسبات.',
                'operation_type' => OperationType::SELL,
                'price' => 25000,
                'condition' => 'new',
                'governorate' => 'aleppo',
                'category_slug' => 'men-shirts',
            ],
            [
                'title' => 'حذاء رجالي جلد أسود',
                'description' => 'حذاء رجالي جلد أسود أنيق، مناسب للعمل والمناسبات الرسمية. المقاس 42، بحالة جيدة جداً.',
                'operation_type' => OperationType::SELL,
                'price' => 80000,
                'condition' => 'used',
                'governorate' => 'homs',
                'category_slug' => 'men-shoes',
            ],
            [
                'title' => 'جاكيت رجالي شتوي',
                'description' => 'جاكيت رجالي شتوي دافئ، من الصوف الطبيعي. المقاس XL، مناسب للطقس البارد.',
                'operation_type' => OperationType::RENT,
                'price' => 15000,
                'deposit_amount' => 30000,
                'condition' => 'used',
                'governorate' => 'latakia',
                'category_slug' => 'men',
            ],
            // Women's Clothing
            [
                'title' => 'فستان نسائي أنيق باللون الأحمر',
                'description' => 'فستان نسائي أنيق باللون الأحمر، مناسب للمناسبات والاحتفالات. المقاس M، بحالة ممتازة.',
                'operation_type' => OperationType::SELL,
                'price' => 120000,
                'condition' => 'used',
                'governorate' => 'damascus',
                'category_slug' => 'women-dresses',
            ],
            [
                'title' => 'عباءة سوداء تقليدية',
                'description' => 'عباءة سوداء تقليدية أنيقة، من القماش عالي الجودة. المقاس كبير، بحالة جيدة جداً.',
                'operation_type' => OperationType::SELL,
                'price' => 100000,
                'condition' => 'used',
                'governorate' => 'aleppo',
                'category_slug' => 'women-abayas',
            ],
            [
                'title' => 'حذاء نسائي كعب عالي',
                'description' => 'حذاء نسائي كعب عالي أنيق باللون الأسود. المقاس 38، مناسب للمناسبات.',
                'operation_type' => OperationType::SELL,
                'price' => 60000,
                'condition' => 'new',
                'governorate' => 'homs',
                'category_slug' => 'women-shoes',
            ],
            [
                'title' => 'بلوزة نسائية بيضاء',
                'description' => 'بلوزة نسائية بيضاء أنيقة من القطن. المقاس S، مناسبة للعمل واليومي.',
                'operation_type' => OperationType::RENT,
                'price' => 10000,
                'deposit_amount' => 20000,
                'condition' => 'new',
                'governorate' => 'tartus',
                'category_slug' => 'women',
            ],
            // Kids Clothing
            [
                'title' => 'قميص أولاد أزرق',
                'description' => 'قميص أولاد أزرق أنيق من القطن. المقاس 10 سنوات، مناسب للمدرسة واليومي.',
                'operation_type' => OperationType::SELL,
                'price' => 15000,
                'condition' => 'used',
                'governorate' => 'damascus',
                'category_slug' => 'kids-boys',
            ],
            [
                'title' => 'فستان بنات وردي',
                'description' => 'فستان بنات وردي جميل، مناسب للمناسبات. المقاس 8 سنوات، بحالة ممتازة.',
                'operation_type' => OperationType::SELL,
                'price' => 20000,
                'condition' => 'used',
                'governorate' => 'aleppo',
                'category_slug' => 'kids-girls',
            ],
            [
                'title' => 'جينز أولاد أزرق',
                'description' => 'بنطلون جينز أولاد أزرق كلاسيكي. المقاس 12 سنة، مقاوم للبلى.',
                'operation_type' => OperationType::DONATE,
                'price' => null,
                'condition' => 'used',
                'governorate' => 'homs',
                'category_slug' => 'kids-boys',
            ],
            [
                'title' => 'حذاء أطفال رياضي',
                'description' => 'حذاء أطفال رياضي مريح، مناسب للعب والرياضة. المقاس 30، بحالة جيدة.',
                'operation_type' => OperationType::SELL,
                'price' => 30000,
                'condition' => 'used',
                'governorate' => 'latakia',
                'category_slug' => 'kids',
            ],
        ];

        // Use the same image path for all items
        // Using the existing image - same image for all products
        $imagePath = 'items/wardrobe-background.jpg';
        $imagePath2 = 'items/wardrobe-background-2.jpg'; // Second image (same file, different path reference)

        // Create items
        foreach ($itemsData as $index => $itemData) {
            // Get category by slug
            $category = Category::where('slug', $itemData['category_slug'])->first();
            
            if (!$category) {
                $this->command->warn("Category not found: {$itemData['category_slug']}. Skipping item: {$itemData['title']}");
                continue;
            }

            $item = Item::create([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'operation_type' => $itemData['operation_type'],
                'title' => $itemData['title'],
                'slug' => Str::slug($itemData['title']) . '-' . ($index + 1),
                'description' => $itemData['description'],
                'governorate' => $itemData['governorate'],
                'condition' => $itemData['condition'],
                'price' => $itemData['price'] ?? null,
                'deposit_amount' => $itemData['deposit_amount'] ?? null,
                'is_available' => true,
                'availability_status' => ItemAvailability::AVAILABLE,
            ]);

            // Create at least 2 images for each item (using the same image path)
            // First image (primary)
            ItemImage::create([
                'item_id' => $item->id,
                'path' => $imagePath,
                'path_webp' => null,
                'disk' => 'public',
                'is_primary' => true,
            ]);

            // Second image
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

            // Create approved approval record for the item
            Approval::create([
                'approvable_type' => Item::class,
                'approvable_id' => $item->id,
                'status' => ApprovalStatus::APPROVED,
                'submitted_by' => $user->id,
                'reviewed_by' => $user->id, // For seeder, use the same user
                'reviewed_at' => now(),
                'resubmission_count' => 0,
            ]);

            $this->command->info("Created item: {$item->title}");
        }

        $this->command->info('Items seeded successfully!');
    }
}
