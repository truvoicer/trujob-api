<?php
namespace App\Helpers;

use App\Enums\Order\OrderItemable;

class ProductHelpers
{
    public static function validateProductableByArray(string $key, array $data): OrderItemable
    {
        $entityType = (!empty($data[$key])) ? $data[$key] : null;
        if (empty($entityType)) {
            throw new \Exception("$key is required to create an order item");
        }

        $orderItemableType = EnumHelpers::getEnumCaseById(OrderItemable::class, $entityType);
        if (!$orderItemableType) {
            throw new \Exception("Invalid $key provided | $entityType");
        }
        return $orderItemableType;
    }
}
