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
            'shipping_method' => $this->whenLoaded(
                'shippingMethod',
                [
                    'id' => $this->shippingMethod->id,
                    'name' => $this->shippingMethod->name,
                ]
            ),
            'shipping_zone' => $this->whenLoaded('shippingZone', ShippingZoneResource::make(
                $this->shippingZone
            )),
            'rate_type' => $this->rate_type,
            'weight_limit' => $this->weight_limit,
            'height_limit' => $this->height_limit,
            'width_limit' => $this->width_limit,
            'length_limit' => $this->length_limit,
            'weight_unit' => $this->weight_unit,
            'height_unit' => $this->height_unit,
            'width_unit' => $this->width_unit,
            'length_unit' => $this->length_unit,
            'min_weight' => $this->min_weight,
            'max_weight' => $this->max_weight,
            'min_height' => $this->min_height,
            'max_height' => $this->max_height,
            'min_length' => $this->min_length,
            'max_length' => $this->max_length,
            'min_width' => $this->min_width,
            'max_width' => $this->max_width,
            'amount' => $this->amount,
            'currency' => $this->whenLoaded('currency', CurrencyResource::make(
                $this->currency
            )),
            'is_free_shipping_possible' => $this->is_free_shipping_possible,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
