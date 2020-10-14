<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'product_name' => $this->translations->product_name,
            'product_price' => $this->product_price,
            'category_name' => $this->category->category_name,
            'category_id' => $this->category_id
        ];
    }
}
