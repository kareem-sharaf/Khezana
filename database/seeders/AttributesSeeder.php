<?php

namespace Database\Seeders;

use App\Enums\AttributeType;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Services\AttributeService;
use Illuminate\Database\Seeder;

class AttributesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attributeService = app(AttributeService::class);

        // Size Attribute
        $size = Attribute::firstOrCreate(
            ['slug' => 'size'],
            [
                'name' => 'Size',
                'type' => AttributeType::SELECT,
                'is_required' => true,
            ]
        );

        // Size Values
        $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL'];
        foreach ($sizes as $sizeValue) {
            AttributeValue::firstOrCreate(
                [
                    'attribute_id' => $size->id,
                    'value' => $sizeValue,
                ]
            );
        }

        // Color Attribute
        $color = Attribute::firstOrCreate(
            ['slug' => 'color'],
            [
                'name' => 'Color',
                'type' => AttributeType::SELECT,
                'is_required' => false,
            ]
        );

        // Color Values
        $colors = ['Black', 'White', 'Red', 'Blue', 'Green', 'Yellow', 'Brown', 'Gray', 'Beige', 'Pink'];
        foreach ($colors as $colorValue) {
            AttributeValue::firstOrCreate(
                [
                    'attribute_id' => $color->id,
                    'value' => $colorValue,
                ]
            );
        }

        // Fabric Attribute
        $fabric = Attribute::firstOrCreate(
            ['slug' => 'fabric'],
            [
                'name' => 'Fabric',
                'type' => AttributeType::SELECT,
                'is_required' => false,
            ]
        );

        // Fabric Values
        $fabrics = ['Cotton', 'Polyester', 'Wool', 'Silk', 'Linen', 'Denim', 'Leather', 'Synthetic'];
        foreach ($fabrics as $fabricValue) {
            AttributeValue::firstOrCreate(
                [
                    'attribute_id' => $fabric->id,
                    'value' => $fabricValue,
                ]
            );
        }

        // Condition Attribute
        $condition = Attribute::firstOrCreate(
            ['slug' => 'condition'],
            [
                'name' => 'Condition',
                'type' => AttributeType::SELECT,
                'is_required' => true,
            ]
        );

        // Condition Values
        $conditions = ['New', 'Like New', 'Good', 'Fair', 'Poor'];
        foreach ($conditions as $conditionValue) {
            AttributeValue::firstOrCreate(
                [
                    'attribute_id' => $condition->id,
                    'value' => $conditionValue,
                ]
            );
        }

        // Gender Attribute (for unisex items)
        $gender = Attribute::firstOrCreate(
            ['slug' => 'gender'],
            [
                'name' => 'Gender',
                'type' => AttributeType::SELECT,
                'is_required' => false,
            ]
        );

        // Gender Values
        $genders = ['Male', 'Female', 'Unisex'];
        foreach ($genders as $genderValue) {
            AttributeValue::firstOrCreate(
                [
                    'attribute_id' => $gender->id,
                    'value' => $genderValue,
                ]
            );
        }

        // Brand Attribute (Text)
        Attribute::firstOrCreate(
            ['slug' => 'brand'],
            [
                'name' => 'Brand',
                'type' => AttributeType::TEXT,
                'is_required' => false,
            ]
        );

        // Material Attribute (Text)
        Attribute::firstOrCreate(
            ['slug' => 'material'],
            [
                'name' => 'Material',
                'type' => AttributeType::TEXT,
                'is_required' => false,
            ]
        );

        // Assign attributes to categories
        $allCategories = Category::all();

        // Assign common attributes to all categories
        foreach ($allCategories as $category) {
            $category->attributes()->syncWithoutDetaching([
                $size->id,
                $color->id,
                $condition->id,
            ]);
        }

        // Assign fabric to clothing categories (not shoes)
        $clothingCategories = Category::whereNotIn('slug', ['men-shoes', 'women-shoes'])->get();
        foreach ($clothingCategories as $category) {
            $category->attributes()->syncWithoutDetaching([$fabric->id]);
        }
    }
}
