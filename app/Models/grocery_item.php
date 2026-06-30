<?php

namespace App\Models;
use App\Models\cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class grocery_item extends Model
{
    use SoftDeletes;
    protected $fillable=[
        'product_name',
        'catgory_id',
        'image',
        'price',
        'Stock_quantity',
        'expiry_date',
        'description',
        'status'
    ];
    public function cart()
    {
        return $this->hasMany(cart::class);
    }
}
