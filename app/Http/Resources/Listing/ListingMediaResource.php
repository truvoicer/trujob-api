<?php

namespace App\Http\Resources\Listing;

use App\Services\Listing\ListingsMediaService;
use Illuminate\Http\Resources\Json\JsonResource;

class ListingMediaResource extends JsonResource
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
        $data['uri'] = ListingsMediaService::getListingMediaUploadUrl($this->resource);
        return $data;
    }
}
