<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

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
     * Phase 1.4: Added image optimization (resize, compress)
     * 
     * @param UploadedFile $file The uploaded file
     * @param int $itemId The item ID for organizing files
     * @param string $disk Storage disk name (default: 'public')
     * @return array ['path' => string, 'disk' => string]
     * @throws \Exception If validation or processing fails
     */
    public function processAndStore(UploadedFile $file, int $itemId, string $disk = 'public'): array
    {
        $this->validateFile($file);

        try {
            // Phase 1.4: Optimize image using Intervention Image
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getRealPath());
            
            // Resize if image is larger than max dimensions
            if ($image->width() > self::MAX_WIDTH || $image->height() > self::MAX_HEIGHT) {
                $image->scale(width: self::MAX_WIDTH, height: self::MAX_HEIGHT);
            }
            
            // Generate unique filename (keep original extension for now, WebP conversion in Phase 3)
            $extension = strtolower($file->getClientOriginalExtension());
            $filename = \Illuminate\Support\Str::uuid() . '.' . $extension;
            $directory = "items/{$itemId}";
            $path = "{$directory}/{$filename}";
            
            // Save optimized image
            // For JPEG: use quality parameter
            // For PNG: quality doesn't apply, but we can optimize
            if (in_array($extension, ['jpg', 'jpeg'])) {
                $optimizedImage = $image->toJpeg(quality: self::QUALITY);
            } else {
                $optimizedImage = $image->encode();
            }
            
            Storage::disk($disk)->put($path, $optimizedImage);
            
            return [
                'path' => $path,
                'disk' => $disk,
            ];
        } catch (\Exception $e) {
            // Fallback to original file if optimization fails
            Log::warning('Image optimization failed, using original file', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName(),
            ]);
            
            // Store original file as fallback
            $extension = strtolower($file->getClientOriginalExtension());
            $filename = \Illuminate\Support\Str::uuid() . '.' . $extension;
            $directory = "items/{$itemId}";
            $path = "{$directory}/{$filename}";
            
            Storage::disk($disk)->putFileAs($directory, $file, $filename);
            
            return [
                'path' => $path,
                'disk' => $disk,
            ];
        }
    }

    /**
     * Phase 3.1: Process and store image from temp path (used by ProcessItemImagesJob)
     * Phase 3.3: Optionally create WebP variant; returns path_webp when created.
     *
     * @param string $tempPath Path relative to public disk (e.g. temp/xxx.jpg)
     * @param int $itemId Item ID
     * @param string $disk Storage disk
     * @return array{path: string, path_webp?: string, disk: string}
     */
    public function processAndStoreFromPath(string $tempPath, int $itemId, string $disk = 'public'): array
    {
        $fullPath = Storage::disk($disk)->path($tempPath);
        if (!is_file($fullPath)) {
            throw new \Exception("Temp file not found: {$tempPath}");
        }

        $size = filesize($fullPath);
        if ($size > self::MAX_FILE_SIZE) {
            @unlink($fullPath);
            throw new \Exception('Image size exceeds maximum allowed size (5MB)');
        }

        $mime = mime_content_type($fullPath) ?: '';
        if (!in_array(strtolower($mime), self::ALLOWED_MIMES)) {
            @unlink($fullPath);
            throw new \Exception('Invalid image type. Allowed: JPEG, PNG, WebP');
        }

        $ext = strtolower(pathinfo($tempPath, PATHINFO_EXTENSION)) ?: 'jpg';
        $uuid = (string) \Illuminate\Support\Str::uuid();
        $filename = $uuid . '.' . $ext;
        $directory = "items/{$itemId}";
        $path = "{$directory}/{$filename}";
        $pathWebp = null;

        try {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($fullPath);
            if ($image->width() > self::MAX_WIDTH || $image->height() > self::MAX_HEIGHT) {
                $image->scale(width: self::MAX_WIDTH, height: self::MAX_HEIGHT);
            }
            if (in_array($ext, ['jpg', 'jpeg'])) {
                $blob = $image->toJpeg(quality: self::QUALITY);
            } else {
                $blob = $image->encode();
            }
            Storage::disk($disk)->put($path, (string) $blob);

            try {
                $webpBlob = $image->toWebp(quality: self::QUALITY);
                $pathWebp = "{$directory}/{$uuid}.webp";
                Storage::disk($disk)->put($pathWebp, (string) $webpBlob);
            } catch (\Throwable $e) {
                Log::debug('WebP conversion skipped', ['error' => $e->getMessage(), 'path' => $path]);
            }
        } catch (\Throwable $e) {
            Log::warning('Image optimization failed in job', ['error' => $e->getMessage(), 'temp' => $tempPath]);
            Storage::disk($disk)->put($path, file_get_contents($fullPath));
        }
        @unlink($fullPath);

        $result = ['path' => $path, 'disk' => $disk];
        if ($pathWebp !== null) {
            $result['path_webp'] = $pathWebp;
        }
        return $result;
    }

    /**
     * Validate uploaded file (public for use when storing to temp before queue)
     */
    public function validateFile(UploadedFile $file): void
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
