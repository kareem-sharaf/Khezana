<?php

declare(strict_types=1);

namespace App\Read\Shared\Models;

use App\Models\ItemAttribute;

class AttributeReadModel
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $type,
        public readonly mixed $value,
        public readonly string $formattedValue,
    ) {
    }

    public static function fromModel(ItemAttribute $itemAttribute): self
    {
        $attribute = $itemAttribute->attribute;
        $value = $itemAttribute->value;
        $attributeSlug = $attribute->slug ?? '';
        
        // Format value based on type
        $formattedValue = match($attribute->type->value) {
            'number' => is_numeric($value) ? number_format((float) $value, 0) : $value,
            'select' => self::translateAttributeValue($value, $attributeSlug),
            default => $value,
        };

        return new self(
            id: $attribute->id,
            name: $attribute->name,
            type: $attribute->type->value,
            value: $value,
            formattedValue: $formattedValue,
        );
    }

    /**
     * Translate attribute value to Arabic only
     * Removes English text if present (e.g., "أسود Black" -> "أسود")
     */
    private static function translateAttributeValue(string $value, ?string $attributeSlug = null): string
    {
        $attributeSlug = $attributeSlug ?? '';
        $value = trim($value);
        
        // Check if value contains Arabic characters
        $hasArabic = preg_match('/[\x{0600}-\x{06FF}]/u', $value);
        $hasEnglish = preg_match('/[A-Za-z]/', $value);
        
        // If value contains both Arabic and English, extract Arabic only
        if ($hasArabic && $hasEnglish) {
            // Pattern 1: "أسود Black" or "أسود Black Red" - extract Arabic part at the beginning
            if (preg_match('/^([\x{0600}-\x{06FF}\s]+?)(?:\s+[A-Za-z])/u', $value, $matches)) {
                return trim($matches[1]);
            }
            
            // Pattern 2: "Black أسود" - extract Arabic part at the end
            if (preg_match('/([\x{0600}-\x{06FF}\s]+)$/u', $value, $matches)) {
                return trim($matches[1]);
            }
            
            // Pattern 3: "أسود / Black" or "أسود - Black" - extract Arabic part before separator
            if (preg_match('/^([\x{0600}-\x{06FF}\s]+?)\s*[\/\-]\s*[A-Za-z]/u', $value, $matches)) {
                return trim($matches[1]);
            }
            
            // Pattern 4: "Black / أسود" or "Black - أسود" - extract Arabic part after separator
            if (preg_match('/[A-Za-z]\s*[\/\-]\s*([\x{0600}-\x{06FF}\s]+)$/u', $value, $matches)) {
                return trim($matches[1]);
            }
            
            // Pattern 5: Extract all Arabic characters (fallback)
            if (preg_match_all('/[\x{0600}-\x{06FF}]+/u', $value, $matches)) {
                return implode(' ', $matches[0]);
            }
        }
        
        // If value contains only English, try to translate it
        if ($hasEnglish && !$hasArabic) {
            return self::translateEnglishToArabic($value, $attributeSlug);
        }
        
        // If value contains only Arabic, return as is
        if ($hasArabic && !$hasEnglish) {
            return $value;
        }
        
        // Return as is if no pattern matches
        return $value;
    }

    /**
     * Translate English attribute value to Arabic
     */
    private static function translateEnglishToArabic(string $englishValue, string $attributeSlug): string
    {
        $mappings = [
            'color' => [
                'Black' => 'أسود',
                'White' => 'أبيض',
                'Red' => 'أحمر',
                'Blue' => 'أزرق',
                'Green' => 'أخضر',
                'Yellow' => 'أصفر',
                'Brown' => 'بني',
                'Gray' => 'رمادي',
                'Grey' => 'رمادي',
                'Beige' => 'بيج',
                'Pink' => 'وردي',
            ],
            'fabric' => [
                'Cotton' => 'قطن',
                'Polyester' => 'بوليستر',
                'Wool' => 'صوف',
                'Silk' => 'حرير',
                'Linen' => 'كتان',
                'Denim' => 'دنيم',
                'Leather' => 'جلد',
                'Synthetic' => 'صناعي',
            ],
            'condition' => [
                'New' => 'جديد',
                'Like New' => 'كالجديد',
                'Good' => 'جيد',
                'Fair' => 'مقبول',
                'Poor' => 'سيء',
            ],
            'gender' => [
                'Male' => 'ذكر',
                'Female' => 'أنثى',
                'Unisex' => 'للجنسين',
            ],
        ];

        $englishValue = trim($englishValue);
        
        if (isset($mappings[$attributeSlug][$englishValue])) {
            return $mappings[$attributeSlug][$englishValue];
        }

        // Return original if no translation found
        return $englishValue;
    }
}
