<?php

namespace App\Http\Resources\Shipping;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\ShippingZone
 */
class ShippingZoneResource extends JsonResource
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
            'label' => $this->label,
            'name' => $this->name,
            'description' => $this->description,
            'shipping_zoneables' => $this->whenLoaded(
                'shippingZoneAbles',
                ShippingZoneAbleResource::collection($this->shippingZoneAbles)
        ),
            'is_active' => $this->is_active,
            'all' => $this->all,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
