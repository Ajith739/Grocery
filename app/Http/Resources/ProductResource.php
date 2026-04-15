<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_code' => $this->product_code,
            'name' => $this->name,

            'category_id' => $this->category_id,

            // ✅ Custom category structure
            'category' => $this->category ? [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ] : null,

            'price' => $this->price,
            'originalPrice' => $this->originalPrice,
            'weight' => $this->weight,
            'rating' => $this->rating,
            'reviews' => $this->reviews,
            'brand' => $this->brand,
            'discount' => $this->discount,
            'bgColor' => $this->bgColor,
            'quantity' => $this->quantity,
        ];
    }
}
