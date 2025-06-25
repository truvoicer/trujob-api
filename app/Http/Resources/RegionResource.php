<?php

namespace App\Http\Resources;

use App\Http\Resources\Country\CountryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RegionResource extends JsonResource
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
            'is_active' => $this->is_active,
            'admin_name' => $this->admin_name,
            'toponym_name' => $this->toponym_name,
            'category' => $this->category,
            'description' => $this->description, // Assuming 'description' is a field in the Region model
            'lng' => $this->lng, // Assuming 'lng' is a field in the Region model
            'lat' => $this->lat, // Assuming 'lat' is a field in the Region model
            'population' => $this->population, // Assuming 'population' is a field in the Region model
            'country' => $this->whenLoaded('country', new CountryResource($this->country)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
