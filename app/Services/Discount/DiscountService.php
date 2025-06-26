<?php

namespace App\Services\Discount;

use App\Enums\Order\Discount\DiscountableType;
use App\Factories\Order\OrderItemFactory;
use App\Factories\Discount\DiscountableFactory;
use App\Helpers\ProductHelpers;
use App\Models\Discount;
use App\Models\Price;
use App\Services\BaseService;
use Illuminate\Support\Str;

class DiscountService extends BaseService
{

    public function createDiscount(array $data)
    {
        $discountables = $data['discountables'] ?? null;
        if (isset($data['discountables'])) {
            unset($data['discountables']);
        }

        $isDefault = $data['is_default'] ?? null;
        if (isset($data['is_default'])) {
            unset($data['is_default']);
        }

        if (empty($data['name'])) {
            $data['name'] = Str::slug($data['label']);
        }
        $discount = new Discount($data);
        if (!$discount->save()) {
            throw new \Exception('Error creating discount');
        }

        if (is_array($discountables) && count($discountables) > 0) {
            $this->syncDiscountables($discount, $discountables);
        }
        if ($isDefault !== null) {
            if ($isDefault) {
                $this->setAsDefault($discount);
            } else {
                $this->removeAsDefault($discount);
            }
        }

        return true;
    }
    public function updateDiscount(Discount $discount, array $data)
    {
        $discountables = $data['discountables'] ?? null;
        if (isset($data['discountables'])) {
            unset($data['discountables']);
        }

        $isDefault = $data['is_default'] ?? null;
        if (isset($data['is_default'])) {
            unset($data['is_default']);
        }

        if (!$discount->update($data)) {
            throw new \Exception('Error updating discount');
        }

        if (is_array($discountables) && count($discountables) > 0) {
            $this->syncDiscountables($discount, $discountables);
        }

        if ($isDefault !== null) {
            if ($isDefault) {
                $this->setAsDefault($discount);
            } else {
                $this->removeAsDefault($discount);
            }
        }

        return true;
    }
    public function syncDiscountables(Discount $discount, array $data): void
    {
        $groupedData = collect($data)->groupBy('discountable_type');
        foreach ($groupedData as $discountableType => $discountables) {
            $discountables = $discountables->toArray();
            $discount->discountables()->where('discountable_type', $discountableType)
                ->whereNotIn('discountable_id', array_column($discountables, 'discountable_id'))
                ->delete();
            foreach ($discountables as $locale) {
                DiscountableFactory::create(DiscountableType::tryFrom($discountableType))
                    ->attachDiscountable($discount, $locale);
            }
        }
    }

    public function setAsDefault(Discount $discount): void
    {
        if (!$discount->default) {
            $discount->default()->create();
        }
    }
    public function removeAsDefault(Discount $discount): void
    {
        if ($discount->default) {
            $discount->default()->delete();
        }
    }

    public function deleteDiscount(Discount $discount)
    {
        if (!$discount->delete()) {
            throw new \Exception('Error deleting discount');
        }
        return true;
    }
}
