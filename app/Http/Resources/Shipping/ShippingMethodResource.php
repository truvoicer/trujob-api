<?php

namespace App\Http\Resources\Shipping;

use App\Http\Resources\Shipping\Tier\ShippingMethodTierResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ShippingMethodResource extends JsonResource
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
            'processing_time_days' => $this->processing_time_days,
            'display_order' => $this->display_order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'rates' => $this->whenLoaded('rates', ShippingRateResource::collection($this->rates)),
            'restrictions' => $this->whenLoaded(
                'restrictions',
                ShippingRestrictionResource::collection($this->restrictions)
            ),
            'tiers' => $this->whenLoaded(
                'tiers',
                ShippingMethodTierResource::collection($this->tiers)
            ),
        ];
    }
}
