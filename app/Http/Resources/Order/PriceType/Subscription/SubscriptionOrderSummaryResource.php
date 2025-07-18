<?php

namespace App\Http\Resources\Order\PriceType\Subscription;

use App\Http\Resources\Discount\DiscountListResource;
use App\Http\Resources\Tax\TaxRateResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Order
 */
class SubscriptionOrderSummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $this->setPriceType($this->price_type);
        $this->init();
        return [
            'id' => $this->id,
            'status' => $this->status,
            'items' => $this->whenLoaded('items', SubscriptionOrderItemResource::collection($this->items)),
            'total_price' => $this->calculateTotalPrice(),
            'total_quantity' => $this->calculateTotalQuantity(),
            'total_tax' => $this->calculateTotalTax(),
            'total_discount' => $this->calculateTotalDiscount(),
            'final_total' => $this->calculateFinalTotal(),
            'total_items' => $this->calculateTotalItems(),
            'average_price_per_item' => $this->calculateAveragePricePerItem(),
            'total_shipping_cost' => $this->calculateTotalShippingCost(),
            'total_price_with_shipping' => $this->calculateTotalPriceWithShipping(),
            'total_price_after_discounts' => $this->calculateTotalPriceAfterDiscounts(),
            'total_price_after_tax' => $this->calculateTotalPriceAfterTax(),
            'total_price_after_tax_and_discounts' => $this->calculateTotalPriceAfterTaxAndDiscounts(),
            'default_discounts' => DiscountListResource::collection(
                $this->getDefaultDiscounts()
            ),
            'default_tax_rates' => TaxRateResource::collection(
                 $this->getDefaultTaxRates()
            ),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
