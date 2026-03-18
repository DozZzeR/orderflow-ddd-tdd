<?php

namespace OrderFlow\Application\Payment;

interface PaymentEventStore
{
    public function hasProcessed(string $eventId): bool;

    public function markAsProcessed(string $eventId): void;
}