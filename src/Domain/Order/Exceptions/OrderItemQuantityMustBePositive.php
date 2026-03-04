<?php

namespace OrderFlow\Domain\Order\Exceptions;

use DomainException;

class OrderItemQuantityMustBePositive extends DomainException
{
    //
    public function __construct(string $message = "Order item quantity must be positive", int $code = 0, ?DomainException $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
