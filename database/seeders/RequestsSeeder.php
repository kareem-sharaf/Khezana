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
        $users = User::where('email', '!=', 'superadmin@khezana.com')
            ->where('email', '!=', 'admin@khezana.com')
            ->get();
        
        $categories = Category::whereNotNull('parent_id')->get();
        
        if ($users->isEmpty() || $categories->isEmpty()) {
            $this->command->warn('No users or categories found. Please run UsersSeeder and CategoriesSeeder first.');
            return;
        }

        $titles = [
            'Looking for Formal Suit',
            'Need Winter Coat',
            'Seeking Designer Dress',
            'Want Children\'s Clothes',
            'Looking for Traditional Abaya',
            'Need Sports Shoes',
            'Seeking Business Attire',
            'Want Casual Outfit',
            'Looking for Evening Wear',
            'Need Summer Clothes',
            'Seeking Vintage Items',
            'Want Modern Fashion',
            'Looking for Special Occasion Outfit',
            'Need Everyday Clothes',
            'Seeking High Quality Items',
        ];

        $descriptions = [
            'I am looking for a high-quality item in good condition. Please contact me if you have something suitable.',
            'Seeking a well-maintained item. Budget is flexible for the right piece.',
            'Looking for something specific. Must be in excellent condition.',
            'Need this item urgently. Willing to pay fair price for quality item.',
            'Interested in purchasing or renting. Please reach out if you have what I need.',
            'Looking for a specific style and size. Open to offers.',
            'Seeking quality item for special occasion. Price negotiable.',
            'Need this item soon. Please contact me with details.',
            'Looking for well-cared-for item. Willing to negotiate.',
            'Interested in finding the right item. Open to discussion.',
        ];

        $statuses = [RequestStatus::OPEN, RequestStatus::OPEN, RequestStatus::OPEN, RequestStatus::CLOSED];
        
        // Create 20 requests
        for ($i = 0; $i < 20; $i++) {
            $user = $users->random();
            $category = $categories->random();
            $title = $titles[array_rand($titles)] . ' ' . ($i + 1);
            $status = $statuses[array_rand($statuses)];
            
            $request = Request::create([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'title' => $title,
                'slug' => Str::slug($title) . '-' . ($i + 1),
                'description' => $descriptions[array_rand($descriptions)],
                'status' => $status,
            ]);

            // Create approval record (most requests approved, some pending)
            $approvalStatus = rand(1, 10) <= 8 ? ApprovalStatus::APPROVED : ApprovalStatus::PENDING;
            
            Approval::create([
                'approvable_type' => Request::class,
                'approvable_id' => $request->id,
                'status' => $approvalStatus,
                'submitted_by' => $user->id,
                'reviewed_by' => $approvalStatus === ApprovalStatus::APPROVED ? User::where('email', 'admin@khezana.com')->first()?->id : null,
                'reviewed_at' => $approvalStatus === ApprovalStatus::APPROVED ? now()->subDays(rand(1, 30)) : null,
            ]);
        }

        $this->command->info('Created 20 requests with approval records.');
    }
}
