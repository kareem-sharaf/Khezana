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
        $men = Category::updateOrCreate(
            ['slug' => 'men'],
            [
                'name' => 'رجال',
                'description' => 'ملابس وإكسسوارات رجالية',
                'is_active' => true,
            ]
        );

        $women = Category::updateOrCreate(
            ['slug' => 'women'],
            [
                'name' => 'نساء',
                'description' => 'ملابس وإكسسوارات نسائية',
                'is_active' => true,
            ]
        );

        $kids = Category::updateOrCreate(
            ['slug' => 'kids'],
            [
                'name' => 'أطفال',
                'description' => 'ملابس وإكسسوارات للأطفال',
                'is_active' => true,
            ]
        );

        // Men Subcategories
        $menSuits = Category::updateOrCreate(
            ['slug' => 'men-suits'],
            [
                'name' => 'بدلات',
                'parent_id' => $men->id,
                'description' => 'بدلات وملابس رسمية رجالية',
                'is_active' => true,
            ]
        );

        $menShoes = Category::updateOrCreate(
            ['slug' => 'men-shoes'],
            [
                'name' => 'أحذية',
                'parent_id' => $men->id,
                'description' => 'أحذية رجالية',
                'is_active' => true,
            ]
        );

        $menShirts = Category::updateOrCreate(
            ['slug' => 'men-shirts'],
            [
                'name' => 'قمصان',
                'parent_id' => $men->id,
                'description' => 'قمصان وبلوزات رجالية',
                'is_active' => true,
            ]
        );

        // Women Subcategories
        $womenDresses = Category::updateOrCreate(
            ['slug' => 'women-dresses'],
            [
                'name' => 'فساتين',
                'parent_id' => $women->id,
                'description' => 'فساتين نسائية',
                'is_active' => true,
            ]
        );

        $womenAbayas = Category::updateOrCreate(
            ['slug' => 'women-abayas'],
            [
                'name' => 'عباءات',
                'parent_id' => $women->id,
                'description' => 'عباءات تقليدية',
                'is_active' => true,
            ]
        );

        $womenShoes = Category::updateOrCreate(
            ['slug' => 'women-shoes'],
            [
                'name' => 'أحذية',
                'parent_id' => $women->id,
                'description' => 'أحذية نسائية',
                'is_active' => true,
            ]
        );

        // Kids Subcategories
        $kidsBoys = Category::updateOrCreate(
            ['slug' => 'kids-boys'],
            [
                'name' => 'أولاد',
                'parent_id' => $kids->id,
                'description' => 'ملابس أولاد',
                'is_active' => true,
            ]
        );

        $kidsGirls = Category::updateOrCreate(
            ['slug' => 'kids-girls'],
            [
                'name' => 'بنات',
                'parent_id' => $kids->id,
                'description' => 'ملابس بنات',
                'is_active' => true,
            ]
        );
    }
}
