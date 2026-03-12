<?php

namespace OrderFlow\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Model;

class OrderModel extends Model
{
    protected $table = 'orders';

    public $incrementing = false;
    
    public function items()
    {
        return $this->hasMany(OrderItemModel::class, 'order_id', 'id');
    }

    protected $fillable = ['id', 'status', 'currency'];
}
