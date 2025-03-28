<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;

class IdOrSlugExists implements ValidationRule
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
        } else {
            $this->validateName($attribute, $value, $fail);
        }
    }

    public function validateId(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->model->where('id', $value)->exists()) {
            $fail("The $attribute does not exist.");
        }
    }

    public function validateName(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->model->where('name', $value)->exists()) {
            $fail("The $attribute does not exist.");
        }
    }
}
