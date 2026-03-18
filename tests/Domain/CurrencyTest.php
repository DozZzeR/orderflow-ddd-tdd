<?php

namespace Tests\Domain;

use OrderFlow\Domain\Order\Currency;
use OrderFlow\Domain\Order\Exceptions\UnknownCurrency;
use Tests\TestCase;

class CurrencyTest extends TestCase
{
    public function test_it_rejects_unknown_currency_code(): void
    {
        $this->expectException(UnknownCurrency::class);
        Currency::fromString('USD0000');
    }
}
