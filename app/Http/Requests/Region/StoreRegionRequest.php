<?php

namespace App\Http\Requests\Region;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
             'country_id' => [
                'required',
                'exists:countries,id'
            ],
            'name' => [
                'required',
                'string',
                'max:100'
            ],
            'admin_name' => [
                'required',
                'string',
                'max:100'
            ],
            'is_active' => [
                'required',
                'boolean'
            ],
            'toponym_name' => [
                'required',
                'string',
                'max:100'
            ],
            'category' => [
                'required',
                'string',
                'max:50'
            ],
            'description' => [
                'required',
                'string',
                'max:255'
            ],
            'lng' => [
                'required',
                'numeric',
                'min:-180',
                'max:180'
            ],
            'lat' => [
                'required',
                'numeric',
                'min:-90',
                'max:90'
            ],
            'population' => [
                'required',
                'integer',
                'min:0'
            ],
        ];
    }
}