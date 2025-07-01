<?php

namespace App\Http\Resources\Shipping\Tier;

use App\Http\Resources\Currency\CurrencyResource;
use App\Http\Resources\Shipping\ShippingMethodResource;
use App\Models\Currency;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ShippingMethodTierResource
 *
 * @package App\Http\Resources\Shipping\Tier
 * @mixin \App\Models\ShippingMethodTier
 */
class ShippingMethodTierResource extends JsonResource
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
            'has_length' => $this->has_length,
            'weight_unit' => $this->weight_unit,
            'max_weight' => $this->max_weight,
            'height_unit' => $this->height_unit,
            'max_height' => $this->max_height,
            'width_unit' => $this->width_unit,
            'max_width' => $this->max_width,
            'length_unit' => $this->length_unit,
            'max_length' => $this->max_length,
            'base_amount' => $this->base_amount,
            'dimensional_weight_divisor' => $this->dimensional_weight_divisor,
            'currency' => $this->whenLoaded('currency', CurrencyResource::make($this->currency)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
