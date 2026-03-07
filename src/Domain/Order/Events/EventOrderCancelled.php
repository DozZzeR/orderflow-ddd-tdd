<?php

namespace OrderFlow\Domain\Order\Events;

use DateTimeImmutable;
use OrderFlow\Domain\Order\OrderId;
use OrderFlow\Domain\Shared\DomainEvent;

final readonly class EventOrderCancelled implements DomainEvent
{
    public function __construct(
        public OrderId $orderId,
        public DateTimeImmutable $occurredAt = new DateTimeImmutable(),
    ) {}
}
