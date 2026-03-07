<?php

namespace Tests\Domain;

use OrderFlow\Domain\Order\Currency;
use OrderFlow\Domain\Order\Exceptions\OrderCannotBeSubmitted;
use OrderFlow\Domain\Order\Money;
use OrderFlow\Domain\Order\Order;
use OrderFlow\Domain\Order\OrderId;
use OrderFlow\Domain\Order\OrderStatus;
use Tests\TestCase;

class SubmitOrderTest extends TestCase
{
    public function test_it_submits_in_submitted_status(): void
    {
        $orderId = OrderId::fromString('123');
        $order = Order::createDraft($orderId, Currency::USD);
        $order->addItem('ABC', 1, Money::of(10, Currency::USD));
        $order->submit();
        $this->assertSame(OrderStatus::Submitted, $order->status());
    }

    public function test_it_cannot_submit_submitted_order(): void
    {
        $orderId = OrderId::fromString('123');
        $order = Order::createDraft($orderId, Currency::USD);
        $order->addItem('ABC', 1, Money::of(10, Currency::USD));
        // First submit the order to set it to Submitted status
        $order->submit();
        $this->expectException(OrderCannotBeSubmitted::class);
        $order->submit();
    }

    public function test_it_rejects_submit_empty_items(): void
    {
        $order = Order::createDraft(OrderId::fromString('123'), Currency::USD);
        $this->expectException(OrderCannotBeSubmitted::class);
        $order->submit();
    }
}
