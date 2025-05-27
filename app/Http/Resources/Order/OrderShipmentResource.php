<?php

namespace App\Http\Resources\Order;

use App\Http\Resources\Listing\ListingListResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderShipmentResource extends JsonResource
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
            'shipping_method' => $this->whenLoaded('shippingMethod', function () {
                return ListingListResource::make($this->shippingMethod);
            }),
            'tracking_number' => $this->tracking_number,
            'carrier' => $this->carrier,
            'weight' => $this->weight,
            'dimensions' => $this->dimensions,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
