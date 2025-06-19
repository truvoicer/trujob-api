<?php

namespace App\Services\Locale;

use App\Contracts\Tax\TaxRateLocaleInterface;
use App\Enums\MorphEntity;
use App\Http\Resources\Product\CurrencyResource;
use App\Models\TaxRate;
use App\Repositories\CurrencyRepository;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyTaxRateLocaleService implements TaxRateLocaleInterface
{
    public function __construct(
        private CurrencyRepository $currencyRepository,
    ) {
    }
    public function validateRequest(): bool
    {
        request()->validate(['localeable_id' => 'exists:currencies,id']);
        return true;
    }
    // public function syncTaxRateLocale(TaxRate $taxRate, array $data): void
    // {
    //     $taxRate->locales()->where('localeable_type', MorphEntity::CURRENCY)
    //         ->whereNotIn('localeable_id', array_column($data, 'localeable_id'))
    //         ->delete();
    //     $this->attachTaxRateLocale($taxRate, $data);
    // }

    public function attachTaxRateLocale(TaxRate $taxRate, array $data): void
    {
        $taxRate->locales()->create([
            'tax_rate_id' => $taxRate->id,
            'localeable_type' => MorphEntity::CURRENCY,
            'localeable_id' => $data['localeable_id'],
        ]);
    }

    public function detachTaxRateLocale(TaxRate $taxRate, array $data): void
    {
        $taxRate->locales()->where('localeable_type', MorphEntity::CURRENCY)
            ->where('localeable_id', $data['localeable_id'])
            ->delete();
    }

    public function getLocaleableEntityResourceData(JsonResource $resource): array
    {
        return [
            'currency' => new CurrencyResource(
                $resource->localeable
            )
        ];
    }
}
