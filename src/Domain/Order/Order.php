<?php

namespace OrderFlow\Domain\Order;

use OrderFlow\Domain\Order\Exceptions\OrderCannotBeCancelled;
use OrderFlow\Domain\Order\Exceptions\OrderCannotBePaid;
use OrderFlow\Domain\Order\Exceptions\OrderCannotBeSubmitted;
use OrderFlow\Domain\Order\Exceptions\OrderItemQuantityMustBePositive;

class Order
{
    private array $items = [];

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
        if (empty($this->items)) {
            throw new OrderCannotBeSubmitted();
        }
        $this->status = OrderStatus::Submitted;
    }

    public function markPaid(): void
    {
        if (!$this->status->canPay()) {
            throw new OrderCannotBePaid();
        }
        if ($this->status === OrderStatus::Paid) {
            return; // idempotent no-op
        }
        $this->status = OrderStatus::Paid;
    }

    public function cancel(): void
    {
        if (!$this->status->canCancel()) {
            throw new OrderCannotBeCancelled();
        }
        $this->status = OrderStatus::Cancelled;
    }

    public function addItem(string $sku, int $quantity): void
    {
        if ($quantity <= 0) {
            throw new OrderItemQuantityMustBePositive();
        }
        $this->items[] = OrderItem::from($sku, $quantity);
    }

    /**
     * @return OrderItem[]
     */
    public function items(): array
    {
        return $this->items;
    }
}
