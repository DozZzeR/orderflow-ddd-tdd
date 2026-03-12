<?php

namespace Tests\Infrastructure\Repositories;

use Illuminate\Foundation\Testing\RefreshDatabase;
use OrderFlow\Domain\Order\Currency;
use OrderFlow\Domain\Order\Exceptions\OrderNotFound;
use OrderFlow\Domain\Order\Money;
use OrderFlow\Domain\Order\Order;
use OrderFlow\Domain\Order\OrderId;
use OrderFlow\Infrastructure\Repositories\EloquentOrderRepository;
use Tests\TestCase;

class EloquentOrderRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_saves_and_restores_order(): void
    {
        $orderId = OrderId::fromString('123');
        $order = Order::createDraft($orderId, Currency::USD);
        $order->addItem('ABC', 1, Money::of(10, Currency::USD));

        $repository = new EloquentOrderRepository();

        $repository->save($order);

        $restored = $repository->get($orderId);

        $this->assertSame($order->id()->toString(), $restored->id()->toString());
        $this->assertSame($order->status()->value, $restored->status()->value);
        $this->assertSame($order->total()->amount(), $restored->total()->amount());
        $this->assertSame($order->currency()->value, $restored->currency()->value);
    }

    public function test_it_restores_order_items(): void
    {
        $orderId = OrderId::fromString('123');
        $order = Order::createDraft($orderId, Currency::USD);
        $order->addItem('ABC', 2, Money::of(10, Currency::USD));
        $order->addItem('DEF', 1, Money::of(20, Currency::USD));

        $repository = new EloquentOrderRepository();
        $repository->save($order);
        $restored = $repository->get($orderId);

        $items = $restored->items();
        usort($items, fn($a, $b) => strcmp($a->sku(), $b->sku()));
        $this->assertCount(2, $items);
        $this->assertEquals('ABC', $items[0]->sku());
        $this->assertEquals(2, $items[0]->quantity());
        $this->assertEquals(10, $items[0]->price()->amount());
        $this->assertEquals('DEF', $items[1]->sku());
        $this->assertEquals(1, $items[1]->quantity());
        $this->assertEquals(20, $items[1]->price()->amount());
    }

    public function test_it_throws_when_order_not_found(): void
    {
        $repository = new EloquentOrderRepository();
        $this->expectException(OrderNotFound::class);
        $repository->get(OrderId::fromString('missing'));
    }
}
