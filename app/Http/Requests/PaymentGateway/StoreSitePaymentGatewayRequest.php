<?php

namespace App\Http\Requests\PaymentGateway;

use App\Enums\Payment\PaymentGatewayEnvironment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSitePaymentGatewayRequest extends FormRequest
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
            'is_active' => [
                'sometimes',
                'boolean'
            ],
            'is_default' => [
                'sometimes',
                'boolean'
            ],
            'environment' => [
                'required',
                'string',
                Rule::enum(PaymentGatewayEnvironment::class)
            ],
            'settings' => [
                'sometimes',
                'array'
            ],
        ];
    }
}
