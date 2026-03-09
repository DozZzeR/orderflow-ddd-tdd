<?php

namespace OrderFlow\Domain\Order;

interface OrderRepository
{
    public function get(OrderId $id): Order;

    public function save(Order $order): void;
}