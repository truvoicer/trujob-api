<?php

namespace App\Http\Resources\Order;

use App\Enums\Price\PriceType;
use App\Http\Resources\Shipping\ShippingMethodWithProductablesResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Order
 */
class OrderShippingMethodResource extends JsonResource
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
        $this->setShippingMethod($this->additional['shipping_method'] ?? null);
        return [
            'shipping_methods' => ShippingMethodWithProductablesResource::collection(
                $this->availableShippingMethods()
            ),
            // 'total_shipping_cost' => $this->calculateTotalShippingCost(),
            // 'total_price_with_shipping' => $this->calculateTotalPriceWithShipping(),
            // 'total_price_after_discounts' => $this->calculateTotalPriceAfterDiscounts(),
        ];
    }
}
