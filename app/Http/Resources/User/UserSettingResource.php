<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Product\CurrencyResource;
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
            'currency' => $this->whenLoaded('currency', CurrencyResource::make($this->currency)),
        ];
    }
}
