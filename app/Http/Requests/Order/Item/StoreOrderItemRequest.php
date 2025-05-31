<?php

namespace App\Http\Requests\Order\Item;

use App\Enums\Order\OrderItemType;
use App\Rules\OrderItemEntityType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderItemRequest extends FormRequest
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
            'price_id' => ['required', 'integer', 'exists:prices,id'],
            'payment_gateway_id' => ['required', 'integer', 'exists:payment_gateways,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'entity_type' => ['required', Rule::enum(OrderItemType::class), new OrderItemEntityType,],
            'entity_id' => ['required', 'integer', 'exists:products,id'],
        ];
    }
}
