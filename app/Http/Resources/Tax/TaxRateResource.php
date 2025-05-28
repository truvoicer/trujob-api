<?php

namespace App\Http\Resources\Tax;

use App\Http\Resources\Listing\CountryResource;
use App\Http\Resources\Region\RegionResource;
use Illuminate\Http\Resources\Json\JsonResource;

class TaxRateResource extends JsonResource
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
            'type' => $this->name,
            'amount' => $this->name,
            'rate' => $this->name,
            'country' => $this->whenLoaded('country', CountryResource::make($this->country)),
            'region' => $this->whenLoaded('region', RegionResource::make($this->region)),
            'is_default' => $this->name,
            'scope' => $this->name,
            'is_active' => $this->name,
            'fixed_rate' => $this->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
