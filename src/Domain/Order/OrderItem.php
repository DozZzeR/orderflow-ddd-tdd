<?php

namespace OrderFlow\Domain\Order;

use OrderFlow\Domain\Order\Exceptions\OrderCannotBeCancelled;
use OrderFlow\Domain\Order\Exceptions\OrderCannotBeSubmitted;
use OrderFlow\Domain\Order\Exceptions\OrderItemQuantityMustBePositive;

class OrderItem
{
    private function __construct(private string $sku, private int $quantity, private Money $price)
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

    public static function from(string $sku, int $quantity, Money $price): self
    {
        if ($quantity <= 0) {
            throw new OrderItemQuantityMustBePositive();
        }
        return new self($sku, $quantity, $price);
    }

    public function total(): Money
    {
        return Money::of($this->price->amount() * $this->quantity, $this->price->currency());
    }
}
