<?php

namespace OrderFlow\Domain\Order;

class OrderId
{
    /**
     * Create a new class instance.
     */
    public function __construct(public string $value)
    {
        //
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }
}
