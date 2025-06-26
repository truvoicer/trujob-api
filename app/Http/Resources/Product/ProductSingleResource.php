<?php

namespace App\Http\Resources\Product;

use App\Http\Resources\Brand\BrandResource;
use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\Color\ColorResource;
use App\Http\Resources\Feature\FeatureResource;
use App\Http\Resources\Follow\FollowResource;
use App\Http\Resources\MediaResource;
use App\Http\Resources\Price\PriceResource;
use App\Http\Resources\Product\Category\ProductCategoryResource;
use App\Http\Resources\Review\ReviewResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Product
 */
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
            'type' => $this->type,
            'name' => $this->name,
            'title' => $this->title,
            'description' => $this->description,
            'active' => $this->active,
            'allow_offers' => $this->allow_offers,
            'categories' => $this->whenLoaded('types', CategoryResource::collection($this->categories)),
            'user' => $this->whenLoaded('user', UserResource::make($this->user)),
            'follow' => $this->whenLoaded('productFollow', FollowResource::collection($this->productFollow)),
            'feature' => $this->whenLoaded('features', FeatureResource::collection($this->features)),
            'review' => $this->whenLoaded('productReview', ReviewResource::collection($this->productReview)),
            'category' => $this->whenLoaded('categories', CategoryResource::collection($this->categories)),
            'brand' => $this->whenLoaded('brands', BrandResource::collection($this->brands)),
            'color' => $this->whenLoaded('colors', ColorResource::collection($this->colors)),
            'product_categories' => $this->whenLoaded(
                'productCategories',
                ProductCategoryResource::collection($this->productCategories)
            ),
            'media' => $this->whenLoaded('media', MediaResource::collection($this->media)),
            'prices' => $this->whenLoaded('prices', PriceResource::collection($this->prices)),
        ];
    }
}
