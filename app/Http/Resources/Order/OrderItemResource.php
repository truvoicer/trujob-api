<?php

namespace App\Http\Resources\Order;

use App\Enums\Price\PriceType;
use App\Http\Resources\Discount\DiscountListResource;
use App\Http\Resources\Discount\DiscountResource;
use App\Http\Resources\Product\ProductListResource;
use App\Http\Resources\Tax\TaxRateResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\OrderItem
 */
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
        $this->init();

        return [
            'id' => $this->id,
            'order_itemable_id' => $this->order_itemable_id,
            'order_itemable_type' => $this->order_itemable_type,
            'entity' => ProductListResource::make($this->orderItemable),
            'default_discounts' => DiscountListResource::collection(
                $this->getDefaultDiscounts()
            ),
            'default_tax_rates' => TaxRateResource::collection(
                 $this->getDefaultTaxRates()
            ),
            'discounts' => DiscountResource::collection(
                $this->getDiscounts()
            ),
            'tax_rates' => TaxRateResource::collection(
                $this->getTaxRates()
            ),
            'total_price' => $this->calculateTotalPrice(),
            'quantity' => $this->calculateQuantity(),
            'tax_without_price' => $this->calculateTaxWithoutPrice($this->calculateTotalPrice()),
            'total_price_with_tax' => $this->calculateTotalPriceWithTax(),
            'discount' => $this->calculateDiscount(),
            'total_tax' => $this->calculateTaxWithoutPrice($this->calculateTotalPrice()),
            'total_price_after_discount' => $this->calculateTotalPriceAfterDiscount(),
            'total_price_after_tax_and_discount' => $this->calculateTotalPriceAfterTaxAndDiscount(),
        ];
    }
}
