<?php

namespace App\Http\Resources\Product;

use App\Http\Resources\PriceResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductPriceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'product' => $this->whenLoaded('product', ProductListResource::make($this->product)),
            'price' => $this->whenLoaded('price', PriceResource::make($this->price)),
        ];
    }
}
