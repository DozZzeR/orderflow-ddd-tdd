<?php

namespace OrderFlow\Application\Order;

use OrderFlow\Application\Events\EventDispatcher;
use OrderFlow\Domain\Order\OrderId;
use OrderFlow\Domain\Order\OrderRepository;

class CapturePaymentAction
{
    public function __construct(
        private OrderRepository $repository,
        private EventDispatcher $dispatcher
    ) {
    }

    public function handle(OrderId $orderId): void
    {
        $order = $this->repository->get($orderId);
        $order->markPaid();
        $events = $order->releaseEvents();
        if ($events === []) {
            return;
        }
        $this->repository->save($order);
        foreach ($events as $event) {
            $this->dispatcher->dispatch($event);
        }
    }
}