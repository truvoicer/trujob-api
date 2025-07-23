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
            'regions.*.code' => [
                'nullable',
                'string',
                'max:20',
                'unique:regions,code,NULL,id,country_id,' . $this->input('regions.*.country_id')
            ],
            'regions.*.is_active' => [
                'boolean'
            ],
        ];
    }
}
