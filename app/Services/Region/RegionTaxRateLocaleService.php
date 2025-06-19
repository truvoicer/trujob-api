<?php

namespace App\Services\Region;

use App\Contracts\Tax\TaxRateLocaleInterface;
use App\Enums\MorphEntity;
use App\Http\Resources\Region\RegionResource;
use App\Models\TaxRate;
use App\Repositories\RegionRepository;
use Illuminate\Http\Resources\Json\JsonResource;

class RegionTaxRateLocaleService implements TaxRateLocaleInterface
{
    public function __construct(
        private RegionRepository $regionRepository,
    ) {
    }
    public function validateRequest(): bool
    {
        request()->validate(['localeable_id' => 'exists:regions,id']);
        return true;
    }
    // public function syncTaxRateLocale(TaxRate $taxRate, array $data): void
    // {
    //     $taxRate->locales()->where('localeable_type', MorphEntity::REGION)
    //         ->whereNotIn('localeable_id', array_column($data, 'localeable_id'))
    //         ->delete();
    //     $this->attachTaxRateLocale($taxRate, $data);
    // }

    public function attachTaxRateLocale(TaxRate $taxRate, array $data): void
    {
        $taxRate->locales()->create([
            'tax_rate_id' => $taxRate->id,
            'localeable_type' => MorphEntity::REGION,
            'localeable_id' => $data['localeable_id'],
        ]);
    }

    public function detachTaxRateLocale(TaxRate $taxRate, array $data): void
    {
        $taxRate->locales()->where('localeable_type', MorphEntity::REGION)
            ->where('localeable_id', $data['localeable_id'])
            ->delete();
    }

    public function getLocaleableEntityResourceData(JsonResource $resource): array
    {
        return [
            'region' => new RegionResource(
                $resource->localeable
            )
        ];
    }
}
