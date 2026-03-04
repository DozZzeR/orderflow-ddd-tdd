<?php

namespace OrderFlow\Domain\Order;

use OrderFlow\Domain\Order\Exceptions\OrderCannotBeCancelled;
use OrderFlow\Domain\Order\Exceptions\OrderCannotBeSubmitted;

class Order
{
    /**
     * Create a new class instance.
     */
    private function __construct(private OrderId $id, private OrderStatus $status)
    {
        //
    }

    public function id(): OrderId
    {
        return $this->id;
    }

    public function status(): OrderStatus
    {
        return $this->status;
    }

    public static function createDraft(OrderId $id): self
    {
        return new self($id, OrderStatus::Draft);
    }

    public function submit(): void
    {
        if (!$this->status->canSubmit()) {
            throw new OrderCannotBeSubmitted();
        }
        $this->status = OrderStatus::Submitted;
    }

    public function cancel(): void
    {
        if (!$this->status->canCancel()) {
            throw new OrderCannotBeCancelled();
        }
        $this->status = OrderStatus::Cancelled;
    }
}
