<?php

namespace App\Services\Category;

use App\Contracts\Shipping\TaxRateLocaleInterface;
use App\Enums\MorphEntity;
use App\Http\Resources\Product\CategoryResource;
use App\Models\TaxRate;
use App\Repositories\CategoryRepository;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryTaxRateLocaleService implements TaxRateLocaleInterface
{
    public function __construct(
        private CategoryRepository $categoryRepository,
    ) {
    }
    public function validateRequest(): bool
    {
        request()->validate(['localeable_id' => 'exists:categories,id']);
        return true;
    }
    // public function syncTaxRateLocale(TaxRate $taxRate, array $data): void
    // {
    //     $taxRate->locales()->where('localeable_type', MorphEntity::CATEGORY)
    //         ->whereNotIn('localeable_id', array_column($data, 'localeable_id'))
    //         ->delete();
    //     $this->attachTaxRateLocale($taxRate, $data);
    // }

    public function attachTaxRateLocale(TaxRate $taxRate, array $data): void
    {
        $taxRate->locales()->create([
            'tax_rate_id' => $taxRate->id,
            'localeable_type' => MorphEntity::CATEGORY,
            'localeable_id' => $data['localeable_id'],
        ]);
    }

    public function detachTaxRateLocale(TaxRate $taxRate, array $data): void
    {
        $taxRate->locales()->where('localeable_type', MorphEntity::CATEGORY)
            ->where('localeable_id', $data['localeable_id'])
            ->delete();
    }

    public function getLocaleableEntityResourceData(JsonResource $resource): array
    {
        return [
            'category' => new CategoryResource(
                $resource->localeable
            )
        ];
    }
}
