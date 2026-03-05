<?php

namespace OrderFlow\Domain\Order\Exceptions;

use DomainException;

class OrderCurrencyMismatch extends DomainException
{
    //
    public function __construct(string $message = "Order currency mismatch", int $code = 0, ?DomainException $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
