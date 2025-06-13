<?php

namespace App\Http\Requests\Product\Review;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductReviewRequest extends FormRequest
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
            'product_id' => ['sometimes', 'integer', 'exists:products,id'],
            'user_id' => ['sometimes', 'integer', 'exists:users,id'],
            'rating' => ['sometimes', 'integer', 'min:1', 'max:5'],
            'review' => ['sometimes', 'nullable', 'string', 'max:1000'],
        ];
    }
}
