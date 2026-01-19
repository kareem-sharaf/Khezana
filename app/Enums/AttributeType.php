<?php

namespace App\Enums;

enum AttributeType: string
{
    case TEXT = 'text';
    case SELECT = 'select';
    case NUMBER = 'number';

    public function getLabel(): string
    {
        return match ($this) {
            self::TEXT => 'Text',
            self::SELECT => 'Select',
            self::NUMBER => 'Number',
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
