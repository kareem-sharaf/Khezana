<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_items_index_returns_200(): void
    {
        $r = $this->get(route('public.items.index'));
        $r->assertStatus(200);
    }

    public function test_public_items_index_with_search_param(): void
    {
        $r = $this->get(route('public.items.index', ['search' => 'test']));
        $r->assertStatus(200);
    }
}
