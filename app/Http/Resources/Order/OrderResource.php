<?php

namespace App\Http\Resources\Order;

use App\Http\Resources\PaymentGateway\PaymentGatewayResource;
use App\Http\Resources\PriceResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'user' => $this->whenLoaded('user', UserResource::make($this->user)),
            'status' => $this->status,
            'items' => $this->whenLoaded('items', OrderItemResource::collection($this->items)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
