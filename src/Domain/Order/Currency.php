<?php

namespace OrderFlow\Domain\Order;

enum Currency: string
{
    case USD = 'USD';
    case EUR = 'EUR';
    case RSD = 'RSD';

    public static function fromString(string $code): self
    {
        return match ($code) {
            'USD' => self::USD,
            'EUR' => self::EUR,
            'RSD' => self::RSD,
            default => throw new Exceptions\UnknownCurrency($code),
        };
    }
}
