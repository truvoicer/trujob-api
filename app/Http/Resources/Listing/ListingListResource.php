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
            'listing_type' => $this->whenLoaded('listingType', ListingTypeResource::make($this->listingType)),
            'listing_user' => $this->whenLoaded('user', UserResource::make($this->user)),
            'listing_follow' => $this->whenLoaded('listingFollow', ListingFollowResource::collection($this->listingFollow)),
            'listing_feature' => $this->whenLoaded('listingFeature', ListingFeatureResource::collection($this->listingFeature)),
            'listing_review' => $this->whenLoaded('listingReview', ListingReviewResource::collection($this->listingReview)),
            'listing_category' => $this->whenLoaded('listingCategory', ListingCategoryResource::collection($this->listingCategory)),
            'listing_brand' => $this->whenLoaded('listingBrand', ListingBrandResource::collection($this->listingBrand)),
            'listing_color' => $this->whenLoaded('listingColor', ListingColorResource::collection($this->listingColor)),
            'listing_product_type' => $this->whenLoaded('listingProductType', ListingProductTypeResource::collection($this->listingProductType)),
            'media' => $this->whenLoaded('media', MediaResource::collection($this->media)),
            'price' => $this->whenLoaded('price', ListingPriceResource::collection($this->price)),
        ];
    }
}
