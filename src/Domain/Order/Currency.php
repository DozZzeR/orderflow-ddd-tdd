<?php

namespace OrderFlow\Domain\Order;

enum Currency: string
{
    case USD = 'USD';
    case EUR = 'EUR';
    case RSD = 'RSD';
}
