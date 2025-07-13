<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Product
 */
class ProductAdminListResource extends JsonResource
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
            'sku' => $this->sku,
            'quantity' => $this->quantity,
            'has_height' => $this->has_height,
            'has_depth' => $this->has_depth,
            'has_width' => $this->has_width,
            'has_weight' => $this->has_weight,
            'weight_unit' => $this->weight_unit,
            'height_unit' => $this->height_unit,
            'depth_unit' => $this->depth_unit,
            'width_unit' => $this->width_unit,
            'height' => $this->height,
            'depth' => $this->depth,
            'width' => $this->width,
            'weight' => $this->weight,
            'health_check' => $this->healthCheck(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
