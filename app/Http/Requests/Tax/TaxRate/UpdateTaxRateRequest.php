<?php

namespace App\Http\Requests\Tax\TaxRate;

use App\Enums\Order\Tax\TaxRateType;
use App\Enums\Order\Tax\TaxScope;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaxRateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {

        return [
            'name' => ['sometimes', 'string', 'max:50'],
            'type' => ['sometimes', Rule::enum((TaxRateType::class))],
            'amount' => [
                'required_if:fixed_rate,true',
                'nullable', 
                'numeric', 
                'min:0'
            ],
            'rate' => [
                'required_if:fixed_rate,false',
                'numeric', 
                'between:0,100'
            ],
            'country_id' => ['sometimes', 'integer', 'exists:countries,id'],
            'currency_id' => ['sometimes', 'integer', 'exists:currencies,id'],
            'has_region' => ['sometimes', 'boolean'],
            'region_id' => ['sometimes', 'integer', 'exists:regions,id'],
            'is_default' => ['sometimes', 'boolean'],
            'scope' => ['sometimes', Rule::enum(TaxScope::class)],
            'is_active' => ['sometimes', 'boolean'],
            'fixed_rate' => ['sometimes', 'boolean'],
        ];
    }
}
