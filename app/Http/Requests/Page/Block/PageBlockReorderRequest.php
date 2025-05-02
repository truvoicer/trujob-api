<?php

namespace App\Http\Requests\Page\Block;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PageBlockReorderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'direction' => [
                'sometimes',
                Rule::in(['up', 'down']),
            ],
            'order' => [
                'sometimes',
                'integer',
            ],
        ];
    }
}
