<?php

namespace Database\Seeders;

use App\Enums\ApprovalStatus;
use App\Enums\RequestStatus;
use App\Models\Approval;
use App\Models\Category;
use App\Models\Request;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RequestsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->whereIn('name', ['admin', 'super_admin']);
        })->take(10)->get();

        if ($users->isEmpty()) {
            $this->command->warn('No regular users found. Please run UsersSeeder first.');
            return;
        }

        // Get categories
        $categories = Category::where('is_active', true)->get();

        if ($categories->isEmpty()) {
            $this->command->warn('No active categories found. Please run CategoriesSeeder first.');
            return;
        }

        // Sample requests data
        $requestsData = [
            [
                'title' => 'أبحث عن بدلة رجالية للزفاف',
                'description' => 'أبحث عن بدلة رجالية أنيقة للزفاف، يفضل اللون الأزرق الداكن أو الأسود. المقاس 50-52. بحالة جيدة.',
                'category_slug' => 'men-suits',
                'status' => RequestStatus::OPEN,
            ],
            [
                'title' => 'أحتاج فستان سهرة أحمر',
                'description' => 'أبحث عن فستان سهرة باللون الأحمر أو العنابي للمناسبات. المقاس M أو L. يفضل أن يكون طويل.',
                'category_slug' => 'women-dresses',
                'status' => RequestStatus::OPEN,
            ],
            [
                'title' => 'أبحث عن ملابس أطفال للمدرسة',
                'description' => 'أحتاج قمصان وبناطيل للأولاد عمر 8-10 سنوات للمدرسة. يفضل ألوان محايدة.',
                'category_slug' => 'kids-boys',
                'status' => RequestStatus::OPEN,
            ],
            [
                'title' => 'أحتاج حذاء رياضي نايكي أو أديداس',
                'description' => 'أبحث عن حذاء رياضي ماركة معروفة، المقاس 43. يفضل أن يكون بحالة جيدة أو جديد.',
                'category_slug' => 'men-shoes',
                'status' => RequestStatus::OPEN,
            ],
            [
                'title' => 'أبحث عن عباءة للحج',
                'description' => 'أحتاج عباءة بيضاء أو سوداء بسيطة للحج. المقاس كبير. يفضل قماش خفيف.',
                'category_slug' => 'women-abayas',
                'status' => RequestStatus::FULFILLED,
            ],
            [
                'title' => 'أحتاج فستان بنات للعيد',
                'description' => 'أبحث عن فستان أنيق للبنات عمر 5-6 سنوات للعيد. يفضل ألوان زاهية.',
                'category_slug' => 'kids-girls',
                'status' => RequestStatus::OPEN,
            ],
            [
                'title' => 'أبحث عن جاكيت جلد رجالي',
                'description' => 'أحتاج جاكيت جلد رجالي أسود أو بني. المقاس L أو XL. بحالة جيدة.',
                'category_slug' => 'men',
                'status' => RequestStatus::OPEN,
            ],
            [
                'title' => 'أحتاج شنطة يد نسائية',
                'description' => 'أبحث عن شنطة يد نسائية أنيقة للمناسبات. يفضل اللون الأسود أو البني.',
                'category_slug' => 'women',
                'status' => RequestStatus::CLOSED,
            ],
            [
                'title' => 'أبحث عن ملابس رضع',
                'description' => 'أحتاج ملابس أطفال رضع عمر 6-12 شهر. أي ألوان مقبولة.',
                'category_slug' => 'kids',
                'status' => RequestStatus::OPEN,
            ],
            [
                'title' => 'أحتاج قميص رجالي للعمل',
                'description' => 'أبحث عن قمصان رجالية رسمية للعمل. المقاس M. ألوان فاتحة يفضل.',
                'category_slug' => 'men-shirts',
                'status' => RequestStatus::OPEN,
            ],
            [
                'title' => 'أبحث عن معطف شتوي نسائي',
                'description' => 'أحتاج معطف شتوي نسائي دافئ. المقاس L. أي لون مقبول.',
                'category_slug' => 'women',
                'status' => RequestStatus::OPEN,
            ],
            [
                'title' => 'أحتاج حذاء كعب للزفاف',
                'description' => 'أبحث عن حذاء كعب عالي أبيض أو فضي للزفاف. المقاس 37-38.',
                'category_slug' => 'women-shoes',
                'status' => RequestStatus::OPEN,
            ],
        ];

        $this->command->info('Creating requests...');

        foreach ($requestsData as $requestData) {
            // Get random user
            $user = $users->random();

            // Get category
            $category = Category::where('slug', $requestData['category_slug'])->first();
            
            if (!$category) {
                // Try parent category
                $category = $categories->random();
            }

            $request = Request::create([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'title' => $requestData['title'],
                'slug' => Str::slug($requestData['title']) . '-' . Str::random(6),
                'description' => $requestData['description'],
                'status' => $requestData['status'],
            ]);

            // Create approval record
            Approval::create([
                'approvable_type' => Request::class,
                'approvable_id' => $request->id,
                'status' => ApprovalStatus::APPROVED,
                'submitted_by' => $user->id,
                'reviewed_by' => $user->id,
                'reviewed_at' => now(),
                'resubmission_count' => 0,
            ]);

            $this->command->info("Created request: {$request->title}");
        }

        $this->command->info('Requests seeded successfully!');
    }
}
