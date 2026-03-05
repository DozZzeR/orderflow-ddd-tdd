<?php

namespace OrderFlow\Domain\Order;

class Money
{
    private function __construct(private int $amount, private Currency $currency)
    {
        if ($amount < 0) {
            throw new Exceptions\MoneyAmountMustBeNonNegative();
        }
    }

    public static function of(int $amount, Currency $currency): self
    {
        return new self($amount, $currency);
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function currency(): Currency
    {
        return $this->currency;
    }
}
