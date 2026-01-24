<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\OperationType;
use App\Models\Category;
use App\Services\ItemService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemServiceTest extends TestCase
{
    use RefreshDatabase;

    private ItemService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ItemService::class);
    }

    public function test_validate_operation_rules_sell_requires_price(): void
    {
        $cat = Category::create(['name' => 'Test', 'slug' => 'test', 'is_active' => true]);
        $data = [
            'operation_type' => 'sell',
            'title' => 'x',
            'category_id' => $cat->id,
            'price' => null,
        ];
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Validation failed');
        $this->service->validateOperationRules($data);
    }

    public function test_validate_operation_rules_sell_passes_with_price(): void
    {
        $cat = Category::create(['name' => 'Test', 'slug' => 'test', 'is_active' => true]);
        $data = [
            'operation_type' => 'sell',
            'title' => 'x',
            'category_id' => $cat->id,
            'price' => 10,
        ];
        $this->service->validateOperationRules($data);
        $this->addToAssertionCount(1);
    }

    public function test_validate_operation_rules_rent_requires_deposit(): void
    {
        $cat = Category::create(['name' => 'Test', 'slug' => 'test', 'is_active' => true]);
        $data = [
            'operation_type' => 'rent',
            'title' => 'x',
            'category_id' => $cat->id,
            'price' => 10,
            'deposit_amount' => null,
        ];
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Validation failed');
        $this->service->validateOperationRules($data);
    }

    public function test_validate_operation_rules_donate_allows_null_price(): void
    {
        $cat = Category::create(['name' => 'Test', 'slug' => 'test', 'is_active' => true]);
        $data = [
            'operation_type' => 'donate',
            'title' => 'x',
            'category_id' => $cat->id,
        ];
        $this->service->validateOperationRules($data);
        $this->addToAssertionCount(1);
    }
}
