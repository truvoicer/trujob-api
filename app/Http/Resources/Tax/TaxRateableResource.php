<?php

namespace App\Http\Resources\Tax;

use App\Enums\Order\Tax\TaxRateAbleType;
use App\Factories\Tax\TaxRateAbleFactory;
use Illuminate\Http\Resources\Json\JsonResource;

class TaxRateAbleResource extends JsonResource
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
            'tax_rateable_id' => $this->tax_rateable_id,
            'tax_rateable_type' => $this->tax_rateable_type,
            ...TaxRateAbleFactory::create(
                TaxRateAbleType::tryFrom($this->tax_rateable_type)
            )->getTaxRateableEntityResourceData($this),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
