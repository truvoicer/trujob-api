<?php

namespace App\Rules;

use App\Enums\Order\OrderItemType;
use App\Models\Listing;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class OrderItemEntityType implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $requestEntityId = request()->input('entity_id');
        if (empty($requestEntityId) || !is_int($requestEntityId)) {
            $fail("The entity_id must be an integer.");
            return; 
        }
        switch ($value) {
            case OrderItemType::LISTING->value:
                if (!Listing::where('id', $requestEntityId)->exists()) {
                    $fail("The $attribute.entity_id does not exist for the specified entity type.");
                }
                break;
            default:
                $fail("The $attribute.entity_type is not supported.");
                break;
        }
    }

}
