<?php

namespace OrderFlow\Domain\Order\Exceptions;

use DomainException;

class MoneyAmountMustBeNonNegative extends DomainException
{
    //
    public function __construct(string $message = "Money amount must be non-negative", int $code = 0, ?DomainException $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
