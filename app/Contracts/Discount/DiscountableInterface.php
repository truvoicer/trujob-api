<?php

namespace App\Contracts\Discount;

use App\Models\Discount;
use App\Models\Discountable;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Resources\Json\JsonResource;

interface DiscountableInterface
{
    public function validateRequest(): bool;
    public function attachDiscountable(Discount $discount, array $data): void;
    public function detachDiscountable(Discount $discount, array $data): void;
    public function getDiscountableEntityResourceData(JsonResource $resource): array;
    public function isDiscountValidForOrderItem(Discountable $discountable, OrderItem $orderItem): bool;
    public function isDiscountValidForOrder(Discountable $discountable, Order $order): bool;
}
