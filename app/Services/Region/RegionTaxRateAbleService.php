<?php

namespace App\Services\Region;

use App\Contracts\Tax\TaxRateAbleInterface;
use App\Enums\MorphEntity;
use App\Http\Resources\Region\RegionResource;
use App\Models\TaxRate;
use App\Repositories\RegionRepository;
use Illuminate\Http\Resources\Json\JsonResource;

class RegionTaxRateAbleService implements TaxRateAbleInterface
{
    public function __construct(
        private RegionRepository $regionRepository,
    ) {
    }
    public function validateRequest(): bool
    {
        request()->validate(['tax_rateable_id' => 'exists:regions,id']);
        return true;
    }
    // public function syncTaxRateAble(TaxRate $taxRate, array $data): void
    // {
    //     $taxRate->taxRateAbles()->where('tax_rateable_type', MorphEntity::REGION)
    //         ->whereNotIn('tax_rateable_id', array_column($data, 'tax_rateable_id'))
    //         ->delete();
    //     $this->attachTaxRateAble($taxRate, $data);
    // }

    public function attachTaxRateAble(TaxRate $taxRate, array $data): void
    {
        $region = $this->regionRepository->findById($data['tax_rateable_id']);
        if (!$region) {
            throw new \Exception('Region not found');
        }
        $region->taxRateAbles()->create([
            'tax_rate_id' => $taxRate->id,
        ]);
    }

    public function detachTaxRateAble(TaxRate $taxRate, array $data): void
    {
        $taxRate->taxRateAbles()->where('tax_rateable_type', MorphEntity::REGION)
            ->where('tax_rateable_id', $data['tax_rateable_id'])
            ->delete();
    }

    public function getTaxRateableEntityResourceData(JsonResource $resource): array
    {
        return [
            'region' => new RegionResource(
                $resource->tax_rateable
            )
        ];
    }
}
