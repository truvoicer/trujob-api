<?php

namespace App\Http\Resources\Product;

use App\Services\Product\ProductsMediaService;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaProductResource extends JsonResource
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
        $data['uri'] = ProductsMediaService::getMediaProductUploadUrl($this->resource);
        return $data;
    }
}
