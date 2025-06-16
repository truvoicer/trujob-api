<?php

namespace App\Services\Category;

use App\Contracts\Shipping\ShippingRestriction;
use App\Enums\MorphEntity;
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
        $data['restrictionable_type'] = MorphEntity::CATEGORY;
        $data['restrictionable_id'] = $data['restriction_id'];
        $shippingRestriction = new ModelsShippingRestriction($data);
        if (!$shippingMethod->restrictions()->save($shippingRestriction)) {
            throw new \Exception('Error creating shipping restriction');
        }
        return $shippingRestriction;
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
