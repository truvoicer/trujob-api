<?php

namespace App\Http\Requests\Page;

use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SitePageRequest extends FormRequest
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
            'permalink' => [
                'required',
                'string',
                'max:255',
                Rule::exists('pages', 'permalink')
                    ->where('site_id', $this->user()?->id)
            ]
        ];
    }
}
