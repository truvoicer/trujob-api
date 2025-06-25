<?php

namespace App\Services\Shipping;

use App\Contracts\Discount\DiscountableInterface;
use App\Enums\MorphEntity;
use App\Http\Resources\Shipping\ShippingMethodResource;
use App\Models\Discount;
use App\Models\Discountable;
use App\Models\Order;
use App\Models\OrderItem;
use App\Repositories\ShippingMethodRepository;
use Illuminate\Http\Resources\Json\JsonResource;

class ShippingMethodDiscountableService implements DiscountableInterface
{
    public function __construct(
        private ShippingMethodRepository $shippingMethodRepository,
    ) {
    }
    public function validateRequest(): bool
    {
        request()->validate(['discountable_id' => 'exists:shipping_methods,id']);
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
        $shippingMethod = $this->shippingMethodRepository->findById($data['discountable_id']);
        if (!$shippingMethod) {
            throw new \Exception('Shipping zone not found');
        }
        $shippingMethod->discountables()->create([
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
            'shipping_method' => new ShippingMethodResource(
                $resource->discountable
            )
        ];
    }

    public function isDiscountValidForOrderItem(Discountable $discountable, OrderItem $orderItem): bool
    {
        return true; // Placeholder return value
    }

    public function isDiscountValidForOrder(Discountable $discountable, Order $order): bool
    {
        $shippingMethod = $this->shippingMethodRepository->findById($discountable->discountable_id);
        if (!$shippingMethod) {
            return false;
        }
        return true;
    }
}
