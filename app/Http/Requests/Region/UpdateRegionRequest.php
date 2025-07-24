<?php
namespace App\Http\Requests\Region;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRegionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
             'country_id' => [
                'sometimes',
                'exists:countries,id'
            ],
            'name' => [
                'sometimes',
                'string',
                'max:100'
            ],
            'admin_name' => [
                'sometimes',
                'string',
                'max:100'
            ],
            'is_active' => [
                'sometimes',
                'boolean'
            ],
            'toponym_name' => [
                'sometimes',
                'string',
                'max:100'
            ],
            'category' => [
                'sometimes',
                'string',
                'max:50'
            ],
            'description' => [
                'sometimes',
                'string',
                'max:255'
            ],
            'lng' => [
                'sometimes',
                'integer',
                'min:-180',
                'max:180'
            ],
            'lat' => [
                'sometimes',
                'integer',
                'min:-90',
                'max:90'
            ],
            'population' => [
                'sometimes',
                'integer',
                'min:0'
            ],
        ];
    }
}