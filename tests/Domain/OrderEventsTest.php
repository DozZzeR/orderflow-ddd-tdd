<?php

namespace Tests\Domain;

use OrderFlow\Domain\Order\Currency;
use OrderFlow\Domain\Order\Money;
use OrderFlow\Domain\Order\Order;
use OrderFlow\Domain\Order\OrderId;
use OrderFlow\Domain\Order\Events\EventOrderCancelled;
use OrderFlow\Domain\Order\Events\EventOrderSubmitted;
use OrderFlow\Domain\Order\Events\EventPaymentCaptured;
use Tests\TestCase;

class OrderEventsTest extends TestCase
{
    public function test_it_releases_order_submitted_event_after_submit(): void
    {
        $orderId = OrderId::fromString('123');
        $order = Order::createDraft($orderId, Currency::USD);
        $order->addItem('ABC', 1, Money::of(10, Currency::USD));
        $order->submit();
        $events = $order->releaseEvents();
        $this->assertCount(1, $events);

        $event = $events[0];
        $this->assertInstanceOf(EventOrderSubmitted::class, $event);
        $this->assertEquals($orderId, $event->orderId);
    }

    public function test_it_releases_order_cancelled_event_after_cancel(): void
    {
        $orderId = OrderId::fromString('123');
        $order = Order::createDraft($orderId, Currency::USD);
        $order->addItem('ABC', 1, Money::of(10, Currency::USD));
        $order->cancel();
        $firstRelease = $order->releaseEvents();
        $this->assertCount(1, $firstRelease);
        $this->assertInstanceOf(EventOrderCancelled::class, $firstRelease[0]);
    }

    public function test_it_clears_events_after_release(): void
    {
        $orderId = OrderId::fromString('123');
        $order = Order::createDraft($orderId, Currency::USD);
        $order->addItem('ABC', 1, Money::of(10, Currency::USD));
        $order->submit();
        $events = $order->releaseEvents();
        // Проверяем количество
        $this->assertCount(1, $events);
        $events = $order->releaseEvents();
        // Проверяем, что после первого вызова событий больше нет
        $this->assertCount(0, $events);
    }

    public function test_it_does_not_record_duplicate_payment_captured_event_if_already_paid(): void
    {
        $orderId = OrderId::fromString('123');
        $order = Order::createDraft($orderId, Currency::USD);
        $order->addItem('ABC', 1, Money::of(10, Currency::USD));
        $order->submit();
        $order->releaseEvents();
        $order->markPaid();
        $singleRelease = $order->releaseEvents();
        $this->assertCount(1, $singleRelease);
        $this->assertInstanceOf(EventPaymentCaptured::class, $singleRelease[0]);
        $order->markPaid();
        $zeroRelease = $order->releaseEvents();
        $this->assertCount(0, $zeroRelease);
    }
}
