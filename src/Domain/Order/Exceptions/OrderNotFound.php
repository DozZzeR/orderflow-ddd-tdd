<?php

namespace OrderFlow\Domain\Order\Exceptions;

use DomainException;
use Throwable;

class OrderNotFound extends DomainException implements Throwable
{
    public function __construct(string $message = "Order not found", int $code = 0, ?DomainException $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
