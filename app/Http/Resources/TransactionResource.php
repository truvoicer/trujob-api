<?php

namespace App\Http\Resources;

use App\Http\Resources\Listing\CountryResource;
use App\Http\Resources\Listing\CurrencyResource;
use App\Http\Resources\PaymentMethod\PaymentMethodResource;
use App\Http\Resources\User\UserResource;
use App\Models\Currency;
use Illuminate\Http\Resources\Json\JsonResource;

class PriceResource extends JsonResource
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
            'currency' => $this->whenLoaded('currency', CurrencyResource::make($this->currency)),
            'payment_method' => $this->whenLoaded('paymentMethod', PaymentMethodResource::make($this->paymentMethod)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
