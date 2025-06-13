<?php

namespace App\Http\Requests\Product\Review;

use Illuminate\Foundation\Http\FormRequest;

class BulkStoreProductReviewRequest extends FormRequest
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
            'reviews' => [
                'required',
                'array',
            ],
            'reviews.*.product_id' => [
                'required',
                'integer',
                'exists:products,id',
            ],
            'reviews.*.user_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],
            'reviews.*.rating' => [
                'required',
                'integer',
                'min:1',
                'max:5',
            ],
            'reviews.*.review' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }
}
