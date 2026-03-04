<?php

namespace Tests\Domain;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use OrderFlow\Domain\Order\Exceptions\OrderCannotBeCancelled;
use OrderFlow\Domain\Order\Order;
use OrderFlow\Domain\Order\OrderId;
use OrderFlow\Domain\Order\OrderStatus;
use Tests\TestCase;

class CancelOrderTest extends TestCase
{
    public function test_it_cancels_draft_order(): void
    {
        $orderId = OrderId::fromString('123');
        $order = Order::createDraft($orderId);
        $order->cancel();
        $this->assertSame(OrderStatus::Cancelled, $order->status());
    }

    public function test_it_cancels_submitted_order(): void
    {
        $orderId = OrderId::fromString('123');
        $order = Order::createDraft($orderId);
        $order->addItem('ABC', 1);
        $order->submit();
        $order->cancel();
        $this->assertSame(OrderStatus::Cancelled, $order->status());
    }

    public function test_it_cannot_cancel_cancelled_order(): void
    {
        $orderId = OrderId::fromString('123');
        $order = Order::createDraft($orderId);
        $order->addItem('ABC', 1);
        // First submit the order to set it to Submitted status
        $order->submit();
        // First cancel the order to set it to Cancelled status
        $order->cancel();
        $this->expectException(OrderCannotBeCancelled::class);
        $order->cancel();
    }
}
