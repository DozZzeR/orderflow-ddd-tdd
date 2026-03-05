<?php

namespace OrderFlow\Domain\Order\Exceptions;

use DomainException;

class UnknownCurrency extends DomainException
{
    //
    public function __construct(string $message = "Unknown currency", int $code = 0, ?DomainException $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
