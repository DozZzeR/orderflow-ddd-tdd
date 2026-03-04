<?php

namespace OrderFlow\Domain\Order;

use OrderFlow\Domain\Order\Exceptions\OrderCannotBeCancelled;
use OrderFlow\Domain\Order\Exceptions\OrderCannotBeSubmitted;

class OrderItem
{
    private function __construct(private string $sku, private int $quantity)
    {
        //
    }

    public function sku(): string
    {
        return $this->sku;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }

    public static function from(string $sku, int $quantity): self
    {
        return new self($sku, $quantity);
    }
}
