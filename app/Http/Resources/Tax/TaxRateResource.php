<?php

namespace App\Http\Resources\Tax;

use App\Enums\Order\Tax\TaxRateLocaleType;
use App\Factories\Shipping\TaxRateLocaleFactory;
use App\Http\Resources\Product\CountryResource;
use App\Http\Resources\Product\CurrencyResource;
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
            'type' => $this->type,
            'amount_type' => $this->amount_type,
            'amount' => $this->amount,
            'rate' => $this->rate,
            'country' => $this->whenLoaded('country', CountryResource::make($this->country)),
            'currency' => $this->whenLoaded('currency', CurrencyResource::make($this->currency)),
            'has_region' => $this->has_region,
            'region' => $this->whenLoaded('region', RegionResource::make($this->region)),
            'is_default' => $this->isDefault(),
            'scope' => $this->scope,
            'is_active' => $this->is_active,
            'locales' => $this->whenLoaded('locales', TaxRateLocaleResource::collection($this->locales)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
