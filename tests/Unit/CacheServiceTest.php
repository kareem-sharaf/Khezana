<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\Cache\CacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CacheServiceTest extends TestCase
{
    use RefreshDatabase;

    private CacheService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(CacheService::class);
    }

    public function test_get_items_index_key_format(): void
    {
        $key = $this->service->getItemsIndexKey([], 'created_at_desc', 1, 'ar', null);
        $this->assertStringContainsString('items:index', $key);
        $this->assertStringContainsString('ar', $key);
        $this->assertStringContainsString('guest', $key);
    }

    public function test_get_item_show_key_format(): void
    {
        $key = $this->service->getItemShowKey(1, null, null, 'ar');
        $this->assertStringContainsString('item:1', $key);
        $this->assertStringContainsString('guest', $key);
    }

    public function test_invalidate_item_does_not_throw(): void
    {
        $this->service->invalidateItem(999);
        $this->addToAssertionCount(1);
    }
}
