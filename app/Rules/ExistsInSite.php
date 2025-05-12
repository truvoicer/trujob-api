<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;

class ExistsInSite implements ValidationRule
{
    public function __construct(
        protected Model $model,
        protected string $relation,
        protected int $siteId,
        protected string $message = 'The :attribute does not exist in the site.',
        private string $column = 'id',
    ) {
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $menuItemExists = $this->model::where('id', $value)
        ->whereRelation($this->relation, $this->column, $this->siteId)
        ->exists();
    if (! $menuItemExists) {
         $fail(sprintf($this->message, $value));
    }
    }
}
