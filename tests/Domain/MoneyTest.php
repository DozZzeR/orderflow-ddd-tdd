<?php

namespace Tests\Domain;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use OrderFlow\Domain\Order\Currency;
use OrderFlow\Domain\Order\Exceptions\MoneyAmountMustBeNonNegative;
use OrderFlow\Domain\Order\Money;
use Tests\TestCase;

class MoneyTest extends TestCase
{
    public function test_money_holds_amount_and_currency(): void
    {
        $money = Money::of(100, Currency::USD);
        $this->assertEquals(100, $money->amount());
        $this->assertEquals(Currency::USD, $money->currency());
    }

    public function test_money_rejects_negative_amount(): void
    {
        $this->expectException(MoneyAmountMustBeNonNegative::class);
        Money::of(-100, Currency::USD);
    }
}
