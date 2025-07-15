<?php

namespace App\Http\Requests\Order\PaymentGateway\PayPal\Capture;

use Illuminate\Foundation\Http\FormRequest;

class StorePayPalOrderCaptureRequest extends FormRequest
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
            'order_id' => ['required', 'string'],
            // 'payer_id' => ['required', 'string'],
        ];
    }
}
