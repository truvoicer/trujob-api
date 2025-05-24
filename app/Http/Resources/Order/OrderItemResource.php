<?php

namespace App\Http\Resources\Order;

use App\Http\Resources\Listing\ListingListResource;
use App\Http\Resources\PaymentGateway\PaymentGatewayResource;
use App\Http\Resources\User\UserResource;
use App\Models\Listing;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
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
            'quantity' => $this->quantity,
            'order_itemable_id' => $this->order_itemable_id,
            'order_itemable_type' => $this->order_itemable_type,
            'entity' => ListingListResource::make($this->orderItemable)
        ];
    }
}
