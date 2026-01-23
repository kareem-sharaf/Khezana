<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Translation Helper
 * 
 * Provides helper methods for translating common terms
 * like attribute names, category names, etc.
 */
class TranslationHelper
{
    /**
     * Translate attribute name
     * 
     * Tries to translate common attribute names (size, color, etc.)
     * Falls back to original name if translation not found
     */
    public static function translateAttributeName(string $name): string
    {
        $lowerName = strtolower(trim($name));
        $translationKey = "attributes.common_names.{$lowerName}";
        
        $translated = __($translationKey);
        
        // If translation key not found, __() returns the key itself
        // So we check if it's different from the key
        if ($translated !== $translationKey) {
            return $translated;
        }
        
        // Fallback to original name
        return $name;
    }

    /**
     * Translate category name
     * 
     * Currently categories are stored in database with their names
     * This method can be extended to support category translations
     * For now, returns the name as-is
     */
    public static function translateCategoryName(string $name): string
    {
        // Categories are stored in database with their names
        // If you want to translate categories, you can add a translation file
        // For now, return as-is
        return $name;
    }

    /**
     * Translate multiple attribute names
     */
    public static function translateAttributeNames(array $names): array
    {
        return array_map([self::class, 'translateAttributeName'], $names);
    }
}
