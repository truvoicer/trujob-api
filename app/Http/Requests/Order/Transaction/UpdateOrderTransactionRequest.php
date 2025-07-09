<?php

namespace App\Http\Requests\Order\Transaction;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderTransactionRequest extends FormRequest
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
            'payment_gateway_id' => [
                'sometimes',
                'integer',
                'exists:payment_gateways,id'
            ],
        ];
    }
}
