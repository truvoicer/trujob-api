<?php

namespace App\Services\Locale;

use App\Contracts\Tax\TaxRateAbleInterface;
use App\Enums\MorphEntity;
use App\Http\Resources\Product\CurrencyResource;
use App\Models\TaxRate;
use App\Repositories\CurrencyRepository;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyTaxRateAbleService implements TaxRateAbleInterface
{
    public function __construct(
        private CurrencyRepository $currencyRepository,
    ) {
    }
    public function validateRequest(): bool
    {
        request()->validate(['tax_rateable_id' => 'exists:currencies,id']);
        return true;
    }
    // public function syncTaxRateAble(TaxRate $taxRate, array $data): void
    // {
    //     $taxRate->taxRateAbles()->where('tax_rateable_type', MorphEntity::CURRENCY)
    //         ->whereNotIn('tax_rateable_id', array_column($data, 'tax_rateable_id'))
    //         ->delete();
    //     $this->attachTaxRateAble($taxRate, $data);
    // }

    public function attachTaxRateAble(TaxRate $taxRate, array $data): void
    {
        $currency = $this->currencyRepository->findById($data['tax_rateable_id']);
        if (!$currency) {
            throw new \Exception('Currency not found');
        }
        $currency->taxRateAbles()->create([
            'tax_rate_id' => $taxRate->id,
        ]);
    }

    public function detachTaxRateAble(TaxRate $taxRate, array $data): void
    {
        $taxRate->taxRateAbles()->where('tax_rateable_type', MorphEntity::CURRENCY)
            ->where('tax_rateable_id', $data['tax_rateable_id'])
            ->delete();
    }

    public function getTaxRateableEntityResourceData(JsonResource $resource): array
    {
        return [
            'currency' => new CurrencyResource(
                $resource->tax_rateable
            )
        ];
    }
}
