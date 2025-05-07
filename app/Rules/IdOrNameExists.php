<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;

class IdOrNameExists implements ValidationRule
{
    public function __construct(
        private Model $model,
    )
    {
        //
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_numeric($value)) {
            $this->validateId($attribute, $value, $fail);
        } else if(is_string($value)) {
            $this->validateSlug($attribute, $value, $fail);
        } else {
            $fail(sprintf(
                "The $attribute must be a string or an integer. | value: %s",
                json_encode($value)
            ));
        }
    }

    public function validateId(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->model->where('id', $value)->exists()) {
            $fail("The $attribute does not exist. | id: $value");
        }
    }

    public function validateSlug(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->model->where('name', $value)->exists()) {
            $fail("The $attribute does not exist. | name: $value");
        }
    }
}
