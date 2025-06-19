<?php

namespace App\Http\Resources\Tax;

use App\Enums\Order\Tax\TaxRateLocaleType;
use App\Factories\Shipping\TaxRateLocaleFactory;
use Illuminate\Http\Resources\Json\JsonResource;

class TaxRateLocaleResource extends JsonResource
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
            'localeable_id' => $this->localeable_id,
            'localeable_type' => $this->localeable_type,
            ...TaxRateLocaleFactory::create(
                TaxRateLocaleType::tryFrom($this->localeable_type)
            )->getLocaleableEntityResourceData($this),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
