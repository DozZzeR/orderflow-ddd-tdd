<?php

namespace Tests\Application\Order;

use OrderFlow\Application\Order\CapturePaymentAction;
use OrderFlow\Application\Events\EventDispatcher;
use OrderFlow\Domain\Order\Currency;
use OrderFlow\Domain\Order\Events\EventPaymentCaptured;
use OrderFlow\Domain\Order\Exceptions\OrderNotFound;
use OrderFlow\Domain\Order\Money;
use OrderFlow\Domain\Order\Order;
use OrderFlow\Domain\Order\OrderId;
use OrderFlow\Domain\Order\OrderRepository;
use OrderFlow\Domain\Order\OrderStatus;
use Tests\TestCase;

class CapturePaymentActionTest extends TestCase
{
    public function test_it_marks_submitted_order_as_paid_and_dispatches_payment_captured(): void
    {
        $orderId = OrderId::fromString('123');
        $order = Order::createDraft($orderId, Currency::USD);
        $order->addItem('ABC', 1, Money::of(10, Currency::USD));
        $order->submit();
        $order->releaseEvents(); // clear events from submit

        $repository = $this->createMock(OrderRepository::class);
        $dispatcher = $this->createMock(EventDispatcher::class);

        $repository->method('get')->with($orderId)->willReturn($order);
        $repository->expects($this->once())->method('save')->with($order);

        $dispatcher->expects($this->once())->method('dispatch')->with($this->callback(
            function ($event) use ($orderId) {
                return $event instanceof EventPaymentCaptured && $event->orderId->equals($orderId);
            }
        ));

        $useCase = new CapturePaymentAction($repository, $dispatcher);
        $useCase->handle($orderId);
        $this->assertTrue($order->status()->equals(OrderStatus::Paid));
    }

    public function test_it_throws_when_order_not_found(): void
    {
        $orderId = OrderId::fromString('123');
        $repository = $this->createMock(OrderRepository::class);
        $dispatcher = $this->createMock(EventDispatcher::class);

        $repository->method('get')->with($orderId)->willThrowException(new OrderNotFound());
        $repository->expects($this->never())->method('save');
        $dispatcher->expects($this->never())->method('dispatch');
        $this->expectException(OrderNotFound::class);
        $useCase = new CapturePaymentAction($repository, $dispatcher);
        $useCase->handle($orderId);
    }

    public function test_it_does_not_save_or_dispatch_when_order_is_already_paid(): void
    {
        $orderId = OrderId::fromString('123');
        $order = Order::createDraft($orderId, Currency::USD);
        $order->addItem('ABC', 1, Money::of(10, Currency::USD));
        $order->submit();
        $order->markPaid();
        $order->releaseEvents(); // clear events from submit and markPaid

        $repository = $this->createMock(OrderRepository::class);
        $dispatcher = $this->createMock(EventDispatcher::class);

        $repository->method('get')->with($orderId)->willReturn($order);
        $repository->expects($this->never())->method('save');
        $dispatcher->expects($this->never())->method('dispatch');
        $useCase = new CapturePaymentAction($repository, $dispatcher);
        $useCase->handle($orderId);
    }
}
