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
            'country_id' => 'sometimes|exists:countries,id',
            'name' => 'sometimes|string|max:100',
            'code' => 'nullable|string|max:20|unique:regions,code,'.$this->region->id.',id,country_id,'.$this->input('country_id', $this->region->country_id),
            'is_active' => 'boolean',
        ];
    }
}