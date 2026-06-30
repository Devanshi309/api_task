<?php

namespace App\Models;
use App\Models\cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable=[
        'order_id',
        'grocery_item_id',
        'quantity',
        'price',
        'subtotal'
    ];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function product()
    {
        return $this->belongsTo(grocery_item::class,'grocery_item_id');
    }
}
