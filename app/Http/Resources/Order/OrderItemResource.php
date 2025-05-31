<?php

namespace App\Http\Resources\Order;

use App\Http\Resources\Product\ProductListResource;
use App\Http\Resources\PaymentGateway\PaymentGatewayResource;
use App\Http\Resources\User\UserResource;
use App\Models\Product;
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
            'productable_id' => $this->productable_id,
            'productable_type' => $this->productable_type,
            'entity' => ProductListResource::make($this->productable)
        ];
    }
}
