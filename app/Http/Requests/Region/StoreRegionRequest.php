<?php
// app/Http/Requests/StoreRegionRequest.php

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
            'country_id' => 'required|exists:countries,id',
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:20|unique:regions,code,NULL,id,country_id,'.$this->country_id,
            'is_active' => 'boolean',
        ];
    }
}