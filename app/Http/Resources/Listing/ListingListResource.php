<?php

namespace App\Http\Resources\Listing;

use App\Http\Resources\User\UserResource;
use App\Services\Listing\ListingsFetchService;
use Illuminate\Http\Resources\Json\JsonResource;

class ListingListResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = parent::toArray($request);
        $data['listingUser'] = UserResource::make($this->user);
        $data['listingMedia'] = ListingMediaResource::make($this->listingMedia->where('category', 'thumbnail')->first());
        return $data;
    }
}
