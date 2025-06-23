<?php

namespace App\Http\Resources\Discount;

use App\Enums\Order\Discount\DiscountableType;
use App\Factories\Discount\DiscountableFactory;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscountableResource extends JsonResource
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
            'discountable_id' => $this->discountable_id,
            'discountable_type' => $this->discountable_type,
            ...DiscountableFactory::create(
                DiscountableType::tryFrom($this->discountable_type)
            )->getDiscountableEntityResourceData($this),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
