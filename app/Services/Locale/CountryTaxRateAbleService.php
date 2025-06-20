<?php

namespace App\Services\Locale;

use App\Contracts\Tax\TaxRateAbleInterface;
use App\Enums\MorphEntity;
use App\Http\Resources\Product\CountryResource;
use App\Models\TaxRate;
use App\Repositories\CountryRepository;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryTaxRateAbleService implements TaxRateAbleInterface
{
    public function __construct(
        private CountryRepository $countryRepository,
    ) {
    }
    public function validateRequest(): bool
    {
        request()->validate(['tax_rateable_id' => 'exists:countries,id']);
        return true;
    }
    // public function syncTaxRateAble(TaxRate $taxRate, array $data): void
    // {
    //     $taxRate->taxRateAbles()->where('tax_rateable_type', MorphEntity::COUNTRY)
    //         ->whereNotIn('tax_rateable_id', array_column($data, 'tax_rateable_id'))
    //         ->delete();
    //     $this->attachTaxRateAble($taxRate, $data);
    // }

    public function attachTaxRateAble(TaxRate $taxRate, array $data): void
    {
        $country = $this->countryRepository->findById($data['tax_rateable_id']);
        if (!$country) {
            throw new \Exception('Country not found');
        }
        $country->taxRateAbles()->create([
            'tax_rate_id' => $taxRate->id,
        ]);
    }

    public function detachTaxRateAble(TaxRate $taxRate, array $data): void
    {
        $taxRate->taxRateAbles()->where('tax_rateable_type', MorphEntity::COUNTRY)
            ->where('tax_rateable_id', $data['tax_rateable_id'])
            ->delete();
    }

    public function getTaxRateableEntityResourceData(JsonResource $resource): array
    {
        return [
            'country' => new CountryResource(
                $resource->tax_rateable
            )
        ];
    }
}
