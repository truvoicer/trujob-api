<?php

namespace App\Services\Category;

use App\Contracts\Discount\DiscountableInterface;
use App\Enums\MorphEntity;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Discountable;
use App\Models\Order;
use App\Models\OrderItem;
use App\Repositories\CategoryRepository;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryDiscountableService implements DiscountableInterface
{
    public function __construct(
        private CategoryRepository $categoryRepository,
    ) {
    }
    public function validateRequest(): bool
    {
        request()->validate(['discountable_id' => 'exists:categories,id']);
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
        $category = $this->categoryRepository->findById($data['discountable_id']);
        if (!$category) {
            throw new \Exception('Category not found');
        }
        $category->discountables()->create([
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
            'category' => new CategoryResource(
                $resource->discountable
            )
        ];
    }

    public function isDiscountValidForOrderItem(Discountable $discountable, OrderItem $orderItem): bool
    {
        $price = Category::find($discountable->discountable_id);
        if (!$price) {
            return false;
        }
        $orderItemable = $orderItem->orderItemable;
        if (!$orderItemable) {
            return false;
        }

        if (!$orderItemable->categories()->where('id', $orderItemable->id)->exists()) {
            return false;
        }
        return true; // Placeholder return value
    }
    public function isDiscountValidForOrder(Discountable $discountable, Order $order): bool
    {
        $category = Category::find($discountable->discountable_id);
        if (!$category) {
            return false;
        }

        return true; // Placeholder return value
    }
}
