<?php

namespace App\Support;

class PaymentMethods
{
    public const PIX = 'pix';

    public const CREDITO = 'credito';

    public const DEBITO = 'debito';

    public const BOLETO = 'boleto';

    public static function options(): array
    {
        return [
            self::PIX => 'PIX',
            self::CREDITO => 'Crédito',
            self::DEBITO => 'Débito',
            self::BOLETO => 'Boleto',
        ];
    }

    public static function values(): array
    {
        return array_keys(self::options());
    }

    public static function label(string $value): string
    {
        return self::options()[$value] ?? $value;
    }
}
