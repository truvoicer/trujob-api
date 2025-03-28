<?php

namespace App\Http\Resources\Listing;

use App\Http\Resources\MediaResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ListingSingleResource extends JsonResource
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
            'type' => $this->whenLoaded('listingType', ListingTypeResource::make($this->listingType)),
            'listingUser' => $this->whenLoaded('user', UserResource::make($this->user)),
            'listingFollow' => $this->whenLoaded('listingFollow', ListingFollowResource::collection($this->listingFollow)),
            'listingFeature' => $this->whenLoaded('listingFeature', ListingFeatureResource::collection($this->listingFeature)),
            'listingReview' => $this->whenLoaded('listingReview', ListingReviewResource::collection($this->listingReview)),
            'listingCategory' => $this->whenLoaded('listingCategory', ListingCategoryResource::collection($this->listingCategory)),
            'listingBrand' => $this->whenLoaded('listingBrand', ListingBrandResource::collection($this->listingBrand)),
            'listingColor' => $this->whenLoaded('listingColor', ListingColorResource::collection($this->listingColor)),
            'listingProductType' => $this->whenLoaded('listingProductType', ListingProductTypeResource::collection($this->listingProductType)),
            'media' => $this->whenLoaded('media', MediaResource::collection($this->media)),
        ];
    }
}
