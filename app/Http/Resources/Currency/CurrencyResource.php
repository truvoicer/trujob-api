<?php

namespace App\Http\Resources\Currency;

use App\Http\Resources\Country\CountryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyResource extends JsonResource
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
            'name_plural' => $this->name_plural,
            'code' => $this->code,
            'symbol' => $this->symbol,
            'is_active' => $this->is_active,
            'country' => $this->whenLoaded('country', CountryResource::make($this->country)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
