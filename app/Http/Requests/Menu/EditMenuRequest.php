<?php

namespace App\Http\Requests\Menu;

use App\Helpers\Tools\ValidationHelpers;
use Illuminate\Foundation\Http\FormRequest;

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
        $menuItemsRules = ValidationHelpers::nestedValidation('menu_items', (new CreateMenuItemRequest())->rules());
        unset($menuItemsRules['menu_items.*.menu_id']);
        return [
            'name' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'menu_items' => [
                'sometimes',
                'array',
            ],
            ...$menuItemsRules
        ];
    }
}
