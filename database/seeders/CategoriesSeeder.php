<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Root Categories
        $men = Category::firstOrCreate(
            ['slug' => 'men'],
            [
                'name' => 'Men',
                'description' => 'Men\'s clothing and accessories',
                'is_active' => true,
            ]
        );

        $women = Category::firstOrCreate(
            ['slug' => 'women'],
            [
                'name' => 'Women',
                'description' => 'Women\'s clothing and accessories',
                'is_active' => true,
            ]
        );

        $kids = Category::firstOrCreate(
            ['slug' => 'kids'],
            [
                'name' => 'Kids',
                'description' => 'Children\'s clothing and accessories',
                'is_active' => true,
            ]
        );

        // Men Subcategories
        $menSuits = Category::firstOrCreate(
            ['slug' => 'men-suits'],
            [
                'name' => 'Suits',
                'parent_id' => $men->id,
                'description' => 'Men\'s suits and formal wear',
                'is_active' => true,
            ]
        );

        $menShoes = Category::firstOrCreate(
            ['slug' => 'men-shoes'],
            [
                'name' => 'Shoes',
                'parent_id' => $men->id,
                'description' => 'Men\'s footwear',
                'is_active' => true,
            ]
        );

        $menShirts = Category::firstOrCreate(
            ['slug' => 'men-shirts'],
            [
                'name' => 'Shirts',
                'parent_id' => $men->id,
                'description' => 'Men\'s shirts and tops',
                'is_active' => true,
            ]
        );

        // Women Subcategories
        $womenDresses = Category::firstOrCreate(
            ['slug' => 'women-dresses'],
            [
                'name' => 'Dresses',
                'parent_id' => $women->id,
                'description' => 'Women\'s dresses',
                'is_active' => true,
            ]
        );

        $womenAbayas = Category::firstOrCreate(
            ['slug' => 'women-abayas'],
            [
                'name' => 'Abayas',
                'parent_id' => $women->id,
                'description' => 'Traditional abayas',
                'is_active' => true,
            ]
        );

        $womenShoes = Category::firstOrCreate(
            ['slug' => 'women-shoes'],
            [
                'name' => 'Shoes',
                'parent_id' => $women->id,
                'description' => 'Women\'s footwear',
                'is_active' => true,
            ]
        );

        // Kids Subcategories
        $kidsBoys = Category::firstOrCreate(
            ['slug' => 'kids-boys'],
            [
                'name' => 'Boys',
                'parent_id' => $kids->id,
                'description' => 'Boys\' clothing',
                'is_active' => true,
            ]
        );

        $kidsGirls = Category::firstOrCreate(
            ['slug' => 'kids-girls'],
            [
                'name' => 'Girls',
                'parent_id' => $kids->id,
                'description' => 'Girls\' clothing',
                'is_active' => true,
            ]
        );
    }
}
