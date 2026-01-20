<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Request Status Enum
 * 
 * Represents the lifecycle status of a request:
 * - OPEN: Request is active and accepting offers
 * - FULFILLED: Request has been fulfilled (user received what they needed)
 * - CLOSED: Request is closed and no longer accepting offers
 */
enum RequestStatus: string
{
    case OPEN = 'open';
    case FULFILLED = 'fulfilled';
    case CLOSED = 'closed';

    /**
     * Get all status values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get human-readable label for the status
     */
    public function label(): string
    {
        return match ($this) {
            self::OPEN => __('requests.status.open'),
            self::FULFILLED => __('requests.status.fulfilled'),
            self::CLOSED => __('requests.status.closed'),
        };
    }

    /**
     * Get badge color for Filament UI
     */
    public function color(): string
    {
        return match ($this) {
            self::OPEN => 'success',
            self::FULFILLED => 'info',
            self::CLOSED => 'gray',
        };
    }

    /**
     * Check if request is open (accepting offers)
     */
    public function isOpen(): bool
    {
        return $this === self::OPEN;
    }

    /**
     * Check if request can receive new offers
     */
    public function canReceiveOffers(): bool
    {
        return $this === self::OPEN;
    }

    /**
     * Get options array for select fields
     */
    public static function options(): array
    {
        return [
            self::OPEN->value => self::OPEN->label(),
            self::FULFILLED->value => self::FULFILLED->label(),
            self::CLOSED->value => self::CLOSED->label(),
        ];
    }
}
