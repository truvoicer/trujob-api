<?php

namespace App\Http\Requests\Listing;

use Illuminate\Foundation\Http\FormRequest;

class StoreListingRequest extends FormRequest
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
            'slug' => 'nullable',
            'title' => 'nullable',
            'description' => 'nullable',
            'allow_offers' => 'nullable|boolean',
            'type' => 'nullable',
            'size' => 'nullable',
            'price' => 'nullable',
        ];
    }
}
