<?php

namespace OrderFlow\Domain\Order;

use OrderFlow\Domain\Order\Exceptions\OrderCannotBeCancelled;
use OrderFlow\Domain\Order\Exceptions\OrderCannotBePaid;
use OrderFlow\Domain\Order\Exceptions\OrderCannotBeSubmitted;
use OrderFlow\Domain\Order\Exceptions\OrderCurrencyMismatch;
use OrderFlow\Domain\Shared\DomainEvent;
use OrderFlow\Domain\Order\Events\EventOrderCancelled;
use OrderFlow\Domain\Order\Events\EventOrderSubmitted;
use OrderFlow\Domain\Order\Events\EventPaymentCaptured;

class Order
{
    private array $items = [];
    
    /** @var DomainEvent[] */
    private array $events = [];

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
        $this->recordEvent(new EventOrderSubmitted(
            orderId: $this->id,
            totalAmount: $this->total()->amount(),
            currency: $this->currency()->value,
        ));
    }

    public function markPaid(): void
    {
        if ($this->status === OrderStatus::Paid) {
            return; // idempotent no-op
        }
        if (!$this->status->canPay()) {
            throw new OrderCannotBePaid();
        }
        $this->status = OrderStatus::Paid;
        $this->recordEvent(new EventPaymentCaptured($this->id));
    }

    public function cancel(): void
    {
        if (!$this->status->canCancel()) {
            throw new OrderCannotBeCancelled();
        }
        $this->status = OrderStatus::Cancelled;
        $this->recordEvent(new EventOrderCancelled($this->id));
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

    /** @return DomainEvent[] */
    public function releaseEvents(): array
    {
        $events = $this->events;
        $this->events = []; // Очищаем после выдачи
        return $events;
    }

    private function recordEvent(DomainEvent $event): void
    {
        $this->events[] = $event;
    }
}
