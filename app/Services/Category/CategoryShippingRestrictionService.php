<?php

namespace App\Services\Category;

use App\Contracts\Shipping\ShippingRestriction;
use App\Enums\MorphEntity;
use App\Enums\Order\Shipping\ShippingRestrictionAction;
use App\Http\Resources\Product\CategoryResource;
use App\Models\Category;
use App\Models\ShippingMethod;
use App\Repositories\CategoryRepository;
use App\Models\ShippingRestriction as ModelsShippingRestriction;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryShippingRestrictionService implements ShippingRestriction
{
    public function __construct(
        private CategoryRepository $categoryRepository,
    ) {}
    public function validateRequest(): bool
    {
        request()->validate(['restriction_id' => 'exists:categories,id']);
        return true;
    }
    public function storeShippingRestriction(ShippingMethod $shippingMethod, array $data): ModelsShippingRestriction
    {
        $category = $this->categoryRepository->findById($data['restriction_id']);
        if (!$category) {
            throw new \Exception('Category not found');
        }
        return $category->shippingRestrictions()->create([
            'shipping_method_id' => $shippingMethod->id,
        ]);
    }
    public function updateShippingRestriction(
        ModelsShippingRestriction $shippingRestriction,
        array $data
    ): ModelsShippingRestriction {
        if (!$shippingRestriction->update($data)) {
            throw new \Exception('Error updating shipping restriction');
        }
        return $shippingRestriction;
    }
    public function deleteShippingRestriction(ModelsShippingRestriction $shippingRestriction): bool
    {
        if (!$shippingRestriction->delete()) {
            throw new \Exception('Error deleting shipping restriction');
        }
        return true;
    }

    public function getRestrictionableEntityResourceData(JsonResource $resource): array
    {
        return [
            'category' => new CategoryResource(
                $resource->restrictionable
            )
        ];
    }
}
