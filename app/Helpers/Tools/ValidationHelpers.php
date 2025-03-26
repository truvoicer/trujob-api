<?php

namespace App\Helpers\Tools;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Validator as ValidationValidator;

class ValidationHelpers
{
    public static function nestedValidationRules(array $rules, ?string $name = null): array
    {
        $nestedRules = [];
        foreach ($rules as $key => $rule) {
            if ($name) {
                $key = "$name.*.$key";
            } else {
                $key = "*.$key";
            }
            $nestedRules[$key] = $rule;
        }
        return $nestedRules;
    }

    public static function nestedValidation(array $data, array $ruleConfig, int $maxDepth, ?int $depth = 0): ValidationValidator|bool
    {
        if ($depth >= $maxDepth) {
            return true;
        }
        foreach ($ruleConfig as $key => $rules) {
            if (!isset($data[$key])) {
                continue;
            }
            $validator = Validator::make(
                $data[$key],
                self::nestedValidationRules($rules)
            );

            if ($validator->fails()) {
                return $validator;
            }

            if (is_array($data[$key])) {
                $validate = self::nestedValidation($data[$key], $ruleConfig, $depth + 1);
                
                if ($validate instanceof MessageBag) {
                    return $validate;
                }
            } else {
            }
        }
        return true;
    }
}
