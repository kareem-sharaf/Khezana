<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\ItemImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ItemImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get only approved items that don't have images yet
        $items = Item::whereHas('approvalRelation', function($query) {
            $query->where('status', 'approved');
        })->whereDoesntHave('images')->get();

        if ($items->isEmpty()) {
            $this->command->warn('No items without images found. Skipping image seeding.');
            return;
        }

        // Ensure the items directory exists
        if (!Storage::disk('public')->exists('items')) {
            Storage::disk('public')->makeDirectory('items');
        }

        $imagesCreated = 0;
        $itemsProcessed = 0;

        foreach ($items as $item) {
            // Skip if item already has images
            if ($item->images()->count() > 0) {
                continue;
            }

            // Each item gets 1-4 images
            $imageCount = rand(1, 4);
            $itemImagesCreated = 0;

            for ($i = 0; $i < $imageCount; $i++) {
                try {
                    // Use Picsum Photos for random images
                    // Using different seeds for variety
                    $seed = $item->id * 100 + $i;
                    $width = 800;
                    $height = 1000; // 4:5 aspect ratio to match our design

                    // Download image from Picsum Photos with timeout
                    $imageUrl = "https://picsum.photos/seed/{$seed}/{$width}/{$height}";

                    $context = stream_context_create([
                        'http' => [
                            'timeout' => 10,
                            'user_agent' => 'Laravel Seeder'
                        ]
                    ]);

                    // Get image content
                    $imageContent = @file_get_contents($imageUrl, false, $context);

                    if ($imageContent === false || strlen($imageContent) < 1000) {
                        $this->command->warn("Failed to download image for item {$item->id}, image {$i}");
                        continue;
                    }

                    // Generate unique filename
                    $filename = 'items/' . Str::uuid() . '.jpg';

                    // Save image to storage
                    $saved = Storage::disk('public')->put($filename, $imageContent);

                    if (!$saved) {
                        $this->command->warn("Failed to save image for item {$item->id}, image {$i}");
                        continue;
                    }

                    // Create image record
                    ItemImage::create([
                        'item_id' => $item->id,
                        'path' => $filename,
                        'is_primary' => $i === 0, // First image is primary
                    ]);

                    $imagesCreated++;
                    $itemImagesCreated++;

                    // Small delay to avoid rate limiting
                    usleep(100000); // 0.1 second
                } catch (\Exception $e) {
                    $this->command->warn("Error creating image for item {$item->id}, image {$i}: " . $e->getMessage());
                    continue;
                }
            }

            if ($itemImagesCreated > 0) {
                $itemsProcessed++;
            }
        }

        $this->command->info("Created {$imagesCreated} images for {$itemsProcessed} items.");
    }
}
