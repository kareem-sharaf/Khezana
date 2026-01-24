<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_requires_auth(): void
    {
        $r = $this->post(route('items.store'), []);
        $r->assertRedirect();
    }

    public function test_store_validates_required(): void
    {
        $user = User::factory()->create();
        $r = $this->actingAs($user)->post(route('items.store'), []);
        $r->assertSessionHasErrors();
    }

    public function test_store_creates_item_with_valid_data(): void
    {
        $user = User::factory()->create();
        $cat = Category::create(['name' => 'Test', 'slug' => 'test', 'is_active' => true]);
        $data = [
            'category_id' => $cat->id,
            'operation_type' => 'donate',
            'title' => 'Item one',
            'description' => 'Desc',
            'condition' => 'used',
        ];
        $r = $this->actingAs($user)->post(route('items.store'), $data);
        $r->assertRedirect();
        $this->assertDatabaseHas('items', ['title' => 'Item one', 'user_id' => $user->id]);
    }
}
