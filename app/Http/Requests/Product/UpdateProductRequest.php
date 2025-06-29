<?php

namespace App\Http\Requests\Product;

use App\Enums\Product\ProductType;
use App\Enums\Product\ProductUnit;
use App\Enums\Product\ProductWeightUnit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
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
            'title' => 'nullable',
            'type' => [
                'sometimes',
                Rule::enum(ProductType::class),
            ],
            'description' => 'nullable',
            'allow_offers' => 'nullable|boolean',
            'has_weight' => ['sometimes', 'boolean'],
            'has_height' => ['sometimes', 'boolean'],
            'has_width' => ['sometimes', 'boolean'],
            'has_length' => ['sometimes', 'boolean'],
            'weight_unit' => [
                'required_if:has_weight,true',
                Rule::enum(ProductWeightUnit::class)
            ],
            'height_unit' => [
                'required_if:has_height,true',
                Rule::enum(ProductUnit::class)
            ],
            'width_unit' => [
                'required_if:has_width,true',
                Rule::enum(ProductUnit::class)
            ],
            'length_unit' => [
                'required_if:has_length,true',
                Rule::enum(ProductUnit::class)
            ],
            'weight' => [
                'required_if:has_weight,true',
                'numeric',
                'min:0'
            ],
            'height' => [
                'required_if:has_height,true',
                'numeric',
                'min:0'
            ],
            'width' => [
                'required_if:has_width,true',
                'numeric',
                'min:0'
            ],
            'length' => [
                'required_if:has_length,true',
                'numeric',
                'min:0'
            ],
        ];
    }
}
