<?php

namespace OrderFlow\Domain\Order\Events;

use DateTimeImmutable;
use OrderFlow\Domain\Order\OrderId;
use OrderFlow\Domain\Shared\DomainEvent;

final readonly class EventOrderSubmitted implements DomainEvent
{
    public function __construct(
        public OrderId $orderId,
        public int $totalAmount,
        public string $currency,
        public DateTimeImmutable $occurredAt = new DateTimeImmutable(),
    ) {}
}
