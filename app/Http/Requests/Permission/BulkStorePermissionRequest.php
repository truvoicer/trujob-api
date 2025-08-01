<?php

namespace App\Http\Requests\Permission;

use Illuminate\Foundation\Http\FormRequest;

class BulkStorePermissionRequest extends FormRequest
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
            'permissions' => [
                'required',
                'array',
            ],
            'permissions.*.name' => [
                'required',
                'string',
                'max:255',
                'unique:permissions,name'
            ],
            'permissions.*.label' => [
                'required',
                'string',
                'max:255'
            ],
        ];
    }
}
