<?php

namespace App\Http\Requests\Page;

use App\Enums\Block\BlockType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BatchDeletePageBlockRequest extends FormRequest
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
            ],
            'ids.*' => [
                'required',
                'integer',
                Rule::exists('page_blocks', 'id')->where(function ($query) {
                    $query->where('page_id', $this->route('page')->id);
                }),
            ],
        ];
    }
}
