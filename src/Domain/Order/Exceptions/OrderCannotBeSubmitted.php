<?php

namespace OrderFlow\Domain\Order\Exceptions;

use DomainException;

class OrderCannotBeSubmitted extends DomainException
{
    //
    public function __construct(string $message = "Order cannot be submitted", int $code = 0, ?DomainException $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
