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

        // Size Attribute (TEXT type to support all size formats: S, M, L, 42, 45, 38, etc.)
        $size = Attribute::updateOrCreate(
            ['slug' => 'size'],
            [
                'name' => 'المقاس',
                'type' => AttributeType::TEXT,
                'is_required' => true,
            ]
        );

        // Remove all predefined size values since we're using TEXT type now
        AttributeValue::where('attribute_id', $size->id)->delete();

        // Color Attribute
        $color = Attribute::updateOrCreate(
            ['slug' => 'color'],
            [
                'name' => 'اللون',
                'type' => AttributeType::SELECT,
                'is_required' => false,
            ]
        );

        // Color Values (Arabic)
        $colors = [
            'أسود' => 'Black',
            'أبيض' => 'White',
            'أحمر' => 'Red',
            'أزرق' => 'Blue',
            'أخضر' => 'Green',
            'أصفر' => 'Yellow',
            'بني' => 'Brown',
            'رمادي' => 'Gray',
            'بيج' => 'Beige',
            'وردي' => 'Pink',
        ];
        foreach ($colors as $colorValueAr => $colorValueEn) {
            AttributeValue::updateOrCreate(
                [
                    'attribute_id' => $color->id,
                    'value' => $colorValueAr,
                ]
            );
        }

        // Fabric Attribute
        $fabric = Attribute::updateOrCreate(
            ['slug' => 'fabric'],
            [
                'name' => 'نوع القماش',
                'type' => AttributeType::SELECT,
                'is_required' => false,
            ]
        );

        // Fabric Values (Arabic)
        $fabrics = [
            'قطن' => 'Cotton',
            'بوليستر' => 'Polyester',
            'صوف' => 'Wool',
            'حرير' => 'Silk',
            'كتان' => 'Linen',
            'دنيم' => 'Denim',
            'جلد' => 'Leather',
            'صناعي' => 'Synthetic',
        ];
        foreach ($fabrics as $fabricValueAr => $fabricValueEn) {
            AttributeValue::updateOrCreate(
                [
                    'attribute_id' => $fabric->id,
                    'value' => $fabricValueAr,
                ]
            );
        }

        // Condition Attribute
        $condition = Attribute::updateOrCreate(
            ['slug' => 'condition'],
            [
                'name' => 'الحالة',
                'type' => AttributeType::SELECT,
                'is_required' => true,
            ]
        );

        // Condition Values (Arabic)
        $conditions = [
            'جديد' => 'New',
            'كالجديد' => 'Like New',
            'جيد' => 'Good',
            'مقبول' => 'Fair',
            'سيء' => 'Poor',
        ];
        foreach ($conditions as $conditionValueAr => $conditionValueEn) {
            AttributeValue::updateOrCreate(
                [
                    'attribute_id' => $condition->id,
                    'value' => $conditionValueAr,
                ]
            );
        }

        // Gender Attribute (for unisex items)
        $gender = Attribute::updateOrCreate(
            ['slug' => 'gender'],
            [
                'name' => 'الجنس',
                'type' => AttributeType::SELECT,
                'is_required' => false,
            ]
        );

        // Gender Values (Arabic)
        $genders = [
            'ذكر' => 'Male',
            'أنثى' => 'Female',
            'للجنسين' => 'Unisex',
        ];
        foreach ($genders as $genderValueAr => $genderValueEn) {
            AttributeValue::updateOrCreate(
                [
                    'attribute_id' => $gender->id,
                    'value' => $genderValueAr,
                ]
            );
        }

        // Brand Attribute (Text)
        Attribute::updateOrCreate(
            ['slug' => 'brand'],
            [
                'name' => 'العلامة التجارية',
                'type' => AttributeType::TEXT,
                'is_required' => false,
            ]
        );

        // Material Attribute (Text)
        Attribute::updateOrCreate(
            ['slug' => 'material'],
            [
                'name' => 'المادة',
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
