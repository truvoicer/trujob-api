<?php

namespace App\Http\Resources\Product;

use App\Http\Resources\User\UserResource;
use App\Services\Product\ProductFetchService;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProductListResource extends JsonResource
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
        $data['productMedia'] = MediaProductResource::make($this->productMedia->where('category', 'thumbnail')->first());
        return $data;
    }
}
