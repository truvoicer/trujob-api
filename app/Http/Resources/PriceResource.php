<?php

namespace App\Http\Resources\Listing;

use App\Http\Resources\User\UserResource;
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
            'created_by_user' => $this->whenLoaded('createdByUser', UserResource::make($this->createdByUser)),
            'country' => $this->whenLoaded('country', CountryResource::make($this->country)),
            'currency' => $this->whenLoaded('currency', CurrencyResource::make($this->currency)),
            'type' => $this->type,
            'amount' => $this->amount,
        ];
    }
}
