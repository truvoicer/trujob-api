<?php

namespace App\Http\Resources\Listing;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ListingPriceResource extends JsonResource
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
            'listing' => $this->whenLoaded('listing', ListingListResource::make($this->listing)),
            'price' => $this->whenLoaded('price', PriceResource::make($this->price)),
        ];
    }
}
