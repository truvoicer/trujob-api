<?php

namespace App\Services\Category;

use App\Contracts\Tax\TaxRateAbleInterface;
use App\Enums\MorphEntity;
use App\Http\Resources\Product\CategoryResource;
use App\Models\TaxRate;
use App\Repositories\CategoryRepository;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryTaxRateAbleService implements TaxRateAbleInterface
{
    public function __construct(
        private CategoryRepository $categoryRepository,
    ) {
    }
    public function validateRequest(): bool
    {
        request()->validate(['tax_rateable_id' => 'exists:categories,id']);
        return true;
    }
    // public function syncTaxRateAble(TaxRate $taxRate, array $data): void
    // {
    //     $taxRate->taxRateAbles()->where('tax_rateable_type', MorphEntity::CATEGORY)
    //         ->whereNotIn('tax_rateable_id', array_column($data, 'tax_rateable_id'))
    //         ->delete();
    //     $this->attachTaxRateAble($taxRate, $data);
    // }

    public function attachTaxRateAble(TaxRate $taxRate, array $data): void
    {
        $category = $this->categoryRepository->findById($data['tax_rateable_id']);
        if (!$category) {
            throw new \Exception('Category not found');
        }
        $category->taxRateAbles()->create([
            'tax_rate_id' => $taxRate->id,
        ]);
    }

    public function detachTaxRateAble(TaxRate $taxRate, array $data): void
    {
        $taxRate->taxRateAbles()->where('tax_rateable_type', MorphEntity::CATEGORY)
            ->where('tax_rateable_id', $data['tax_rateable_id'])
            ->delete();
    }

    public function getTaxRateableEntityResourceData(JsonResource $resource): array
    {
        return [
            'category' => new CategoryResource(
                $resource->tax_rateable
            )
        ];
    }
}
