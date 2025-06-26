<?php

namespace App\Services\Price;

use App\Contracts\Discount\DiscountableInterface;
use App\Enums\MorphEntity;
use App\Http\Resources\Price\PriceResource;
use App\Models\Discount;
use App\Models\Discountable;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Price;
use App\Repositories\PriceRepository;
use Illuminate\Http\Resources\Json\JsonResource;

class PriceDiscountableService implements DiscountableInterface
{
    public function __construct(
        private PriceRepository $priceRepository,
    ) {
    }
    public function validateRequest(): bool
    {
        request()->validate(['discountable_id' => 'exists:prices,id']);
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
        $price = $this->priceRepository->findById($data['discountable_id']);
        if (!$price) {
            throw new \Exception('Price not found');
        }
        $price->discountables()->create([
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
            'price' => new PriceResource(
                $resource->discountable
            )
        ];
    }

    public function isDiscountValidForOrderItem(Discountable $discountable, OrderItem $orderItem): bool
    {
        $price = Price::find($discountable->discountable_id);
        if (!$price) {
            return false; // Price not found, discount is not valid
        }
        $orderItemable = $orderItem->orderItemable;
        if (!$orderItemable) {
            return false; // No orderItemable associated with the order item
        }

        $product = $price->products()
        ->where('id', $orderItemable->id);
        if (!$product) {
            return false; // Product not found in the price's products
        }
        return true; // Placeholder return value
    }

    public function isDiscountValidForOrder(Discountable $discountable, Order $order): bool
    {
        $price = Price::find($discountable->discountable_id);
        if (!$price) {
            return false; // Price not found, discount is not valid
        }
        return true; // Placeholder return value
    }
}
