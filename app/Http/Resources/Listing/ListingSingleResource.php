<?php

namespace App\Http\Resources\Listing;

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
        $data = parent::toArray($request);
        $data['listingUser'] = UserResource::make($this->user);
        $data['listingMedia'] = ListingMediaResource::collection($this->listingMedia);
        $data['listingFollow'] = ListingFollowResource::collection($this->listingFollow);
        $data['listingFeature'] = ListingFeatureResource::collection($this->listingFeature);
        $data['listingReview'] = ListingReviewResource::collection($this->listingReview);
        $data['listingCategory'] = ListingCategoryResource::collection($this->listingCategory);
        $data['listingBrand'] = ListingBrandResource::collection($this->listingBrand);
        $data['listingColor'] = ListingColorResource::collection($this->listingColor);
        $data['listingProductType'] = ListingProductTypeResource::collection($this->listingProductType);
        return $data;
    }
}
