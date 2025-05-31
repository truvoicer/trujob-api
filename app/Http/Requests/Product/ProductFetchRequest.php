
<?php

namespace App\Http\Requests\Product;

use App\Enums\Product\ProductFetchProperty;
use App\Helpers\Tools\UtilHelpers;
use App\Models\Category;
use App\Models\ProductType;
use App\Models\User;
use App\Rules\StringArrayExists;
use Illuminate\Foundation\Http\FormRequest;

class ProductFetchRequest extends FormRequest
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
            ProductFetchProperty::QUERY->value => [
                'sometimes',
                'string',
            ],
            ProductFetchProperty::TITLE->value => [
                'sometimes',
                'string',
                'max:255',
            ],
            ProductFetchProperty::DESCRIPTION->value => [
                'sometimes',
                'string',
            ],
            ProductFetchProperty::PRICE->value => [
                'sometimes',
                'integer',
            ],
            ProductFetchProperty::TYPE->value => [
                'sometimes',
                new StringArrayExists(
                    new ProductType(),
                    [
                        ['name' => 'id', 'type' => 'integer'],
                        ['name' => 'name']
                    ]
                ),
            ],
            ProductFetchProperty::CATEGORIES->value => [
                'sometimes',
                new StringArrayExists(
                    new Category(),
                    [
                        ['name' => 'id', 'type' => 'integer'],
                        ['name' => 'name']
                    ]
                ),
            ],
            ProductFetchProperty::USER->value => [
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
            // ProductFetchProperty::IMAGES->value => [
            //     'sometimes',
            //     'array',
            // ],
            ProductFetchProperty::VIEWS->value => [
                'sometimes',
                'integer',
            ],
            ProductFetchProperty::STATUS->value => [
                'sometimes',
                'string',
            ],
            ProductFetchProperty::LOCATION->value => [
                'sometimes',
                'string',
            ],
            ProductFetchProperty::LATITUDE->value => [
                'sometimes',
                'integer',
            ],
            ProductFetchProperty::LONGITUDE->value => [
                'sometimes',
                'integer',
            ],
            ProductFetchProperty::COUNTRY->value => [
                'sometimes',
                'string',
            ],
            ProductFetchProperty::CITY->value => [
                'sometimes',
                'string',
            ],
            ProductFetchProperty::PROXIMITY->value => [
                'sometimes',
                'integer',
            ],
        ];
    }
}
