<?php

namespace OrderFlow\Domain\Order;

enum OrderStatus: string // Backed enum with string values
{
    case Draft = 'draft';
}