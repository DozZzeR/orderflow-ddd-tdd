<?php

namespace Tests\Domain;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use OrderFlow\Domain\Order\Currency;
use OrderFlow\Domain\Order\Exceptions\OrderCannotBeSubmitted;
use OrderFlow\Domain\Order\Exceptions\OrderCurrencyMismatch;
use OrderFlow\Domain\Order\Exceptions\OrderItemQuantityMustBePositive;
use OrderFlow\Domain\Order\Money;
use OrderFlow\Domain\Order\Order;
use OrderFlow\Domain\Order\OrderId;
use Tests\TestCase;

class OrderItemsTest extends TestCase
{
    public function test_it_adds_order_items(): void
    {
        $order = Order::createDraft(OrderId::fromString('123'), Currency::USD);
        $order->addItem('ABC', 1, Money::of(10, Currency::USD));
        $items = $order->items();
        $this->assertCount(1, $items);
        $this->assertEquals('ABC', $items[0]->sku());
        $this->assertEquals(1, $items[0]->quantity());
    }

    public function test_it_rejects_quantity_less_than_one(): void
    {
        $order = Order::createDraft(OrderId::fromString('123'), Currency::USD);
        $this->expectException(OrderItemQuantityMustBePositive::class);
        $order->addItem('ABC', 0, Money::of(10, Currency::USD));
    }

    public function test_it_rejects_item_with_different_currency(): void
    {
        $order = Order::createDraft(OrderId::fromString('123'), Currency::USD);
        $this->expectException(OrderCurrencyMismatch::class);
        $order->addItem('ABC', 1, Money::of(10, Currency::EUR));
    }
}
