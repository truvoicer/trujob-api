<?php

namespace App\Http\Resources;

use App\Http\Resources\Listing\CountryResource;
use App\Http\Resources\Listing\CurrencyResource;
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
            'currency' => $this->whenLoaded('currency', CurrencyResource::make($this->currency)),
            'country' => $this->whenLoaded('country', CountryResource::make($this->country)),
            'user' => $this->whenLoaded('user', UserResource::make($this->user)),
            'type' => $this->whenLoaded('type', PriceTypeResource::make($this->type)),
            'valid_from' => $this->valid_from,
            'valid_to' => $this->valid_to,
            'is_default' => $this->is_default,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
