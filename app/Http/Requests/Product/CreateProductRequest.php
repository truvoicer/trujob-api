<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

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
                'exists:product_types,id',
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
            'product_types' => [
                'nullable',
                'array',
            ],
            'product_types.*' => [
                'nullable',
                'integer',
                'exists:product_types,id',
            ],
            'media' => [
                'nullable',
                'array',
            ],
        ];
    }
}
