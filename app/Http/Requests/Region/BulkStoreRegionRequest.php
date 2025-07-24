<?php

namespace App\Http\Requests\Region;

use Illuminate\Foundation\Http\FormRequest;

class BulkStoreRegionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'regions' => [
                'required',
                'array'
            ],
            'regions.*.country_id' => [
                'required',
                'exists:countries,id'
            ],
            'regions.*.name' => [
                'required',
                'string',
                'max:100'
            ],
            'regions.*.admin_name' => [
                'required',
                'string',
                'max:100'
            ],
            'regions.*.is_active' => [
                'required',
                'boolean'
            ],
            'regions.*.toponym_name' => [
                'required',
                'string',
                'max:100'
            ],
            'regions.*.category' => [
                'required',
                'string',
                'max:50'
            ],
            'regions.*.description' => [
                'required',
                'string',
                'max:255'
            ],
            'regions.*.lng' => [
                'required',
                'numeric    ',
                'min:-180',
                'max:180'
            ],
            'regions.*.lat' => [
                'required',
                'numeric',
                'min:-90',
                'max:90'
            ],
            'regions.*.population' => [
                'required',
                'integer',
                'min:0'
            ],
        ];
    }
}
