<?php

namespace App\Http\Requests\Page;

use App\Enums\BlockType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditPageBlockRequest extends FormRequest
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
            'type' => [
                'sometimes',
                Rule::enum(BlockType::class)
            ],
            'order' => [
                'sometimes',
                'integer',
            ],
            ...(new CreatePageBlockPropertyRequest())->rules(),
        ];
    }
}
