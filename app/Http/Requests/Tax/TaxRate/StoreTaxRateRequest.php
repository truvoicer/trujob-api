<?php

namespace App\Http\Requests\Tax\TaxRate;

use App\Enums\Order\Tax\TaxRateAbleType;
use App\Enums\Order\Tax\TaxRateAmountType;
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
            'amount_type' => ['required', Rule::enum((TaxRateAmountType::class))],
            'amount' => [
                'required_if:amount_type,fixed',
                'nullable',
                'numeric',
                'min:0'
            ],
            'rate' => [
                'required_if:amount_type,percentage',
                'numeric',
                'between:0,100'
            ],
            'currency_id' => ['required', 'integer', 'exists:currencies,id'],
            'has_region' => ['required', 'boolean'],
            'region_id' => [
                'required_if:has_region,true',
                'integer',
                'exists:regions,id'
            ],
            'is_default' => ['required', 'boolean'],
            'scope' => ['required', Rule::enum(TaxScope::class)],
            'is_active' => ['required', 'boolean'],
            'tax_rateables' => [
                'sometimes',
                'array'
            ],
            'tax_rateables.*.id' => [
                'required',
                'integer',
            ],
            'tax_rateables.*.type' => [
                'required',
                Rule::enum(TaxRateAbleType::class)
            ],
        ];
    }
}
