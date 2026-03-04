<?php

namespace Tests\Domain;

use OrderFlow\Domain\Order\Exceptions\OrderCannotBeSubmitted;
use OrderFlow\Domain\Order\Order;
use OrderFlow\Domain\Order\OrderId;
use OrderFlow\Domain\Order\OrderStatus;
use Tests\TestCase;

class SubmitOrderTest extends TestCase
{
    public function test_it_submits_in_submitted_status(): void
    {
        $orderId = OrderId::fromString('123');
        $order = Order::createDraft($orderId);
        $order->submit();
        $this->assertSame(OrderStatus::Submitted, $order->status());
    }

    public function test_it_cannot_submit_submitted_order(): void
    {
        $orderId = OrderId::fromString('123');
        $order = Order::createDraft($orderId);
        // First submit the order to set it to Submitted status
        $order->submit();
        $this->expectException(OrderCannotBeSubmitted::class);
        $order->submit();
    }
}
