<?php

namespace OrderFlow\Domain\Order\Events;

use DateTimeImmutable;
use OrderFlow\Domain\Order\Money;
use OrderFlow\Domain\Order\OrderId;
use OrderFlow\Domain\Shared\DomainEvent;

final readonly class EventOrderSubmitted implements DomainEvent
{
    public function __construct(
        public OrderId $orderId,
        public Money $total,
        public DateTimeImmutable $occurredAt = new DateTimeImmutable(),
    ) {}
}
