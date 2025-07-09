<?php

namespace App\Http\Requests\PaymentGateway;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentGatewayRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:255'
            ],
            'description' => [
                'required',
                'string',
                'max:255'
            ],
            'icon' => [
                'required',
                'string',
                'max:255'
            ],
            'is_default' => [
                'required',
                'boolean'
            ],
            'is_active' => [
                'required',
                'boolean'
            ],
            'settings' => [
                'sometimes',
                'array'
            ],
            'required_fields' => [
                'sometimes',
                'array'
            ],
            'required_fields.*.name' => [
                'required',
                'string',
                'max:255'
            ],
            'required_fields.*.label' => [
                'required',
                'string',
                'max:255'
            ],
            'required_fields.*.description' => [
                'sometimes',
                'string',
                'nullable',
                'max:255'
            ],
            'required_fields.*.type' => [
                'required',
                'string',
                'in:string,number,boolean'
            ],
        ];
    }
}
