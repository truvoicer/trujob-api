<?php

namespace App\Http\Requests\Menu;

use App\Enums\MenuItemType;
use App\Models\Role;
use App\Rules\IdOrNameExists;
use App\Rules\StringOrIntger;
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
            'label' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'name' => [
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
            'li_class' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'a_class' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'menus' => [
                'sometimes',
                'array',
            ],
            'roles' => [
                'sometimes',
                'array',
            ],
            'roles.*' => [
                'required',
                new StringOrIntger,
                new IdOrNameExists(new Role())
            ],
        ];
    }
}
