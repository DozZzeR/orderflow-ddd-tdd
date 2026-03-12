<?php

namespace OrderFlow\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class OrderItemModel extends Model
{
    use HasUuids;
    protected $table = 'order_items';

    protected $fillable = ['order_id', 'sku', 'quantity', 'price'];
}
