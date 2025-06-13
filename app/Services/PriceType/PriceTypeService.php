<?php
namespace App\Services\PriceType;

use App\Enums\Price\PriceType as PricePriceType;
use App\Models\Price;
use App\Models\PriceType;
use App\Services\BaseService;

class PriceTypeService extends BaseService
{
    // public function calculateFinalPriceType(): float
    // {
    //     $discountedPriceType = $this->priceType - ($this->priceType * ($this->discount / 100));
    //     $finalPriceType = $discountedPriceType + ($discountedPriceType * ($this->tax / 100));
    //     return round($finalPriceType, 2);
    // }

    public function attachBulkTypesToPrice(Price $price, array $types): bool
    {
        $result = $price->types()->createMany($types);
        return true;
    }

    public function detachBulkTypesFromPrice(Price $price, array $typeIds): bool
    {
        $result = $price->types()->whereIn('id', $typeIds)->delete();
        return true;
    }

    public function createPriceType(array $data): bool
    {
        $priceType = new PriceType($data);
        if (!$priceType->save()) {
            throw new \Exception('Error creating priceType');
        }
        return true;
    }

    public function updatePriceType(PriceType $priceType, array $data): bool
    {
        if (!$priceType->update($data)) {
            throw new \Exception('Error updating priceType');
        }
        return true;
    }
    public function deletePriceType(PriceType $priceType): bool
    {
        if (!$priceType->delete()) {
            throw new \Exception('Error deleting priceType');
        }
        return true;
    }

    public function defaultPriceTypes() {
        $data = include_once(database_path('data/PriceTypeData.php'));
        if (!$data) {
            throw new \Exception('Error reading PriceTypeData.php file ' . database_path('data/PriceTypeData.php'));
        }
        foreach (PricePriceType::cases() as $priceType) {
            $atts = [
                'name' => $priceType->value,
            ];
            $findInData = array_search($priceType->value, array_column($data, 'name'));
            if ($findInData !== false) {
                $atts = [
                    ...$atts,
                    ...$data[$findInData],
                ];
            }
            PriceType::query()->updateOrCreate(
                ['name' => $priceType],
                $atts
            );
        }
    }

    public function destroyBulkPriceTypes(array $ids): bool
    {
        $priceTypes = PriceType::whereIn('id', $ids)->get();
        if ($priceTypes->isEmpty()) {
            throw new \Exception('No price types found for the given IDs');
        }
        foreach ($priceTypes as $priceType) {
            if (!$priceType->delete()) {
                throw new \Exception('Error deleting price type with ID: ' . $priceType->id);
            }
        }
        return true;
    }

}
