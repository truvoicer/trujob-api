<?php

namespace App\Http\Requests\Listing;

use App\Enums\Listing\ListingFetchProperty;
use App\Helpers\Tools\UtilHelpers;
use App\Models\Category;
use App\Models\ListingType;
use App\Models\User;
use App\Rules\StringArrayExists;
use Illuminate\Foundation\Http\FormRequest;

class ListingFetchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            ListingFetchProperty::QUERY->value => [
                'sometimes',
                'string',
            ],
            ListingFetchProperty::TITLE->value => [
                'sometimes',
                'string',
                'max:255',
            ],
            ListingFetchProperty::DESCRIPTION->value => [
                'sometimes',
                'string',
            ],
            ListingFetchProperty::PRICE->value => [
                'sometimes',
                'integer',
            ],
            ListingFetchProperty::TYPE->value => [
                'sometimes',
                new StringArrayExists(
                    new ListingType(),
                    [
                        ['name' => 'id', 'type' => 'integer'],
                        ['name' => 'name']
                    ]
                ),
            ],
            ListingFetchProperty::CATEGORIES->value => [
                'sometimes',
                new StringArrayExists(
                    new Category(),
                    [
                        ['name' => 'id', 'type' => 'integer'],
                        ['name' => 'name']
                    ]
                ),
            ],
            ListingFetchProperty::USER->value => [
                'sometimes',
                new StringArrayExists(
                    new User(),
                    [
                        ['name' => 'id', 'type' => 'integer'],
                        ['name' => 'username'],
                        ['name' => 'email']
                    ]
                ),
            ],
            // ListingFetchProperty::IMAGES->value => [
            //     'sometimes',
            //     'array',
            // ],
            ListingFetchProperty::VIEWS->value => [
                'sometimes',
                'integer',
            ],
            ListingFetchProperty::STATUS->value => [
                'sometimes',
                'string',
            ],
            ListingFetchProperty::LOCATION->value => [
                'sometimes',
                'string',
            ],
            ListingFetchProperty::LATITUDE->value => [
                'sometimes',
                'integer',
            ],
            ListingFetchProperty::LONGITUDE->value => [
                'sometimes',
                'integer',
            ],
            ListingFetchProperty::COUNTRY->value => [
                'sometimes',
                'string',
            ],
            ListingFetchProperty::CITY->value => [
                'sometimes',
                'string',
            ],
            ListingFetchProperty::PROXIMITY->value => [
                'sometimes',
                'integer',
            ],
        ];
    }
}
