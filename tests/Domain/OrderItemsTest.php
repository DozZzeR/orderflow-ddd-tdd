<?php

namespace Tests\Domain;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use OrderFlow\Domain\Order\Exceptions\OrderCannotBeSubmitted;
use OrderFlow\Domain\Order\Exceptions\OrderItemQuantityMustBePositive;
use OrderFlow\Domain\Order\Order;
use OrderFlow\Domain\Order\OrderId;
use Tests\TestCase;

class OrderItemsTest extends TestCase
{
    public function test_it_adds_order_items(): void
    {
        $order = Order::createDraft(OrderId::fromString('123'));
        $order->addItem('ABC', 1);
        $items = $order->items();
        $this->assertCount(1, $items);
        $this->assertEquals('ABC', $items[0]->sku());
        $this->assertEquals(1, $items[0]->quantity());
    }

    public function test_it_rejects_quantity_less_than_one(): void
    {
        $order = Order::createDraft(OrderId::fromString('123'));
        $this->expectException(OrderItemQuantityMustBePositive::class);
        $order->addItem('ABC', 0);
    }

    public function test_it_rejects_submit_empty_items(): void
    {
        $order = Order::createDraft(OrderId::fromString('123'));
        $this->expectException(OrderCannotBeSubmitted::class);
        $order->submit();
    }
}
