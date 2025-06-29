<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Country\CountryResource;
use App\Http\Resources\Currency\CurrencyResource;
use App\Http\Resources\Language\LanguageResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserSettingResource extends JsonResource
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
            'app_theme' => $this->app_theme,
            'push_notification' => $this->push_notification,
            'timezone' => $this->timezone,
            'currency' => $this->whenLoaded('currency', CurrencyResource::make($this->currency)),
            'country' => $this->whenLoaded('country', CountryResource::make($this->country)),
            'language' => $this->whenLoaded('currency', LanguageResource::make($this->language)),
        ];
    }
}
