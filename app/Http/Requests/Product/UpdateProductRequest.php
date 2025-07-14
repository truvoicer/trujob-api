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
            'active' => [
                'sometimes',
                'boolean',
            ],
            'sku' => [
                'sometimes',
                'string',
                'max:255',
                'unique:products,sku',
            ],
            'has_weight' => ['sometimes', 'boolean'],
            'has_height' => ['sometimes', 'boolean'],
            'has_width' => ['sometimes', 'boolean'],
            'has_depth' => ['sometimes', 'boolean'],
            'weight_unit' => [
                'sometimes',
                Rule::enum(ProductWeightUnit::class)
            ],
            'height_unit' => [
                'sometimes',
                Rule::enum(ProductUnit::class)
            ],
            'width_unit' => [
                'sometimes',
                Rule::enum(ProductUnit::class)
            ],
            'depth_unit' => [
                'sometimes',
                Rule::enum(ProductUnit::class)
            ],
            'weight' => [
                'sometimes',
                'numeric',
                'min:0'
            ],
            'height' => [
                'sometimes',
                'numeric',
                'min:0'
            ],
            'width' => [
                'sometimes',
                'numeric',
                'min:0'
            ],
            'depth' => [
                'sometimes',
                'numeric',
                'min:0'
            ],
        ];
    }
}
