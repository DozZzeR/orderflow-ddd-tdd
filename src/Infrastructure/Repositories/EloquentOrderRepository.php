<?php

namespace OrderFlow\Infrastructure\Repositories;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use OrderFlow\Domain\Order\Currency;
use OrderFlow\Domain\Order\Exceptions\OrderNotFound;
use OrderFlow\Domain\Order\Money;
use OrderFlow\Domain\Order\Order;
use OrderFlow\Domain\Order\OrderId;
use OrderFlow\Domain\Order\OrderItem;
use OrderFlow\Domain\Order\OrderStatus;
use OrderFlow\Infrastructure\Persistence\OrderItemModel;
use OrderFlow\Infrastructure\Persistence\OrderModel;

class EloquentOrderRepository
{
    public function save(Order $order): void
    {
        DB::transaction(function () use ($order) {
            OrderItemModel::where('order_id', $order->id()->toString())->delete();
            
            $model = OrderModel::updateOrCreate(
                ['id' => $order->id()->toString()],
                [
                    'status' => $order->status()->value,
                    'currency' => $order->total()->currency()->value,
                ]
            );
            $itemData = [];
            foreach ($order->items() as $item) {
                $itemData[] = [
                    'sku' => $item->sku(),
                    'quantity' => $item->quantity(),
                    'price' => $item->price()->amount(),
                ];
            }
            $model->items()->createMany($itemData);
        });
        
    }

    public function get(OrderId $id): Order
    {
        $model = OrderModel::with('items')->find($id->toString());

        if ($model === null) {
            throw new OrderNotFound();
        }
        $items = [];
        foreach ($model->items as $itemModel) {
            $items[] = OrderItem::from(
                $itemModel->sku,
                $itemModel->quantity,
                Money::of($itemModel->price, Currency::fromString($model->currency))
            );
        }
        return Order::reconstitute(
            OrderId::fromString($model->id),
            OrderStatus::from($model->status),
            Currency::fromString($model->currency),
            $items
        );
    }
}