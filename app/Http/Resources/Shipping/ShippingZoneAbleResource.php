<?php

namespace App\Http\Resources\Shipping;

use App\Enums\Order\Shipping\ShippingZoneAbleType;
use App\Factories\Shipping\ShippingZoneAbleFactory;
use Illuminate\Http\Resources\Json\JsonResource;

class ShippingZoneAbleResource extends JsonResource
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
            'shipping_zoneable_id' => $this->shipping_zoneable_id,
            'shipping_zoneable_type' => $this->shipping_zoneable_type,
            ...ShippingZoneAbleFactory::create(
                ShippingZoneAbleType::tryFrom($this->shipping_zoneable_type)
            )->getShippingZoneAbleEntityResourceData($this),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
