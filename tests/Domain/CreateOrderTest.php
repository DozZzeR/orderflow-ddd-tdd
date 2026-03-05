<?php

namespace Tests\Domain;

use OrderFlow\Domain\Order\Currency;
use OrderFlow\Domain\Order\Money;
use OrderFlow\Domain\Order\OrderId;
use OrderFlow\Domain\Order\Order;
use OrderFlow\Domain\Order\OrderStatus;
use Tests\TestCase;

class CreateOrderTest extends TestCase
{
    public function test_it_creates_order_in_draft_status_with_currency(): void
    {
        $orderId = OrderId::fromString('123');
        $order = Order::createDraft($orderId, Currency::EUR);
        $this->assertSame(OrderStatus::Draft, $order->status());
        $this->assertSame($orderId->toString(), $order->id()->toString());
        $this->assertSame(Currency::EUR, $order->currency());
    }

    public function test_total_is_zero_for_empty_order(): void
    {
        $orderId = OrderId::fromString('123');
        $order = Order::createDraft($orderId, Currency::USD);
        $this->assertSame(0, $order->total()->amount());
    }

    public function test_total_sums_multiple_items(): void
    {
        $orderId = OrderId::fromString('123');
        $order = Order::createDraft($orderId, Currency::USD);
        $order->addItem('ABC', 2, Money::of(10, Currency::USD));
        $order->addItem('DEF', 1, Money::of(20, Currency::USD));
        $this->assertSame(40, $order->total()->amount());
    }
}
