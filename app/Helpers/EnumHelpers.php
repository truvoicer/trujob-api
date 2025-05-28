<?php
namespace App\Helpers;

use BackedEnum;
use UnitEnum;

class EnumHelpers {
    /**
     * Get the enum case by its value.
     *
     * @param string $enumClass
     * @param mixed $value
     * @return mixed
     */
    public static function getEnumCaseById(string $enumClass, string $id): BackedEnum | UnitEnum | null 
    {
        if (!is_subclass_of($enumClass, BackedEnum::class) && !is_subclass_of($enumClass, UnitEnum::class)) {
            throw new \InvalidArgumentException("The class $enumClass must be a backed enum or unit enum.");
        }
        if (!enum_exists($enumClass)) {
            throw new \InvalidArgumentException("The class $enumClass is not an enum.");
        }
        if (!is_string($id)) {
            throw new \InvalidArgumentException("The id must be a string.");
        }
        foreach ($enumClass::cases() as $case) {
            if ($case->id() === $id) {
                return $case;
            }
        }
        return null;
    }

    public static function validateMorphEnumByArray(string $enum, string $key, array $data): BackedEnum|UnitEnum
    {
        $entityType = (!empty($data[$key])) ? $data[$key] : null;
        if (empty($entityType)) {
            throw new \Exception("$key is required to create an order item");
        }

        $productType = EnumHelpers::getEnumCaseById($enum, $entityType);
        if (!$productType) {
            throw new \Exception("Invalid $key provided");
        }
        return $productType;
    }
    public static function validateMorphEnumByType(string $enum, string $type): BackedEnum|UnitEnum
    {
        $productType = EnumHelpers::getEnumCaseById($enum, $type);
        if (!$productType) {
            throw new \Exception("Invalid $type provided");
        }
        return $productType;
    }
}