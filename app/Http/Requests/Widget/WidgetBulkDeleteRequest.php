<?php

namespace App\Http\Requests\Widget;

use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WidgetBulkDeleteRequest extends FormRequest
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
            'ids' => [
                'required',
                'array',
                'min:1'
            ],
            'ids.*' => [
                'required',
                'integer',
                Rule::exists('widgets', 'id')
                    ->where(function (Builder $query) {
                        return $query->where('site_id', request()->user()?->id);
                    })
            ]
        ];
    }
}
