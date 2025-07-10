<?php

namespace App\Http\Requests\Order;

use App\Enums\Order\OrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderRequest extends FormRequest
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
            'billing_address_id' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:addresses,id'
            ],
            'shipping_address_id' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:addresses,id'
            ],
            'status' => [
                'sometimes',
                'string',
                Rule::enum(OrderStatus::class),
            ],
        ];
    }
}
