<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroceryItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
           // 'id' => $this->id,
            'product_name' => $this->product_name,
            'category_id' => $this->catgory_id,
            'image' => $this->image,
            'price' => $this->price,
            'stock_quantity' => $this->Stock_quantity,
            'expiry_date' => $this->expiry_date,
            'description' => $this->description,
            'status' => $this->status,
        ];
    }
}
