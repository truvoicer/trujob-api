<?php

namespace App\Http\Requests\Tax\TaxRate;

use App\Enums\Order\Tax\TaxRateType;
use App\Enums\Order\Tax\TaxScope;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaxRateRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:50'],
            'type' => ['required', Rule::enum((TaxRateType::class))],
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
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'region_id' => ['required', 'integer', 'exists:regions,id'],
            'is_default' => ['required', 'boolean'],
            'scope' => ['required', Rule::enum(TaxScope::class)],
            'is_active' => ['required', 'boolean'],
            'fixed_rate' => ['required', 'boolean'],
        ];
    }
}
