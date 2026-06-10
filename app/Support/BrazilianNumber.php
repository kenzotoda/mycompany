<?php

namespace App\Support;

class BrazilianNumber
{
    public static function toInteger(int|float|string|null $value): int
    {
        return max(0, (int) round((float) ($value ?? 0)));
    }

    public static function formatInteger(int|float|string|null $value): string
    {
        return number_format(self::toInteger($value), 0, ',', '.');
    }
}
