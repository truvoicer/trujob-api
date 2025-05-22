<?php

namespace App\Http\Requests\PaymentGateway;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentGatewayRequest extends FormRequest
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
                'sometimes',
                'string',
                'max:255'
            ],
            'description' => [
                'sometimes',
                'string',
                'max:255'
            ],
            'icon' => [
                'sometimes',
                'string',
                'max:255'
            ],
            'is_default' => [
                'sometimes',
                'boolean'
            ],
            'is_active' => [
                'sometimes',
                'boolean'
            ],
            'settings' => [
                'sometimes',
                'array'
            ],
        ];
    }
}
