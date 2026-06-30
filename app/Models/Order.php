<?php

namespace App\Models;
use App\Models\cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable=[
        'user_id',
        'total_amount',
        'status',
        'payment_method',
        'shipping_address'
    ];
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
