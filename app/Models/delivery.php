<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class delivery extends Model
{
    protected $fillable=[
        'order_id',
        'delivery_boy_name',
        'status',
        'delivery_date'
    ];
    public function order()
    {
        return $this->belongsTo(order::class);
    }
}
