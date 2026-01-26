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
                'name' => 'مستخدم عادي',
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
                'title' => 'بدلة رجالية أنيقة باللون الأزرق',
                'description' => 'بدلة رجالية أنيقة باللون الأزرق الداكن، مناسبة للمناسبات الرسمية. المقاس 50، بحالة ممتازة.',
                'operation_type' => OperationType::SELL,
                'price' => 150000,
                'condition' => 'used',
                'governorate' => 'damascus',
                'category_slug' => 'men-suits',
                'branch_code' => 'MZE', // فرع المزة
            ],
            [
                'title' => 'قميص رجالي أبيض كلاسيكي',
                'description' => 'قميص رجالي أبيض كلاسيكي من القطن عالي الجودة. المقاس L، مناسب للعمل والمناسبات.',
                'operation_type' => OperationType::SELL,
                'price' => 25000,
                'condition' => 'new',
                'governorate' => 'damascus',
                'category_slug' => 'men-shirts',
                'branch_code' => 'SHL', // فرع الشعلان
            ],
            [
                'title' => 'حذاء رجالي جلد أسود',
                'description' => 'حذاء رجالي جلد أسود أنيق، مناسب للعمل والمناسبات الرسمية. المقاس 42، بحالة جيدة جداً.',
                'operation_type' => OperationType::SELL,
                'price' => 80000,
                'condition' => 'used',
                'governorate' => 'damascus',
                'category_slug' => 'men-shoes',
                'branch_code' => 'MLK', // فرع المالكي
            ],
            [
                'title' => 'جاكيت رجالي شتوي',
                'description' => 'جاكيت رجالي شتوي دافئ، من الصوف الطبيعي. المقاس XL، مناسب للطقس البارد.',
                'operation_type' => OperationType::RENT,
                'price' => 15000,
                'deposit_amount' => 30000,
                'condition' => 'used',
                'governorate' => 'damascus',
                'category_slug' => 'men',
                'branch_code' => 'BTM', // فرع باب توما
            ],
            // Women's Clothing - Some in branches, some with sellers
            [
                'title' => 'فستان نسائي أنيق باللون الأحمر',
                'description' => 'فستان نسائي أنيق باللون الأحمر، مناسب للمناسبات والاحتفالات. المقاس M، بحالة ممتازة.',
                'operation_type' => OperationType::SELL,
                'price' => 120000,
                'condition' => 'used',
                'governorate' => 'damascus',
                'category_slug' => 'women-dresses',
                'branch_code' => 'MZE', // فرع المزة
            ],
            [
                'title' => 'عباءة سوداء تقليدية',
                'description' => 'عباءة سوداء تقليدية أنيقة، من القماش عالي الجودة. المقاس كبير، بحالة جيدة جداً.',
                'operation_type' => OperationType::SELL,
                'price' => 100000,
                'condition' => 'used',
                'governorate' => 'damascus',
                'category_slug' => 'women-abayas',
                'branch_code' => null, // لدى البائع
            ],
            [
                'title' => 'حذاء نسائي كعب عالي',
                'description' => 'حذاء نسائي كعب عالي أنيق باللون الأسود. المقاس 38، مناسب للمناسبات.',
                'operation_type' => OperationType::SELL,
                'price' => 60000,
                'condition' => 'new',
                'governorate' => 'damascus',
                'category_slug' => 'women-shoes',
                'branch_code' => 'SHL', // فرع الشعلان
            ],
            [
                'title' => 'بلوزة نسائية بيضاء',
                'description' => 'بلوزة نسائية بيضاء أنيقة من القطن. المقاس S، مناسبة للعمل واليومي.',
                'operation_type' => OperationType::RENT,
                'price' => 10000,
                'deposit_amount' => 20000,
                'condition' => 'new',
                'governorate' => 'damascus',
                'category_slug' => 'women',
                'branch_code' => null, // لدى البائع
            ],
            // Kids Clothing - Mix
            [
                'title' => 'قميص أولاد أزرق',
                'description' => 'قميص أولاد أزرق أنيق من القطن. المقاس 10 سنوات، مناسب للمدرسة واليومي.',
                'operation_type' => OperationType::SELL,
                'price' => 15000,
                'condition' => 'used',
                'governorate' => 'damascus',
                'category_slug' => 'kids-boys',
                'branch_code' => 'JRM', // فرع جرمانا
            ],
            [
                'title' => 'فستان بنات وردي',
                'description' => 'فستان بنات وردي جميل، مناسب للمناسبات. المقاس 8 سنوات، بحالة ممتازة.',
                'operation_type' => OperationType::SELL,
                'price' => 20000,
                'condition' => 'used',
                'governorate' => 'damascus',
                'category_slug' => 'kids-girls',
                'branch_code' => 'BTM', // فرع باب توما
            ],
            [
                'title' => 'جينز أولاد أزرق',
                'description' => 'بنطلون جينز أولاد أزرق كلاسيكي. المقاس 12 سنة، مقاوم للبلى.',
                'operation_type' => OperationType::DONATE,
                'price' => null,
                'condition' => 'used',
                'governorate' => 'damascus',
                'category_slug' => 'kids-boys',
                'branch_code' => null, // لدى البائع - تبرع
            ],
            [
                'title' => 'حذاء أطفال رياضي',
                'description' => 'حذاء أطفال رياضي مريح، مناسب للعب والرياضة. المقاس 30، بحالة جيدة.',
                'operation_type' => OperationType::SELL,
                'price' => 30000,
                'condition' => 'used',
                'governorate' => 'damascus',
                'category_slug' => 'kids',
                'branch_code' => 'MLK', // فرع المالكي
            ],
            // Additional items
            [
                'title' => 'معطف رجالي صوف رمادي',
                'description' => 'معطف رجالي من الصوف الطبيعي باللون الرمادي. المقاس L، مثالي للشتاء.',
                'operation_type' => OperationType::SELL,
                'price' => 200000,
                'condition' => 'new',
                'governorate' => 'damascus',
                'category_slug' => 'men',
                'branch_code' => 'MZE',
            ],
            [
                'title' => 'تنورة نسائية سوداء',
                'description' => 'تنورة نسائية سوداء أنيقة، مناسبة للعمل. المقاس M.',
                'operation_type' => OperationType::SELL,
                'price' => 35000,
                'condition' => 'used',
                'governorate' => 'damascus',
                'category_slug' => 'women',
                'branch_code' => 'SHL',
            ],
            [
                'title' => 'بيجاما أطفال قطنية',
                'description' => 'بيجاما أطفال قطنية ناعمة ومريحة. المقاس 6 سنوات.',
                'operation_type' => OperationType::DONATE,
                'price' => null,
                'condition' => 'used',
                'governorate' => 'damascus',
                'category_slug' => 'kids',
                'branch_code' => null,
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

            // Create approved approval record for the item
            Approval::create([
                'approvable_type' => Item::class,
                'approvable_id' => $item->id,
                'status' => ApprovalStatus::APPROVED,
                'submitted_by' => $user->id,
                'reviewed_by' => $user->id,
                'reviewed_at' => now(),
                'resubmission_count' => 0,
            ]);

            $branchName = $branchId ? Branch::find($branchId)->name : 'لدى البائع';
            $this->command->info("Created item: {$item->title} - {$branchName}");
        }

        $this->command->info('Items seeded successfully!');
    }
}
