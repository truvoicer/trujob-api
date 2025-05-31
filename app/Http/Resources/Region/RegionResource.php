<?php
// app/Http/Resources/RegionResource.php

namespace App\Http\Resources\Region;

use App\Http\Resources\Product\CountryResource;
use Illuminate\Http\Resources\Json\JsonResource;
use PHPUnit\Framework\Constraint\Count;

class RegionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'is_active' => $this->is_active,
            'country' => $this->whenLoaded('country', CountryResource::make($this->country)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}