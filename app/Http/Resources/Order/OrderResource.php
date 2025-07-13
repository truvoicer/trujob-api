<?php

namespace App\Http\Resources\Order;

use App\Enums\Price\PriceType;
use App\Http\Resources\Discount\DiscountListResource;
use App\Http\Resources\Locale\AddressResource;
use App\Http\Resources\Tax\TaxRateResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Order
 */
class OrderResource extends JsonResource
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
            'status' => $this->status,
            'items' => $this->whenLoaded('items', OrderItemResource::collection($this->items)),
            'total_price' => $this->calculateTotalPrice(),
            'total_quantity' => $this->calculateTotalQuantity(),
            'total_tax' => $this->calculateTotalTax(),
            'total_discount' => $this->calculateTotalDiscount(),
            'final_total' => $this->calculateFinalTotal(),
            'total_items' => $this->calculateTotalItems(),
            'average_price_per_item' => $this->calculateAveragePricePerItem(),
            'total_price_after_discounts' => $this->calculateTotalPriceAfterDiscounts(),
            'total_price_after_tax' => $this->calculateTotalPriceAfterTax(),
            'total_price_after_tax_and_discounts' => $this->calculateTotalPriceAfterTaxAndDiscounts(),
            'default_discounts' => DiscountListResource::collection(
                $this->getDefaultDiscounts()
            ),
            'default_tax_rates' => TaxRateResource::collection(
                 $this->getDefaultTaxRates()
            ),
            'billing_address' => $this->whenLoaded('billingAddress', AddressResource::make($this->billingAddress)),
            'shipping_address' => $this->whenLoaded('shippingAddress', AddressResource::make($this->shippingAddress)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
