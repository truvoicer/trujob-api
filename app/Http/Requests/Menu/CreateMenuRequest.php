<?php

namespace App\Http\Requests\Menu;

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
        return [
            'site_id' => [
                'required',
                'integer',
                Rule::exists('sites', 'id')
            ],
            'menu_item_id' => [
                'sometimes',
                'integer',
                Rule::exists('menu_items', 'id'),
            ],
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
            'menu_items' => [
                'sometimes',
                'array',
            ],
            'menu_items.*.menus' => [
                'sometimes',
                'array',
            ],
            'menu_items.*.menus.*' => [
                'required',
                'integer',
                Rule::exists('menus', 'id')->where(function ($query) {
                    return $query->where('site_id', request()->user()?->site?->id);
                }),
            ],
            'menu_items.*.roles' => [
                'sometimes',
                'array',
            ],
            'menu_items.*.roles.*' => [
                'required',
                'integer',
                Rule::exists('roles', 'id'),
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
