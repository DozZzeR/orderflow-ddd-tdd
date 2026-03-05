<?php

namespace Tests\Domain;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use OrderFlow\Domain\Order\Currency;
use OrderFlow\Domain\Order\Exceptions\OrderCannotBeCancelled;
use OrderFlow\Domain\Order\Exceptions\OrderCannotBePaid;
use OrderFlow\Domain\Order\Money;
use OrderFlow\Domain\Order\Order;
use OrderFlow\Domain\Order\OrderId;
use OrderFlow\Domain\Order\OrderStatus;
use Tests\TestCase;

class OrderPaymentTest extends TestCase
{
    public function test_it_marks_submitted_order_as_paid_and_is_idempotent(): void
    {
        $orderId = OrderId::fromString('123', Currency::USD);
        $order = Order::createDraft($orderId, Currency::USD);
        $order->addItem('ABC', 1, Money::of(10, Currency::USD));
        $order->submit();
        $order->markPaid();
        $this->assertSame(OrderStatus::Paid, $order->status());
        $order->markPaid();
        $this->assertSame(OrderStatus::Paid, $order->status());
    }

    public function test_it_cannot_pay_cancelled_order(): void
    {
        $orderId = OrderId::fromString('123', Currency::USD);
        $order = Order::createDraft($orderId, Currency::USD);
        $order->addItem('ABC', 1, Money::of(10, Currency::USD));
        $order->submit();
        $order->cancel();
        $this->expectException(OrderCannotBePaid::class);
        $order->markPaid();
    }

    public function test_it_cannot_pay_draft_order(): void
    {
        $orderId = OrderId::fromString('123', Currency::USD);
        $order = Order::createDraft($orderId, Currency::USD);
        $this->expectException(OrderCannotBePaid::class);
        $order->markPaid();
    }
}
