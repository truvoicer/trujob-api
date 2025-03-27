<?php

namespace App\Http\Requests\Menu;

use App\Enums\MenuItemType;
use App\Models\Role;
use App\Rules\IdOrNameExists;
use App\Rules\StringOrIntger;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditMenuRequest extends FormRequest
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
            'site_id' => [
                'sometimes',
                'integer',
                Rule::exists('sites', 'id')
            ],
            'menu_item_id' => [
                'sometimes',
                'integer',
                Rule::exists('menu_items', 'id'),
            ],
            'name' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'ul_class' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'menu_items' => [
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
