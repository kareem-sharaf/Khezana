<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Item Availability Status Enum
 */
enum ItemAvailability: string
{
    case AVAILABLE = 'available';
    case UNAVAILABLE = 'unavailable';

    /**
     * Get all availability values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get human-readable label for the availability
     */
    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Available',
            self::UNAVAILABLE => 'Unavailable',
        };
    }

    /**
     * Get badge color for Filament UI
     */
    public function color(): string
    {
        return match ($this) {
            self::AVAILABLE => 'success',
            self::UNAVAILABLE => 'danger',
        };
    }

    /**
     * Check if item is available
     */
    public function isAvailable(): bool
    {
        return $this === self::AVAILABLE;
    }

    /**
     * Get options array for select fields
     */
    public static function options(): array
    {
        return [
            self::AVAILABLE->value => self::AVAILABLE->label(),
            self::UNAVAILABLE->value => self::UNAVAILABLE->label(),
        ];
    }
}
