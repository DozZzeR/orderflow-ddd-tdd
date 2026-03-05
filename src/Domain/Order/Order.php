<?php

namespace OrderFlow\Domain\Order;

use OrderFlow\Domain\Order\Exceptions\OrderCannotBeCancelled;
use OrderFlow\Domain\Order\Exceptions\OrderCannotBePaid;
use OrderFlow\Domain\Order\Exceptions\OrderCannotBeSubmitted;
use OrderFlow\Domain\Order\Exceptions\OrderCurrencyMismatch;
use OrderFlow\Domain\Order\Exceptions\OrderItemQuantityMustBePositive;

class Order
{
    private array $items = [];

    private function __construct(private OrderId $id, private OrderStatus $status, private Currency $currency)
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

    public static function createDraft(OrderId $id, Currency $currency): self
    {
        return new self($id, OrderStatus::Draft, $currency);
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

    public function addItem(string $sku, int $quantity, Money $price): void
    {
        if ($price->currency() !== $this->currency()) {
            throw new OrderCurrencyMismatch();
        }
        $this->items[] = OrderItem::from($sku, $quantity, $price);
    }

    /**
     * @return OrderItem[]
     */
    public function items(): array
    {
        return $this->items;
    }

    public function total(): Money
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total = $item->total()->amount() + $total;
        }
        return Money::of($total, $this->currency());
    }

    public function currency(): Currency
    {
        return $this->currency;
    }
}
