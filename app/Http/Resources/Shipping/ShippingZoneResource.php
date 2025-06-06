<?php

namespace App\Http\Resources\Shipping;

use App\Http\Resources\Product\CountryResource;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'name' => $this->name,
            'description' => $this->description,
            'countries' => $this->whenLoaded(
                'countries', 
                CountryResource::collection($this->countries)
            ),
            'is_active' => $this->is_active,
            'all' => $this->all,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
