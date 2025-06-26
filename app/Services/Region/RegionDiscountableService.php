<?php

namespace App\Services\Region;

use App\Contracts\Discount\DiscountableInterface;
use App\Enums\MorphEntity;
use App\Http\Resources\Region\RegionResource;
use App\Models\Discount;
use App\Models\Discountable;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Region;
use App\Repositories\RegionRepository;
use Illuminate\Http\Resources\Json\JsonResource;

class RegionDiscountableService implements DiscountableInterface
{
    public function __construct(
        private RegionRepository $regionRepository,
    ) {
    }
    public function validateRequest(): bool
    {
        request()->validate(['discountable_id' => 'exists:regions,id']);
        return true;
    }
    // public function syncDiscountable(Discount $discount, array $data): void
    // {
    //     $discount->discountables()->where('discountable_type', MorphEntity::CURRENCY)
    //         ->whereNotIn('discountable_id', array_column($data, 'discountable_id'))
    //         ->delete();
    //     $this->attachDiscountable($discount, $data);
    // }

    public function attachDiscountable(Discount $discount, array $data): void
    {
        $region = $this->regionRepository->findById($data['discountable_id']);
        if (!$region) {
            throw new \Exception('Region not found');
        }
        $region->discountables()->create([
            'discount_id' => $discount->id,
        ]);
    }

    public function detachDiscountable(Discount $discount, array $data): void
    {
        $discount->discountables()->where('discountable_type', MorphEntity::CURRENCY)
            ->where('discountable_id', $data['discountable_id'])
            ->delete();
    }

    public function getDiscountableEntityResourceData(JsonResource $resource): array
    {
        return [
            'region' => new RegionResource(
                $resource->discountable
            )
        ];
    }

    public function isDiscountValidForOrderItem(Discountable $discountable, OrderItem $orderItem): bool
    {
        $region = Region::find($discountable->discountable_id);
        if (!$region) {
            return false;
        }
        $orderItemable = $orderItem->orderItemable;
        if (!$orderItemable) {
            return false;
        }

        if (!request()->user()->settings()->whereRelation('region', 'id', $region->id)->exists()) {
            return false;
        }
        return true; // Placeholder return value
    }

    public function isDiscountValidForOrder(Discountable $discountable, Order $order): bool
    {
        $region = Region::find($discountable->discountable_id);
        if (!$region) {
            return false;
        }

        return true;
    }
}
