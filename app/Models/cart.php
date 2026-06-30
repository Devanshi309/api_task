<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class cart extends Model
{
    use SoftDeletes;
    protected $fillable=[
        'user_id',
        'grocery_item_id',
        'quantity'
    ];
    public function product()
    {
        return $this->belongsTo(grocery_item::class,'grocery_item_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
