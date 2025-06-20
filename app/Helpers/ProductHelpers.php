<?php
namespace App\Helpers;

use App\Enums\Product\ProductType;

class ProductHelpers
{
    public static function validateProductableByArray(string $key, array $data): ProductType
    {
        $entityType = (!empty($data[$key])) ? $data[$key] : null;
        if (empty($entityType)) {
            throw new \Exception("$key is required to create an order item");
        }

        $productType = EnumHelpers::getEnumCaseById(ProductType::class, $entityType);
        if (!$productType) {
            throw new \Exception("Invalid $key provided | $entityType");
        }
        return $productType;
    }
}
