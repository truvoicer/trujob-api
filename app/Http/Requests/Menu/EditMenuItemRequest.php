<?php

namespace App\Http\Requests\Menu;

use App\Enums\LinkTarget;
use App\Enums\MenuItemType;
use App\Models\MenuItem;
use App\Models\Role;
use App\Rules\ExistsInSite;
use App\Rules\IdOrNameExists;
use App\Rules\StringOrIntger;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;

class EditMenuItemRequest extends FormRequest
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
                'required_if:type,' . MenuItemType::PAGE->value,
                'integer',
                Rule::exists('pages', 'id')
                    ->where(function ($query) {
                        return $query->where('site_id', request()->user()?->site?->id);
                    }),
            ],
            'active' => [
                'sometimes',
                'boolean',
            ],
            'id' => [
                'sometimes',
                'integer',
                new ExistsInSite(
                    new MenuItem(),
                    'parentMenus.site',
                    request()->user()?->site?->id,
                    'The menu item with id %s does not exist.'
                ),
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
                'sometimes',
                Rule::enum(MenuItemType::class),
            ],
            'url' => [
                'required_if:type,' . MenuItemType::URL->value,
                'string',
                'max:255',
            ],
            'target' => [
                'sometimes',
                'string',
                Rule::enum(LinkTarget::class),
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
                'nullable',
                'max:255',
            ],
            'a_class' => [
                'sometimes',
                'string',
                'nullable',
                'max:255',
            ],
            'menus' => [
                'sometimes',
                'array',
            ],
            'menus.*' => [
                'required',
                'integer',
                Rule::exists('menus', 'id')->where(function ($query) {
                    return $query->where('site_id', request()->user()?->site?->id);
                }),
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
