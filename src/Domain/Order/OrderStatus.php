<?php

namespace OrderFlow\Domain\Order;

enum OrderStatus: string // Backed enum with string values
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Cancelled = 'cancelled';
    case Paid = 'paid';

    public function canSubmit(): bool
    {
        return $this === self::Draft;
    }

    public function canCancel(): bool
    {
        return $this === self::Draft || $this === self::Submitted;
    }

    public function canPay(): bool
    {
        return $this === self::Submitted || $this === self::Paid;
    }
}