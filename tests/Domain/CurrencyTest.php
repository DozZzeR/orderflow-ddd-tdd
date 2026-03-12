<?php

namespace Tests\Domain;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use OrderFlow\Domain\Order\Currency;
use OrderFlow\Domain\Order\Exceptions\UnknownCurrency;
use Tests\TestCase;

class CurrencyTest extends TestCase
{
    public function test_it_rejects_unknown_currency_code(): void
    {
        $this->expectException(UnknownCurrency::class);
        Currency::from('USD0000');
    }
}
