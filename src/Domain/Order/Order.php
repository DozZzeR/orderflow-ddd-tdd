<?php

namespace OrderFlow\Domain\Order;

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
}
