<?php

namespace App\Enums;

enum RoleEnum: string
{
    case CUSTOMER = 'customer';
    case ADMIN = 'admin';
    case STAFF = 'staff';

    public static function values(): array
    {
        return [
            self::CUSTOMER->value,
            self::ADMIN->value,
            self::STAFF->value,
        ];
    }
}
