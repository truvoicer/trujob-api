<?php

namespace App\Http\Requests\Menu;

use App\Enums\MenuItemType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateMenuItemRequest extends FormRequest
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
            'menu_id' => [
                'integer',
                'required',
                Rule::exists('menus', 'id'),
            ],
            'page_id' => [
                'sometimes',
                'integer',
                'required_if:type,page',
                Rule::exists('pages', 'id'),
            ],
            'active' => [
                'sometimes',
                'boolean',
            ],
            'title' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'slug' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'type' => [
                'required',
                Rule::enum(MenuItemType::class),
            ],
            'url' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'target' => [
                'sometimes',
                'string',
                Rule::in(['_self', '_blank']),
            ],
            'icon' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'order' => [
                'sometimes',
                'integer',
            ],
        ];
    }
}
