<?php

namespace App\Http\Resources;

use App\Http\Resources\PaymentGateway\PaymentGatewayResource;
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
            'amount' => $this->amount,
            'price' => $this->whenLoaded('price', PriceResource::make($this->price)),
            'user' => $this->whenLoaded('user', UserResource::make($this->user)),
            'payment_gateway' => $this->whenLoaded('paymentGateway', PaymentGatewayResource::make($this->paymentGateway)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
