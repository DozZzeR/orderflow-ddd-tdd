<?php

namespace OrderFlow\Domain\Order\Exceptions;

use DomainException;

class OrderCannotBeCancelled extends DomainException
{
    //
    public function __construct(string $message = "Order cannot be cancelled", int $code = 0, ?DomainException $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
