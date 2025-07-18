<?php

namespace App\Http\Resources\Order\PriceType\Subscription;

use App\Enums\Price\PriceType;
use App\Http\Resources\Discount\DiscountListResource;
use App\Http\Resources\Discount\DiscountResource;
use App\Http\Resources\Product\OrderProductListResource;
use App\Http\Resources\Product\ProductListResource;
use App\Http\Resources\Product\ProductSingleResource;
use App\Http\Resources\Tax\TaxRateResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\OrderItem
 */
class SubscriptionOrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $this->setPriceType($this->order->price_type);
        $this->init();

        return [
            'id' => $this->id,
            'order_itemable_id' => $this->order_itemable_id,
            'order_itemable_type' => $this->order_itemable_type,
            'entity' => OrderProductListResource::make($this->orderItemable),
        ];
    }
}
