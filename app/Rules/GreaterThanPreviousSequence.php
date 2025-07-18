<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class GreaterThanPreviousSequence implements ValidationRule
{
    protected $data;
    protected $index;
    protected $field;

    public function __construct(array $data, int $index, string $field)
    {
        $this->data = $data;
        $this->index = $index;
        $this->field = $field;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // If it's the first item, there's no previous item to compare against.
        if ($this->index === 0) {
            return;
        }

        // Get the sequence of the previous item
        $previousIndex = $this->index - 1;
        $previousItemSequence = data_get($this->data, "{$previousIndex}.{$this->field}");

        // Ensure both current and previous values are integers for comparison
        if (!is_int($value) || !is_int($previousItemSequence)) {
            $fail("The :attribute must be an integer and the previous item's sequence must also be an integer for comparison.");
            return;
        }

        if ($value <= $previousItemSequence) {
            $fail("The :attribute must be greater than the sequence of the previous item.");
        }
    }
}
