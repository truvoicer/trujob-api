<?php

namespace App\Services\Category;

use App\Contracts\Shipping\ShippingZoneAbleInterface;
use App\Enums\MorphEntity;
use App\Http\Resources\Category\CategoryResource;
use App\Models\ShippingZone;
use App\Repositories\CategoryRepository;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryShippingZoneAbleService implements ShippingZoneAbleInterface
{
    public function __construct(
        private CategoryRepository $categoryRepository,
    ) {
    }
    public function validateRequest(): bool
    {
        request()->validate(['shipping_zoneable_id' => 'exists:categories,id']);
        return true;
    }
    // public function syncShippingZoneAble(ShippingZone $shippingZone, array $data): void
    // {
    //     $shippingZone->shippingZoneAbles()->where('shipping_zoneable_type', MorphEntity::CATEGORY->value)
    //         ->whereNotIn('shipping_zoneable_id', array_column($data, 'shipping_zoneable_id'))
    //         ->delete();
    //     $this->attachShippingZoneAble($shippingZone, $data);
    // }

    public function attachShippingZoneAble(ShippingZone $shippingZone, array $data): void
    {
        $category = $this->categoryRepository->findById($data['shipping_zoneable_id']);
        if (!$category) {
            throw new \Exception('Category not found');
        }
        $category->shippingZoneable()->create([
            'shipping_zone_id' => $shippingZone->id,
        ]);
    }

    public function detachShippingZoneAble(ShippingZone $shippingZone, array $data): void
    {
        $shippingZone->shippingZoneAbles()->where('shipping_zoneable_type', MorphEntity::CATEGORY->value)
            ->where('shipping_zoneable_id', $data['shipping_zoneable_id'])
            ->delete();
    }

    public function getShippingZoneableEntityResourceData(JsonResource $resource): array
    {
        return [
            'category' => new CategoryResource(
                $resource->shipping_zoneable
            )
        ];
    }
}
