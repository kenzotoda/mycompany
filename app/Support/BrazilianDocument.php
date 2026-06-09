<?php

namespace App\Support;

class BrazilianDocument
{
    public static function digitsOnly(?string $value): string
    {
        return preg_replace('/\D/', '', $value ?? '') ?? '';
    }

    public static function formatCnpj(string $digits): string
    {
        $digits = substr(self::digitsOnly($digits), 0, 14);

        return match (strlen($digits)) {
            0 => '',
            1, 2 => $digits,
            3, 4, 5 => substr($digits, 0, 2).'.'.substr($digits, 2),
            6, 7, 8 => substr($digits, 0, 2).'.'.substr($digits, 2, 3).'.'.substr($digits, 5),
            9, 10, 11 => substr($digits, 0, 2).'.'.substr($digits, 2, 3).'.'.substr($digits, 5, 3).'/'.substr($digits, 8),
            12, 13, 14 => substr($digits, 0, 2).'.'.substr($digits, 2, 3).'.'.substr($digits, 5, 3).'/'.substr($digits, 8, 4).'-'.substr($digits, 12),
            default => $digits,
        };
    }

    public static function formatCpf(string $digits): string
    {
        $digits = substr(self::digitsOnly($digits), 0, 11);

        return match (strlen($digits)) {
            0 => '',
            1, 2, 3 => $digits,
            4, 5, 6 => substr($digits, 0, 3).'.'.substr($digits, 3),
            7, 8, 9 => substr($digits, 0, 3).'.'.substr($digits, 3, 3).'.'.substr($digits, 6),
            10, 11 => substr($digits, 0, 3).'.'.substr($digits, 3, 3).'.'.substr($digits, 6, 3).'-'.substr($digits, 9),
            default => $digits,
        };
    }

    public static function formatPhone(string $digits): string
    {
        $digits = substr(self::digitsOnly($digits), 0, 11);

        if (strlen($digits) === 0) {
            return '';
        }

        if (strlen($digits) <= 2) {
            return '('.$digits;
        }

        if (strlen($digits) <= 6) {
            return '('.substr($digits, 0, 2).') '.substr($digits, 2);
        }

        if (strlen($digits) <= 10) {
            return '('.substr($digits, 0, 2).') '.substr($digits, 2, 4).'-'.substr($digits, 6);
        }

        return '('.substr($digits, 0, 2).') '.substr($digits, 2, 5).'-'.substr($digits, 7);
    }

    public static function isCompleteCnpj(?string $value): bool
    {
        return strlen(self::digitsOnly($value)) === 14;
    }

    public static function isCompleteCpf(?string $value): bool
    {
        return strlen(self::digitsOnly($value)) === 11;
    }

    public static function isCompletePhone(?string $value): bool
    {
        $length = strlen(self::digitsOnly($value));

        return $length === 10 || $length === 11;
    }
}
