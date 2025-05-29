<?php

namespace App\Http\Requests\Order\Discount;

use Illuminate\Foundation\Http\FormRequest;

class StoreBulkOrderDiscountRequest extends FormRequest
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
            'ids' => [
                'required',
                'array'
            ],
            'ids.*' => ['exists:discounts,id'],
        ];
    }
}
