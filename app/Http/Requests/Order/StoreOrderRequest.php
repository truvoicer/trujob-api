<?php

namespace App\Http\Requests\Order;

use App\Enums\Order\OrderStatus;
use App\Enums\Price\PriceType;
use App\Rules\OrderItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
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
            'price_type' => [
                'required',
                'string',
                Rule::enum(PriceType::class),
            ],
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
            'items' => [
                'required',
                'array'
            ],
            'items.*' => [new OrderItem],
        ];
    }


    public function withValidator($validator)
    {
        $validator->sometimes('items', 'size:1', function ($input) {
            return $input->price_type === PriceType::SUBSCRIPTION->value;
        });
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'items.size' => sprintf(
                'The items array must contain exactly one item when the price type is %s.',
                PriceType::SUBSCRIPTION->value
            ),
        ];
    }
}
