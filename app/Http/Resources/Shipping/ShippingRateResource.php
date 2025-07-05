<?php

namespace App\Http\Resources\Shipping;

use App\Http\Resources\Currency\CurrencyResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\ShippingRate
 */
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
            'type' => $this->type,
            'name' => $this->name,
            'label' => $this->label,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'has_max_dimension' => $this->has_max_dimension,
            'max_dimension' => $this->max_dimension,
            'max_dimension_unit' => $this->max_dimension_unit,
            'has_weight' => $this->has_weight,
            'has_height' => $this->has_height,
            'has_width' => $this->has_width,
            'has_depth' => $this->has_depth,
            'weight_unit' => $this->weight_unit,
            'max_weight' => $this->max_weight,
            'height_unit' => $this->height_unit,
            'max_height' => $this->max_height,
            'width_unit' => $this->width_unit,
            'max_width' => $this->max_width,
            'depth_unit' => $this->depth_unit,
            'max_depth' => $this->max_depth,
            'amount' => $this->amount,
            'dimensional_weight_divisor' => $this->dimensional_weight_divisor,
            'currency' => $this->whenLoaded('currency', CurrencyResource::make($this->currency)),
            'shipping_zone' => $this->whenLoaded('shippingZone', ShippingZoneResource::make(
                $this->shippingZone
            )),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
