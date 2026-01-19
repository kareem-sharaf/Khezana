<?php

namespace App\Enums;

enum OperationType: string
{
    case SELL = 'sell';
    case RENT = 'rent';
    case DONATE = 'donate';

    public function getLabel(): string
    {
        return match ($this) {
            self::SELL => 'Sell',
            self::RENT => 'Rent',
            self::DONATE => 'Donate',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::SELL => 'success',
            self::RENT => 'info',
            self::DONATE => 'warning',
        };
    }

    public static function options(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return array_map(
            fn (self $case) => $case->getLabel(),
            self::cases()
        );
    }
}
