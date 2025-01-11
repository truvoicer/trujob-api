<?php

namespace App\Helpers\Tools;

class ValidationHelpers
{
    public static function nestedValidation(string $name, array $rules): array
    {
        $nestedRules = [];
        foreach ($rules as $key => $rule) {
            $nestedRules["$name.*.$key"] = $rule;
        }
        return $nestedRules;
    }
}
