<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;

class StringArrayExists implements ValidationRule
{

    public function __construct(
        private Model $model,
        private array $columns,
        private ?string $delimiter = ','
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
        if (!is_string($value)) {
            $fail("The $attribute must be a string.");
        }
        $split = explode(',', $value);
        if (count($split) === 0) {
            $fail("The $attribute must be a comma separated list of ids.");
        }
        $pass = false;
        foreach ($split as $id) {
            if ($pass) {
                continue;
            }
            foreach ($this->columns as $column) {
                switch ((!empty($column['type']))? $column['type'] : null) {
                    case 'integer':
                        if ($this->model::where($column['name'], (int)$id)->exists()) {
                           $pass = true;
                           continue;
                        }
                        break;
                    default:
                        if ($this->model::where($column['name'], $id)->exists()) {
                            $pass = true;
                            continue;
                        }
                        break;
                }
            }
        }
        if (!$pass) {
            $fail(
                sprintf(
                    "The $attribute must be a valid %s.",
                    implode(' or ', array_column($this->columns, 'name'))
                )
            );
        }
    }
}
