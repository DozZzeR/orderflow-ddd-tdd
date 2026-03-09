<?php

namespace OrderFlow\Domain\Order;

class OrderId
{
    public function __construct(public string $value)
    {
        if (empty($value)) {
            throw new \InvalidArgumentException("OrderId cannot be empty");
        }
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
