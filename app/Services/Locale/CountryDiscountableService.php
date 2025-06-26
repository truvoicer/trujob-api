<?php

namespace App\Services\Locale;

use App\Contracts\Discount\DiscountableInterface;
use App\Enums\MorphEntity;
use App\Http\Resources\Country\CountryResource;
use App\Models\Country;
use App\Models\Discount;
use App\Models\Discountable;
use App\Models\Order;
use App\Models\OrderItem;
use App\Repositories\CountryRepository;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryDiscountableService implements DiscountableInterface
{
    public function __construct(
        private CountryRepository $countryRepository,
    ) {
    }
    public function validateRequest(): bool
    {
        request()->validate(['discountable_id' => 'exists:countries,id']);
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
        $country = $this->countryRepository->findById($data['discountable_id']);
        if (!$country) {
            throw new \Exception('Country not found');
        }
        $country->discountables()->create([
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
            'country' => new CountryResource(
                $resource->discountable
            )
        ];
    }

    public function isDiscountValidForOrderItem(Discountable $discountable, OrderItem $orderItem): bool
    {
        $country = Country::find($discountable->discountable_id);
        if (!$country) {
            return false;
        }
        $orderItemable = $orderItem->orderItemable;
        if (!$orderItemable) {
            return false;
        }

        if (!request()->user()->settings()->whereRelation('country', 'id', $country->id)->exists()) {
            return false;
        }
        return true; // Placeholder return value
    }

    public function isDiscountValidForOrder(Discountable $discountable, Order $order): bool
    {
        $country = Country::find($discountable->discountable_id);
        if (!$country) {
            return false;
        }
        return true; // Placeholder return value
    }
}
