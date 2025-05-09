<?php

namespace App\Http\Resources\Listing;

use App\Http\Resources\MediaResource;
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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'title' => $this->title,
            'description' => $this->description,
            'active' => $this->active,
            'allow_offers' => $this->allow_offers,
            'quantity' => $this->quantity,
            'type' => $this->whenLoaded('types', ListingTypeResource::collection($this->types)),
            'user' => $this->whenLoaded('user', UserResource::make($this->user)),
            'follow' => $this->whenLoaded('listingFollow', ListingFollowResource::collection($this->listingFollow)),
            'feature' => $this->whenLoaded('features', ListingFeatureResource::collection($this->features)),
            'review' => $this->whenLoaded('listingReview', ListingReviewResource::collection($this->listingReview)),
            'category' => $this->whenLoaded('categories', ListingCategoryResource::collection($this->categories)),
            'brand' => $this->whenLoaded('brands', ListingBrandResource::collection($this->brands)),
            'color' => $this->whenLoaded('colors', ListingColorResource::collection($this->colors)),
            'product_type' => $this->whenLoaded('productTypes', ListingProductTypeResource::collection($this->productTypes)),
            'media' => $this->whenLoaded('media', MediaResource::collection($this->media)),
            'price' => $this->whenLoaded('price', ListingPriceResource::collection($this->price)),
        ];
    }
}
