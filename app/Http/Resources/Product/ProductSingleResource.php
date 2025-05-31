<?php

namespace App\Http\Resources\Product;

use App\Http\Resources\MediaResource;
use App\Http\Resources\PriceResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductSingleResource extends JsonResource
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
            'type' => $this->whenLoaded('types', ProductTypeResource::collection($this->types)),
            'user' => $this->whenLoaded('user', UserResource::make($this->user)),
            'follow' => $this->whenLoaded('productFollow', ProductFollowResource::collection($this->productFollow)),
            'feature' => $this->whenLoaded('features', FeatureResource::collection($this->features)),
            'review' => $this->whenLoaded('productReview', ProductReviewResource::collection($this->productReview)),
            'category' => $this->whenLoaded('categories', CategoryResource::collection($this->categories)),
            'brand' => $this->whenLoaded('brands', BrandResource::collection($this->brands)),
            'color' => $this->whenLoaded('colors', ColorResource::collection($this->colors)),
            'product_type' => $this->whenLoaded('productTypes', ProductTypeResource::collection($this->productTypes)),
            'media' => $this->whenLoaded('media', MediaResource::collection($this->media)),
            'prices' => $this->whenLoaded('prices', PriceResource::collection($this->prices)),
        ];
    }
}
