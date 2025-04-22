<?php

namespace App\Http\Requests\Menu;

use App\Helpers\Tools\ValidationHelpers;
use App\Models\Role;
use App\Rules\IdOrNameExists;
use App\Rules\StringOrIntger;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateMenuRequest extends FormRequest
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

        $menuItemRules = ValidationHelpers::nestedValidationRules(
            (new CreateMenuItemRequest())->rules(),
            'menu_items.*'
        );

        $menuItemRules['menu_items.*.roles'] = [
            'sometimes',
            'array',
        ];
        $menuItemRules['menu_items.*.roles.*'] = [
            'required',
            'integer',
            Rule::exists('roles', 'id'),
        ];
        $menuItemRules['menu_items.*.menus'] = [
            'sometimes',
            'array',
        ];
        $menuItemRules['menu_items.*.menus.*'] = [
            'required',
            'integer',
            Rule::exists('menus', 'id')->where(function ($query) {
                return $query->where('site_id', request()->user()?->site?->id);
            }),
        ];
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('menus', 'name')
            ],
            'ul_class' => [
                'sometimes',
                'string',
                'max:255',
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
            'menu_items' => [
                'sometimes',
                'array',
            ],
            ...$menuItemRules
        ];
    }
}
