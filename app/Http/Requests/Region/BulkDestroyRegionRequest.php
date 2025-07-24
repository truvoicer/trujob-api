<?php

namespace App\Http\Requests\Region;

use Illuminate\Foundation\Http\FormRequest;

class BulkDestroyRegionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'ids' => [
                'required',
                'array'
            ],
            'ids.*' => [
                'required',
                'integer',
                'exists:regions,id'
            ],
        ];
    }
}
