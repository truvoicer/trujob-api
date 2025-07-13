<?php

namespace App\Http\Requests\Product;

use App\Contracts\Product\Product;
use App\Enums\Product\ProductType;
use App\Enums\Product\ProductUnit;
use App\Enums\Product\ProductWeightUnit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateProductRequest extends FormRequest
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
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'required',
                'string',
                'max:1000',
            ],
            'active' => [
                'required',
                'boolean',
            ],
            'sku' => [
                'sometimes',
                'string',
                'max:255',
                'unique:products,sku',
            ],
            'allow_offers' => [
                'required',
                'boolean',
            ],
            'quantity' => [
                'required',
                'integer',
                'min:0',
            ],
            'type' => [
                'required',
                'integer',
                Rule::enum(ProductType::class),
            ],
            'has_weight' => ['sometimes', 'boolean'],
            'has_height' => ['sometimes', 'boolean'],
            'has_width' => ['sometimes', 'boolean'],
            'has_depth' => ['sometimes', 'boolean'],
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
            'depth_unit' => [
                'required_if:has_depth,true',
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
            'depth' => [
                'required_if:has_depth,true',
                'numeric',
                'min:0'
            ],
            'user' => [
                'required',
                'integer',
                'exists:users,id',
            ],
            'follows' => [
                'nullable',
                'array',
            ],
            'follows.*' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],
            'features' => [
                'nullable',
                'array',
            ],
            'features.*' => [
                'nullable',
                'integer',
                'exists:features,id',
            ],
            'reviews' => [
                'nullable',
                'array',
            ],
            'reviews.*.rating' => [
                'nullable',
                'integer',
                'between:1,5',
            ],
            'reviews.*.review' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'categories' => [
                'nullable',
                'array',
            ],
            'categories.*' => [
                'nullable',
                'integer',
                'exists:categories,id',
            ],
            'brands' => [
                'nullable',
                'array',
            ],
            'brands.*' => [
                'nullable',
                'integer',
                'exists:brands,id',
            ],
            'colors' => [
                'nullable',
                'array',
            ],
            'colors.*' => [
                'nullable',
                'integer',
                'exists:colors,id',
            ],
            'product_categories' => [
                'nullable',
                'array',
            ],
            'product_categories.*' => [
                'nullable',
                'integer',
                'exists:product_categories,id',
            ],
            'media' => [
                'nullable',
                'array',
            ],
        ];
    }
}
