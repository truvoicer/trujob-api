<?php

namespace App\Http\Resources\Shipping;

use App\Http\Resources\Product\CountryResource;
use App\Http\Resources\Product\CurrencyResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ShippingRateResource extends JsonResource
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
            'shipping_method' => $this->whenLoaded('shippingMethod', ShippingMethodResource::make(
                $this->shippingMethod
            )),
            'shippingZone' => $this->whenLoaded('shippingZone', ShippingZoneResource::make(
                $this->shippingZone
            )),
            'rate_type' => $this->rate_type,
            'min_value' => $this->min_value,
            'max_value' => $this->max_value,
            'rate_amount' => $this->rate_amount,
            'currency' => $this->whenLoaded('currency', CurrencyResource::make(
                $this->currency
            )),
            'is_free_shipping_possible' => $this->is_free_shipping_possible,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
