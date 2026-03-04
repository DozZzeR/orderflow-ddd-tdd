<?php

namespace Tests\Domain;
use OrderFlow\Domain\Order\OrderId;
use OrderFlow\Domain\Order\Order;
use OrderFlow\Domain\Order\OrderStatus;
use Tests\TestCase;

class CreateOrderTest extends TestCase
{
    public function test_it_creates_orders_in_draft_status(): void
    {
        $orderId = OrderId::fromString('123');
        $order = Order::createDraft($orderId);
        $this->assertSame(OrderStatus::Draft, $order->status());
        $this->assertSame($orderId->toString(), $order->id()->toString());
    }
}
