<?php

namespace App\Http\Resources\Order;

use App\Enums\Price\PriceType;
use App\Http\Resources\Product\ProductListResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $this->setPriceType(PriceType::ONE_TIME);
        return [
            'id' => $this->id,
            'productable_id' => $this->productable_id,
            'productable_type' => $this->productable_type,
            'entity' => ProductListResource::make($this->productable),
            'total_price' => $this->calculateTotalPrice(),
            'quantity' => $this->calculateQuantity(),
            'tax_without_price' => $this->calculateTaxWithoutPrice(),
            'total_price_with_tax' => $this->calculateTotalPriceWithTax(),
            'discount' => $this->calculateDiscount(),
            'total_price_after_discount' => $this->calculateTotalPriceAfterDiscount(),
            'total_price_after_tax_and_discount' => $this->calculateTotalPriceAfterTaxAndDiscount(),
        ];
    }
}
