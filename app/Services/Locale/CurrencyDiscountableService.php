<?php

namespace App\Services\Locale;

use App\Contracts\Discount\DiscountableInterface;
use App\Enums\MorphEntity;
use App\Http\Resources\Currency\CurrencyResource;
use App\Models\Currency;
use App\Models\Discount;
use App\Models\Discountable;
use App\Models\Order;
use App\Models\OrderItem;
use App\Repositories\CurrencyRepository;
use App\Services\BaseService;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyDiscountableService extends BaseService implements DiscountableInterface
{
    public function __construct(
        private CurrencyRepository $currencyRepository,
    ) {
    }
    public function validateRequest(): bool
    {
        request()->validate(['discountable_id' => 'exists:currencies,id']);
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
        $currency = $this->currencyRepository->findById($data['discountable_id']);
        if (!$currency) {
            throw new \Exception('Currency not found');
        }
        $currency->discountables()->create([
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
            'currency' => new CurrencyResource(
                $resource->discountable
            )
        ];
    }

    public function isDiscountValidForOrderItem(Discountable $discountable, OrderItem $orderItem): bool
    {
        $currency = Currency::find($discountable->discountable_id);
        if (!$currency) {
            return false;
        }
        $orderItemable = $orderItem->orderItemable;
        if (!$orderItemable) {
            return false;
        }

        if (!request()->user()->settings()->whereRelation('currency', 'id', $currency->id)->exists()) {
            return false;
        }
        return true; // Placeholder return value
    }

    public function isDiscountValidForOrder(Discountable $discountable, Order $order): bool
    {
        $currency = Currency::find($discountable->discountable_id);
        if (!$currency) {
            return false;
        }

        return true; // Placeholder return value
    }
}
