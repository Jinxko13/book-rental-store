<?php

namespace App\Enums;

enum RangeEnum:string
{
    case Daily='daily';
    case WEEK = 'week';
    case MONTH = 'month';
    case YEAR = 'year';
    case QUARTER = 'quarter';

    public static function values(): array
    {
        return [
            self::Daily->value,
            self::WEEK->value,
            self::MONTH->value,
            self::YEAR->value,
            self::QUARTER->value,
        ];
    }

}
