<?php

namespace Tests\Application;

use OrderFlow\Application\Events\EventDispatcher;
use OrderFlow\Application\SubmitOrderAction;
use OrderFlow\Domain\Order\Currency;
use OrderFlow\Domain\Order\Events\EventOrderSubmitted;
use OrderFlow\Domain\Order\Exceptions\OrderCannotBeCancelled;
use OrderFlow\Domain\Order\Exceptions\OrderCannotBeSubmitted;
use OrderFlow\Domain\Order\Exceptions\OrderNotFound;
use OrderFlow\Domain\Order\Money;
use OrderFlow\Domain\Order\Order;
use OrderFlow\Domain\Order\OrderId;
use OrderFlow\Domain\Order\OrderRepository;
use OrderFlow\Domain\Order\OrderStatus;
use Tests\TestCase;

class SubmitOrderActionTest extends TestCase
{
    public function test_it_submits_order_and_dispatches_order_submitted_event(): void
    {
        $orderId = OrderId::fromString('123');
        $order = Order::createDraft($orderId, Currency::USD);
        $order->addItem('ABC', 1, Money::of(10, Currency::USD));

        $repository = $this->createMock(OrderRepository::class);
        $dispatcher = $this->createMock(EventDispatcher::class);

        $repository->method('get')->with($orderId)->willReturn($order);
        $repository->expects($this->once())->method('save')->with($order);

        $dispatcher->expects($this->once())->method('dispatch')->with($this->callback(
            function ($event) use ($orderId) {
                return $event instanceof EventOrderSubmitted && $event->orderId->equals($orderId);
            }
        ));

        $useCase = new SubmitOrderAction($repository, $dispatcher);
        $useCase->handle($orderId);
        $this->assertTrue($order->status()->equals(OrderStatus::Submitted));
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
        $useCase = new SubmitOrderAction($repository, $dispatcher);
        $useCase->handle($orderId);
    }

    public function test_it_throws_when_order_cannot_be_submitted(): void
    {
        $orderId = OrderId::fromString('123');
        $order = Order::createDraft($orderId, Currency::USD);
        $order->addItem('ABC', 1, Money::of(10, Currency::USD));
        $order->submit();

        $repository = $this->createMock(OrderRepository::class);
        $dispatcher = $this->createMock(EventDispatcher::class);

        $repository->method('get')->with($orderId)->willReturn($order);
        $repository->expects($this->never())->method('save');
        $dispatcher->expects($this->never())->method('dispatch');
        $this->expectException(OrderCannotBeSubmitted::class);
        $useCase = new SubmitOrderAction($repository, $dispatcher);
        $useCase->handle($orderId);
    }
}
