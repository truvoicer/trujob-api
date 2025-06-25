<?php

namespace App\Http\Resources\Discount;

use App\Http\Resources\Currency\CurrencyResource;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscountListResource extends JsonResource
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
            'label' => $this->label,
            'description' => $this->description,
            'type' => $this->type,
            'amount_type' => $this->amount_type,
            'amount' => $this->amount,
            'rate' => $this->rate,
            'currency' => $this->whenLoaded('currency', CurrencyResource::make($this->currency)),
        ];
    }
}
