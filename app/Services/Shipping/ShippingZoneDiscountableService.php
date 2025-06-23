<?php

namespace App\Services\Shipping;

use App\Contracts\Discount\DiscountableInterface;
use App\Enums\MorphEntity;
use App\Http\Resources\Shipping\ShippingZoneResource;
use App\Models\Discount;
use App\Models\Discountable;
use App\Models\OrderItem;
use App\Repositories\ShippingZoneRepository;
use Illuminate\Http\Resources\Json\JsonResource;

class ShippingZoneDiscountableService implements DiscountableInterface
{
    public function __construct(
        private ShippingZoneRepository $shippingZoneRepository,
    ) {
    }
    public function validateRequest(): bool
    {
        request()->validate(['discountable_id' => 'exists:shipping_zones,id']);
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
        $shippingZone = $this->shippingZoneRepository->findById($data['discountable_id']);
        if (!$shippingZone) {
            throw new \Exception('Shipping zone not found');
        }
        $shippingZone->discountables()->create([
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
            'shipping_zone' => new ShippingZoneResource(
                $resource->discountable
            )
        ];
    }

    public function isDiscountValidForOrderItem(Discountable $discountable, OrderItem $orderItem): bool
    {
        // Implement logic to check if the discount is valid for the given order item
        // This could involve checking if the order item's category matches the discount's applicable categories
        return true; // Placeholder return value
    }
}
