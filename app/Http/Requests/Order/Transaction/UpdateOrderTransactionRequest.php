<?php

namespace App\Http\Requests\Order\Transaction;

use App\Enums\Order\OrderStatus;
use App\Enums\Transaction\TransactionPaymentStatus;
use App\Enums\Transaction\TransactionStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'amount' => [
                'sometimes',
                'numeric',
                'min:0'
            ],
            'status' => [
                'sometimes',
                'string',
                Rule::enum(TransactionStatus::class)
            ],
            'payment_status' => [
                'sometimes',
                'string',
                Rule::enum(TransactionPaymentStatus::class)
            ],
            'order_data' => [
                'sometimes',
                'array'
            ],
            'transaction_data' => [
                'sometimes',
                'array'
            ],
        ];
    }
}
