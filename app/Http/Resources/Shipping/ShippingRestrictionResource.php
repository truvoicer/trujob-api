<?php

namespace App\Http\Resources\Shipping;

use App\Enums\Order\Shipping\ShippingRestrictionType;
use App\Factories\Shipping\ShippingRestrictionFactory;
use Illuminate\Http\Resources\Json\JsonResource;

class ShippingRestrictionResource extends JsonResource
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
            'restriction_id' => $this->restrictionable_id,
            'type' => $this->restrictionable_type,
            'action' => $this->action, // Assuming type is an enum or string
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            ...ShippingRestrictionFactory::create(
                ShippingRestrictionType::tryFrom($this->restrictionable_type)
            )
                ->getRestrictionableEntityResourceData($this)

        ];
    }
}
