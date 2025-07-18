<?php

namespace App\Http\Resources\Price;

use App\Enums\Price\PriceType;
use App\Http\Resources\Country\CountryResource;
use App\Http\Resources\Currency\CurrencyResource;
use App\Http\Resources\Tax\TaxRateResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Discount\DiscountableWithoutEntityResource;

/**
 * @mixin \App\Models\Price
 */
class PriceResource extends JsonResource
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
            'amount' => $this->amount,
            'currency' => $this->whenLoaded('currency', CurrencyResource::make($this->currency)),
            'country' => $this->whenLoaded('country', CountryResource::make($this->country)),
            'created_by_user' => $this->whenLoaded('createdByUser', UserResource::make($this->createdByUser)),
            'price_type' => $this->price_type->listItem(),
            'valid_from' => $this->valid_from,
            'valid_to' => $this->valid_to,
            // 'valid_from_timestamp' => $this->valid_from->timestamp,
            // 'valid_to_timestamp' => $this->valid_to->timestamp,
            'is_active' => $this->is_active,
            'tax_rates' => $this->whenLoaded('taxRates', TaxRateResource::collection($this->taxRates)),
            'discountables' => $this->whenLoaded('discountables', DiscountableWithoutEntityResource::collection($this->discountables)),

            $this->mergeWhen($this->price_type === PriceType::SUBSCRIPTION, [
                'label' => $this->subscription?->label,
                'description' => $this->subscription?->description,
                'setup_fee' => [
                    'value' => $this->subscription?->setup_fee_value,
                    'currency' => $this->subscription?->setupFeeCurrency ? CurrencyResource::make($this->subscription->setupFeeCurrency) : null,
                ],
                'items' => $this->subscription?->items ? $this->subscription->items->map(function ($item) {
                    return [
                        'frequency' => [
                            'interval_unit' => $item->frequency_interval_unit,
                            'interval_count' => $item->frequency_interval_count,
                        ],
                        'tenure_type' => $item->tenure_type,
                        'sequence' => $item->sequence,
                        'total_cycles' => $item->total_cycles,
                        'price' => [
                            'value' => $item->price_value,
                            'currency' => $item->priceCurrency ? CurrencyResource::make($item->priceCurrency) : null,
                        ],
                    ];
                }) : [],
            ]),
            'label' => $this->label,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
