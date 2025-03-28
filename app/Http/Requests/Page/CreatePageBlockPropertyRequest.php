<?php

namespace App\Http\Requests\Page;

use App\Enums\Block\PageBlockType;
use App\Enums\Listing\ListingBlockSidebarWidget;
use App\Enums\Pagination\PaginationScrollType;
use App\Enums\Pagination\PaginationType;
use App\Helpers\Tools\ValidationHelpers;
use App\Http\Requests\Listing\ListingFetchRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatePageBlockPropertyRequest extends FormRequest
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
    public function rules(?string $type = null): array
    {
        return [
            'properties' => [
                'sometimes',
                'array',
            ],
            ...ValidationHelpers::buildRequestPropertyRules($type),
            'title' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'subtitle' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'pagination' => [
                'sometimes',
                'boolean',
            ],
            'pagination_type' => [
                'sometimes',
                Rule::enum(PaginationType::class),
            ],
            'pagination_scroll_type' => [
                'sometimes',
                Rule::enum(PaginationScrollType::class),
            ],
            'has_sidebar' => [
                'sometimes',
                'boolean',
            ],
            'sidebar_widgets' => [
                'sometimes',
                'array',
            ],
            'sidebar_widgets.*.type' => [
                'required_if:type,' . PageBlockType::LISTINGS_GRID->value,
                Rule::enum(ListingBlockSidebarWidget::class)
            ],
            'sidebar_widgets.*.title' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'sidebar_widgets.*.has_container' => [
                'required',
                'boolean',
            ],
        ];
    }
}
