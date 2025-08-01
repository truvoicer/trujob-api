<?php

namespace App\Http\Resources\Site\Setting;

use App\Http\Resources\Country\CountryResource;
use App\Http\Resources\Currency\CurrencyResource;
use App\Http\Resources\Language\LanguageResource;
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
            'frontend_url' => $this->frontend_url,
            'locale' => $this->locale,
            'timezone' => $this->timezone,
            'country' => $this->whenLoaded('country', CountryResource::make($this->country)),
            'currency' => $this->whenLoaded('currency', CurrencyResource::make($this->currency)),
            'language' => $this->whenLoaded('language', LanguageResource::make($this->language)),
        ];
    }
}
