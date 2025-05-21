<?php

namespace App\Http\Resources\Site\Setting;

use App\Http\Resources\Listing\CountryResource;
use App\Http\Resources\Listing\CurrencyResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SiteSettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'country' => $this->whenLoaded('country', CountryResource::make($this->country)),
            'currency' => $this->whenLoaded('currency', CurrencyResource::make($this->currency)),
        ];
    }
}
