<?php

namespace OrderFlow\Application\Order;

use OrderFlow\Application\Events\EventDispatcher;
use OrderFlow\Domain\Order\OrderId;
use OrderFlow\Domain\Order\OrderRepository;

final class SubmitOrderAction
{
    public function __construct(
        private OrderRepository $repository,
        private EventDispatcher $dispatcher
    ) {
    }

    public function handle(OrderId $orderId): void
    {
        $order = $this->repository->get($orderId);
        $order->submit();
        $this->repository->save($order);

        $events = $order->releaseEvents();
        foreach ($events as $event) {
            $this->dispatcher->dispatch($event);
        }
    }
}