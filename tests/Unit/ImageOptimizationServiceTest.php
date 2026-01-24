<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\ImageOptimizationService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageOptimizationServiceTest extends TestCase
{
    private ImageOptimizationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->service = app(ImageOptimizationService::class);
    }

    public function test_validate_file_rejects_invalid_mime(): void
    {
        $file = UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf');
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid image type');
        $this->service->validateFile($file);
    }

    public function test_validate_file_rejects_oversized(): void
    {
        if (!function_exists('imagecreatetruecolor')) {
            $this->markTestSkipped('GD extension required for image fake');
        }
        $file = UploadedFile::fake()->image('big.jpg')->size(6000);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Image size exceeds');
        $this->service->validateFile($file);
    }

    public function test_validate_file_accepts_valid_jpeg(): void
    {
        if (!function_exists('imagecreatetruecolor')) {
            $this->markTestSkipped('GD extension required for image fake');
        }
        $file = UploadedFile::fake()->image('ok.jpg', 100, 100)->size(100);
        $this->service->validateFile($file);
        $this->addToAssertionCount(1);
    }

    public function test_get_url_returns_string(): void
    {
        $url = $this->service->getUrl('items/1/x.jpg', 'public');
        $this->assertIsString($url);
        $this->assertNotEmpty($url);
    }
}
