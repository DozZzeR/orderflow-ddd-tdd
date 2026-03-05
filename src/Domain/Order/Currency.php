<?php

namespace OrderFlow\Domain\Order;

enum Currency: string
{
    case USD = 'USD';
    case EUR = 'EUR';
    case RSD = 'RSD';

    public static function fromString(string $value): self
    {
        return match ($value) {
            'USD' => self::USD,
            'EUR' => self::EUR,
            'RSD' => self::RSD,
            default => throw new Exceptions\UnknownCurrency(),
        };
    }
}
