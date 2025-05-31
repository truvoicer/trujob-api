<?php

namespace App\Rules;

use App\Enums\Order\OrderItemType;
use App\Models\Product;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class OrderItem implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_array($value)) {
            $fail("The $attribute must be an array.");
            return;
        }
        if (!isset($value['price_id']) || !is_int($value['price_id'])) {
            $fail("The $attribute.price_id must be an integer.");
            return;
        }
        if (!isset($value['payment_gateway_id']) || !is_int($value['payment_gateway_id'])) {
            $fail("The $attribute.payment_gateway_id must be an integer.");
            return;
        }
        if (!isset($value['quantity']) || !is_int($value['quantity']) || $value['quantity'] < 1) {
            $fail("The $attribute.quantity must be an integer and at least 1.");
            return;
        }
        if (!isset($value['entity_type']) || !in_array($value['entity_type'], ['product'])) {
            $fail("The $attribute.entity_type must be a valid entity type.");
            return;
        }
        if (!isset($value['entity_id']) || !is_int($value['entity_id'])) {
            $fail("The $attribute.entity_id must be an integer.");
            return; 
        }
        switch ($value['entity_type']) {
            case OrderItemType::PRODUCT->value:
                if (!Product::where('id', $value['entity_id'])->exists()) {
                    $fail("The $attribute.entity_id does not exist for the specified entity type.");
                }
                break;
            default:
                $fail("The $attribute.entity_type is not supported.");
                break;
        }
    }

}
