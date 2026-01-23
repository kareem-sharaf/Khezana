<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

/**
 * Image Optimization Service
 * 
 * Handles image validation, storage, and URL generation
 * Supports multiple storage disks (local, s3, etc.)
 * Ready for future image optimization with Intervention Image
 */
class ImageOptimizationService
{
    private const MAX_WIDTH = 1920;
    private const MAX_HEIGHT = 1920;
    private const QUALITY = 85;
    private const ALLOWED_MIMES = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
    private const MAX_FILE_SIZE = 5242880; // 5MB

    /**
     * Process and store uploaded image
     * 
     * @param UploadedFile $file The uploaded file
     * @param int $itemId The item ID for organizing files
     * @param string $disk Storage disk name (default: 'public')
     * @return array ['path' => string, 'disk' => string]
     * @throws \Exception If validation or processing fails
     */
    public function processAndStore(UploadedFile $file, int $itemId, string $disk = 'public'): array
    {
        // Validate file
        $this->validateFile($file);

        // Generate unique filename
        $extension = strtolower($file->getClientOriginalExtension());
        $filename = \Illuminate\Support\Str::uuid() . '.' . $extension;
        $directory = "items/{$itemId}";
        $path = "{$directory}/{$filename}";

        // Store file using Laravel Storage
        Storage::disk($disk)->putFileAs($directory, $file, $filename);

        return [
            'path' => $path,
            'disk' => $disk,
        ];
    }

    /**
     * Validate uploaded file
     */
    private function validateFile(UploadedFile $file): void
    {
        // Check MIME type
        $mimeType = strtolower($file->getMimeType());
        if (!in_array($mimeType, self::ALLOWED_MIMES)) {
            throw new \Exception('Invalid image type. Allowed: JPEG, PNG, WebP');
        }

        // Check file size
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new \Exception('Image size exceeds maximum allowed size (5MB)');
        }
    }

    /**
     * Delete image from storage
     */
    public function delete(string $path, string $disk): bool
    {
        try {
            return Storage::disk($disk)->delete($path);
        } catch (\Exception $e) {
            Log::warning('Failed to delete image', [
                'path' => $path,
                'disk' => $disk,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get image URL
     */
    public function getUrl(string $path, string $disk): string
    {
        return Storage::disk($disk)->url($path);
    }

    /**
     * Check if image exists
     */
    public function exists(string $path, string $disk): bool
    {
        return Storage::disk($disk)->exists($path);
    }
}
